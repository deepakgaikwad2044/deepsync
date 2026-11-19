<?php

namespace App\Services;

use App\Config\Database;
use PDO;
use PDOException;
use RuntimeException;
use InvalidArgumentException;

class AuthorizationService
{
    private PDO $connection;

    public function __construct(?PDO $connection = null)
    {
        $connection = $connection ?? Database::connect();

        if (!($connection instanceof PDO)) {
            throw new RuntimeException(
                'Authorization database connection is unavailable.'
            );
        }

        $this->connection = $connection;
    }

    /**
     * Get all roles assigned to a user.
     */
    public function rolesForUser(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }

        $sql = "
            SELECT r.id, r.name, r.slug, r.description
            FROM role_user AS ru
            INNER JOIN roles AS r ON r.id = ru.role_id
            WHERE ru.user_id = ?
            ORDER BY r.id ASC
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Assign a role to a user.
     * Repeated assignment is idempotent.
     */
    public function assignRole(int $userId, int $roleId): bool
    {
        $this->assertRecordExists('users', $userId, 'User');

        $ownsTransaction = !$this->connection->inTransaction();
        $savepoint = 'rbac_assign_role';

        if ($ownsTransaction) {
            $this->connection->beginTransaction();
        } else {
            $this->connection->exec("SAVEPOINT {$savepoint}");
        }

        try {
            // All role assignments and removals must lock this role row first.
            $roleStmt = $this->connection->prepare(
                "SELECT id FROM roles WHERE id = ? FOR UPDATE"
            );
            $roleStmt->execute([$roleId]);

            if (!$roleStmt->fetchColumn()) {
                throw new InvalidArgumentException('Role not found.');
            }

            if (!$this->assignmentExists(
                'role_user',
                'user_id',
                $userId,
                'role_id',
                $roleId
            )) {
                $stmt = $this->connection->prepare(
                    "INSERT INTO role_user (user_id, role_id) VALUES (?, ?)"
                );
                $stmt->execute([$userId, $roleId]);
            }

            if ($ownsTransaction) {
                $this->connection->commit();
            } else {
                $this->connection->exec("RELEASE SAVEPOINT {$savepoint}");
            }

            return true;
        } catch (\Throwable $e) {
            if ($ownsTransaction) {
                if ($this->connection->inTransaction()) {
                    $this->connection->rollBack();
                }
            } else {
                $this->connection->exec(
                    "ROLLBACK TO SAVEPOINT {$savepoint}"
                );
                $this->connection->exec(
                    "RELEASE SAVEPOINT {$savepoint}"
                );
            }

            throw $e;
        }
    }

    /**
     * Remove a role from a user.
     * Repeated removal is idempotent.
     */
    public function removeRole(int $userId, int $roleId): bool
    {
        $this->assertRecordExists('users', $userId, 'User');

        $ownsTransaction = !$this->connection->inTransaction();
        $savepoint = 'rbac_remove_role';

        if ($ownsTransaction) {
            $this->connection->beginTransaction();
        } else {
            $this->connection->exec("SAVEPOINT {$savepoint}");
        }

        try {
            // Shared lock protocol: assignment and removal serialize on this row.
            $roleStmt = $this->connection->prepare(
                "SELECT id, slug FROM roles WHERE id = ? FOR UPDATE"
            );
            $roleStmt->execute([$roleId]);
            $role = $roleStmt->fetch(PDO::FETCH_ASSOC);

            if (!$role) {
                throw new InvalidArgumentException('Role not found.');
            }

            $assignmentStmt = $this->connection->prepare(
                "SELECT user_id FROM role_user WHERE role_id = ? FOR UPDATE"
            );
            $assignmentStmt->execute([$roleId]);

            $assignedUserIds = array_map(
                static fn ($id): int => (int) $id,
                $assignmentStmt->fetchAll(PDO::FETCH_COLUMN)
            );

            $isAssigned = in_array($userId, $assignedUserIds, true);

            if (
                $isAssigned
                && $role['slug'] === 'admin'
                && count($assignedUserIds) <= 1
            ) {
                throw new \DomainException(
                    'The last Admin role assignment cannot be removed.'
                );
            }

            if ($isAssigned) {
                $deleteStmt = $this->connection->prepare(
                    "DELETE FROM role_user WHERE user_id = ? AND role_id = ?"
                );
                $deleteStmt->execute([$userId, $roleId]);
            }

            if ($ownsTransaction) {
                $this->connection->commit();
            } else {
                $this->connection->exec("RELEASE SAVEPOINT {$savepoint}");
            }

            return true;
        } catch (\Throwable $e) {
            if ($ownsTransaction) {
                if ($this->connection->inTransaction()) {
                    $this->connection->rollBack();
                }
            } else {
                $this->connection->exec(
                    "ROLLBACK TO SAVEPOINT {$savepoint}"
                );
                $this->connection->exec(
                    "RELEASE SAVEPOINT {$savepoint}"
                );
            }

            throw $e;
        }
    }

    /**
     * Assign a permission to a role.
     * Repeated assignment is idempotent.
     */
    public function assignPermission(
        int $roleId,
        int $permissionId
    ): bool {
        $this->assertRecordExists('roles', $roleId, 'Role');
        $this->assertRecordExists(
            'permissions',
            $permissionId,
            'Permission'
        );

        if ($this->assignmentExists(
            'permission_role',
            'role_id',
            $roleId,
            'permission_id',
            $permissionId
        )) {
            return true;
        }

        try {
            $stmt = $this->connection->prepare(
                "INSERT INTO permission_role (role_id, permission_id)
                 VALUES (?, ?)"
            );

            $stmt->execute([$roleId, $permissionId]);
        } catch (PDOException $e) {
            if ($this->assignmentExists(
                'permission_role',
                'role_id',
                $roleId,
                'permission_id',
                $permissionId
            )) {
                return true;
            }

            throw $e;
        }

        return true;
    }

