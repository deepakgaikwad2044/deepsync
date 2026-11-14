<?php

namespace App\Controllers;

use App\Config\Database;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthorizationService;
use InvalidArgumentException;
use PDO;
use Throwable;

class AdminRbacController
{
    private PDO $db;
    private AuthorizationService $authorization;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->authorization = new AuthorizationService($this->db);
    }

    /**
     * List users with their assigned roles.
     */
    public function users(): void
    {
        try {
            $stmt = $this->db->query("
                SELECT
                    u.id,
                    u.name,
                    u.email,
                    r.id AS role_id,
                    r.name AS role_name,
                    r.slug AS role_slug
                FROM users AS u
                LEFT JOIN role_user AS ru ON ru.user_id = u.id
                LEFT JOIN roles AS r ON r.id = ru.role_id
                ORDER BY u.id ASC, r.id ASC
            ");

            $users = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $userId = (int) $row['id'];

                if (!isset($users[$userId])) {
                    $users[$userId] = [
                        'id' => $userId,
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'roles' => [],
                    ];
                }

                if ($row['role_id'] !== null) {
                    $users[$userId]['roles'][] = [
                        'id' => (int) $row['role_id'],
                        'name' => $row['role_name'],
                        'slug' => $row['role_slug'],
                    ];
                }
            }

            Response::success(
                'Users retrieved successfully.',
                array_values($users)
            );
        } catch (Throwable $e) {
            error_log('RBAC users error: ' . $e->getMessage());
            Response::error('Unable to retrieve users.', 500);
        }
    }

    /**
     * List roles with their assigned permissions.
     */
    public function roles(): void
    {
        try {
            $stmt = $this->db->query("
                SELECT
                    r.id,
                    r.name,
                    r.slug,
                    r.description,
                    p.id AS permission_id,
                    p.name AS permission_name,
                    p.slug AS permission_slug
                FROM roles AS r
                LEFT JOIN permission_role AS pr ON pr.role_id = r.id
                LEFT JOIN permissions AS p ON p.id = pr.permission_id
                ORDER BY r.id ASC, p.id ASC
            ");

            $roles = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $roleId = (int) $row['id'];

                if (!isset($roles[$roleId])) {
                    $roles[$roleId] = [
                        'id' => $roleId,
                        'name' => $row['name'],
                        'slug' => $row['slug'],
                        'description' => $row['description'],
                        'permissions' => [],
                    ];
                }

                if ($row['permission_id'] !== null) {
                    $roles[$roleId]['permissions'][] = [
                        'id' => (int) $row['permission_id'],
                        'name' => $row['permission_name'],
                        'slug' => $row['permission_slug'],
                    ];
                }
            }

            Response::success(
                'Roles retrieved successfully.',
                array_values($roles)
            );
        } catch (Throwable $e) {
            error_log('RBAC roles error: ' . $e->getMessage());
            Response::error('Unable to retrieve roles.', 500);
        }
    }

    /**
     * List all available permissions.
     */
    public function permissions(): void
    {
        try {
            $stmt = $this->db->query("
                SELECT id, name, slug, description
                FROM permissions
                ORDER BY id ASC
            ");

            Response::success(
                'Permissions retrieved successfully.',
                $stmt->fetchAll(PDO::FETCH_ASSOC)
            );
        } catch (Throwable $e) {
            error_log('RBAC permissions error: ' . $e->getMessage());
            Response::error('Unable to retrieve permissions.', 500);
        }
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(): void
    {
        $input = Request::input();

        if (!is_array($input)) {
            Response::error('Invalid request payload.', 422);
        }

        $userId = $this->positiveId($input['user_id'] ?? null);
        $roleId = $this->positiveId($input['role_id'] ?? null);

        if ($userId === null || $roleId === null) {
            Response::error('Valid user_id and role_id are required.', 422);
        }

        try {
            $this->authorization->assignRole($userId, $roleId);

            Response::success('Role assigned successfully.');
        } catch (InvalidArgumentException $e) {
            Response::error('User or role not found.', 404);
        } catch (Throwable $e) {
            error_log('RBAC role assignment error: ' . $e->getMessage());
            Response::error('Unable to assign role.', 500);
        }
    }

    /**
     * Remove a role from a user.
     */
    public function removeRole(): void
    {
        $input = Request::input();

        if (!is_array($input)) {
            Response::error('Invalid request payload.', 422);
        }

        $userId = $this->positiveId($input['user_id'] ?? null);
        $roleId = $this->positiveId($input['role_id'] ?? null);

        if ($userId === null || $roleId === null) {
            Response::error('Valid user_id and role_id are required.', 422);
        }

        try {
            $this->authorization->removeRole($userId, $roleId);

            Response::success('Role removed successfully.');
        } catch (\DomainException $e) {
            Response::error('Cannot remove the last Admin role.', 409);
        } catch (InvalidArgumentException $e) {
            Response::error('User or role not found.', 404);
        } catch (Throwable $e) {
            error_log('RBAC role removal error: ' . $e->getMessage());
            Response::error('Unable to remove role.', 500);
        }
    }

    /**
     * Assign a permission to a role.
     */
    public function assignPermission(): void
    {
        $input = Request::input();

        if (!is_array($input)) {
            Response::error('Invalid request payload.', 422);
        }

        $roleId = $this->positiveId($input['role_id'] ?? null);
        $permissionId = $this->positiveId(
            $input['permission_id'] ?? null
        );

        if ($roleId === null || $permissionId === null) {
            Response::error(
                'Valid role_id and permission_id are required.',
                422
            );
        }

        try {
            $this->authorization->assignPermission(
                $roleId,
                $permissionId
            );

            Response::success('Permission assigned successfully.');
        } catch (InvalidArgumentException $e) {
            Response::error('Role or permission not found.', 404);
        } catch (Throwable $e) {
            error_log(
                'RBAC permission assignment error: ' . $e->getMessage()
            );
            Response::error('Unable to assign permission.', 500);
        }
    }

    /**
     * Remove a permission from a role.
     */
    public function removePermission(): void
    {
        $input = Request::input();

        if (!is_array($input)) {
            Response::error('Invalid request payload.', 422);
        }

        $roleId = $this->positiveId($input['role_id'] ?? null);
        $permissionId = $this->positiveId(
            $input['permission_id'] ?? null
        );

        if ($roleId === null || $permissionId === null) {
            Response::error(
                'Valid role_id and permission_id are required.',
                422
            );
        }

        try {
            $this->authorization->removePermission(
                $roleId,
                $permissionId
            );

            Response::success('Permission removed successfully.');
        } catch (InvalidArgumentException $e) {
            Response::error('Role or permission not found.', 404);
        } catch (Throwable $e) {
            error_log(
                'RBAC permission removal error: ' . $e->getMessage()
            );
            Response::error('Unable to remove permission.', 500);
        }
    }

    /**
     * Validate a positive integer ID.
     */
    private function positiveId(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value > 0 ? $value : null;
        }

        if (
            !is_string($value) ||
            !preg_match('/^[1-9][0-9]*$/D', $value)
        ) {
            return null;
        }

        $id = filter_var($value, FILTER_VALIDATE_INT);

        return $id !== false && $id > 0 ? $id : null;
    }
}
