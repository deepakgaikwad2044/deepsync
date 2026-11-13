<?php

namespace App\Services;

use App\Config\Database;
use PDO;
use RuntimeException;

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