    /**
     * Remove a permission from a role.
     * Repeated removal is idempotent.
     */
    public function removePermission(
        int $roleId,
        int $permissionId
    ): bool {
        $this->assertRecordExists('roles', $roleId, 'Role');
        $this->assertRecordExists(
            'permissions',
            $permissionId,
            'Permission'
        );

        $stmt = $this->connection->prepare(
            "DELETE FROM permission_role
             WHERE role_id = ? AND permission_id = ?"
        );

        $stmt->execute([$roleId, $permissionId]);

        return true;
    }

    /**
     * Check whether a user has a specific role.
     */
    public function hasRole(int $userId, string $roleSlug): bool
    {
        $roleSlug = trim($roleSlug);

        if ($userId <= 0 || $roleSlug === '') {
            return false;
        }

        $sql = "
            SELECT 1
            FROM role_user AS ru
            INNER JOIN roles AS r ON r.id = ru.role_id
            WHERE ru.user_id = ?
              AND r.slug = ?
            LIMIT 1
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$userId, $roleSlug]);

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Check whether a user has a specific permission.
     */
    public function hasPermission(
        int $userId,
        string $permissionSlug
    ): bool {
        $permissionSlug = trim($permissionSlug);

        if ($userId <= 0 || $permissionSlug === '') {
            return false;
        }

        $sql = "
            SELECT 1
            FROM role_user AS ru
            INNER JOIN permission_role AS pr
                ON pr.role_id = ru.role_id
            INNER JOIN permissions AS p
                ON p.id = pr.permission_id
            WHERE ru.user_id = ?
              AND p.slug = ?
            LIMIT 1
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$userId, $permissionSlug]);

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Check whether a user has at least one requested permission.
     */
    public function hasAnyPermission(
        int $userId,
        array $permissions
    ): bool {
        $permissions = $this->normalizePermissionSlugs($permissions);

        if ($userId <= 0 || $permissions === null) {
            return false;
        }

        $placeholders = implode(
            ', ',
            array_fill(0, count($permissions), '?')
        );

        $sql = "
            SELECT 1
            FROM role_user AS ru
            INNER JOIN permission_role AS pr
                ON pr.role_id = ru.role_id
            INNER JOIN permissions AS p
                ON p.id = pr.permission_id
            WHERE ru.user_id = ?
              AND p.slug IN ({$placeholders})
            LIMIT 1
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_merge([$userId], $permissions));

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Check whether a user has every requested permission.
     */
    public function hasAllPermissions(
        int $userId,
        array $permissions
    ): bool {
        $permissions = $this->normalizePermissionSlugs($permissions);

        if ($userId <= 0 || $permissions === null) {
            return false;
        }

        $placeholders = implode(
            ', ',
            array_fill(0, count($permissions), '?')
        );

        $sql = "
            SELECT COUNT(DISTINCT p.id)
            FROM role_user AS ru
            INNER JOIN permission_role AS pr
                ON pr.role_id = ru.role_id
            INNER JOIN permissions AS p
                ON p.id = pr.permission_id
            WHERE ru.user_id = ?
              AND p.slug IN ({$placeholders})
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array_merge([$userId], $permissions));

        return (int) $stmt->fetchColumn() === count($permissions);
    }

    /**
     * Verify that a referenced database record exists.
     */
    private function assertRecordExists(
        string $table,
        int $id,
        string $label
    ): void {
        $allowedTables = ['users', 'roles', 'permissions'];

        if (!in_array($table, $allowedTables, true)) {
            throw new RuntimeException('Invalid authorization table.');
        }

        if ($id <= 0) {
            throw new InvalidArgumentException(
                "{$label} ID must be a positive integer."
            );
        }

        $stmt = $this->connection->prepare(
            "SELECT 1 FROM {$table} WHERE id = ? LIMIT 1"
        );

        $stmt->execute([$id]);

        if ($stmt->fetchColumn() === false) {
            throw new InvalidArgumentException(
                "{$label} not found."
            );
        }
    }

    /**
     * Check whether an assignment already exists.
     */
    private function assignmentExists(
        string $table,
        string $firstColumn,
        int $firstId,
        string $secondColumn,
        int $secondId
    ): bool {
        $allowed = [
            'role_user' => ['user_id', 'role_id'],
            'permission_role' => ['role_id', 'permission_id'],
        ];

        if (
            !isset($allowed[$table]) ||
            !in_array($firstColumn, $allowed[$table], true) ||
            !in_array($secondColumn, $allowed[$table], true)
        ) {
            throw new RuntimeException(
                'Invalid authorization assignment.'
            );
        }

        $sql = "
            SELECT 1
            FROM {$table}
            WHERE {$firstColumn} = ?
              AND {$secondColumn} = ?
            LIMIT 1
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$firstId, $secondId]);

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Validate and normalize permission slugs.
     */
    private function normalizePermissionSlugs(
        array $permissions
    ): ?array {
        if (empty($permissions)) {
            return null;
        }

        $normalized = [];

        foreach ($permissions as $permission) {
            if (!is_string($permission)) {
                return null;
            }

            $permission = trim($permission);

            if ($permission === '') {
                return null;
            }

            $normalized[] = $permission;
        }

        return array_values(array_unique($normalized));
    }
}
