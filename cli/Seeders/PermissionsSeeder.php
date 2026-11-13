<?php

namespace CLI\Seeders;

use PDO;
use RuntimeException;
use App\Core\Console\CLI;

class PermissionsSeeder
{
    public static function run(PDO $pdo): void
    {
        $permissions = require __DIR__ . '/permissions_data.php';

        $roleNames = [
            'admin' => 'Admin',
            'editor' => 'Editor',
            'user' => 'User',
        ];

        if (!is_array($permissions) || $permissions === []) {
            throw new RuntimeException(
                'Invalid or empty permission data.'
            );
        }

        $seenNames = [];
        $seenSlugs = [];

        foreach ($permissions as $index => $permission) {
            if (
                !is_array($permission) ||
                !isset(
                    $permission['name'],
                    $permission['slug'],
                    $permission['description'],
                    $permission['roles']
                )
            ) {
                throw new RuntimeException(
                    "Invalid permission structure at entry {$index}."
                );
            }

            if (
                !is_string($permission['name']) ||
                trim($permission['name']) === '' ||
                !is_string($permission['slug']) ||
                trim($permission['slug']) === '' ||
                !is_string($permission['description'])
            ) {
                throw new RuntimeException(
                    "Invalid permission fields at entry {$index}."
                );
            }

            $name = $permission['name'];
            $slug = $permission['slug'];

            if (isset($seenNames[$name]) || isset($seenSlugs[$slug])) {
                throw new RuntimeException(
                    "Duplicate permission name or slug: {$slug}."
                );
            }

            $seenNames[$name] = true;
            $seenSlugs[$slug] = true;

            if (
                !is_array($permission['roles']) ||
                $permission['roles'] === []
            ) {
                throw new RuntimeException(
                    "Invalid role assignments for permission: {$slug}."
                );
            }

            $seenRoles = [];

            foreach ($permission['roles'] as $roleSlug) {
                if (
                    !is_string($roleSlug) ||
                    !isset($roleNames[$roleSlug])
                ) {
                    throw new RuntimeException(
                        "Unknown role in permission data: {$slug}."
                    );
                }

                if (isset($seenRoles[$roleSlug])) {
                    throw new RuntimeException(
                        "Duplicate role assignment for permission: {$slug}."
                    );
                }

                $seenRoles[$roleSlug] = true;
            }
        }

        $roleFind = $pdo->prepare(
            'SELECT id, name, slug FROM roles WHERE slug = ? OR name = ?'
        );

        $permissionFind = $pdo->prepare(
            'SELECT id, name, slug FROM permissions WHERE name = ? OR slug = ?'
        );

        $permissionInsert = $pdo->prepare(
            'INSERT INTO permissions
             (name, slug, description, created_at, updated_at)
             VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)'
        );

        $mappingFind = $pdo->prepare(
            'SELECT id FROM permission_role
             WHERE permission_id = ? AND role_id = ?'
        );

        $mappingInsert = $pdo->prepare(
            'INSERT INTO permission_role
             (permission_id, role_id, created_at, updated_at)
             VALUES (?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)'
        );

        $createdPermissions = 0;
        $skippedPermissions = 0;
        $createdMappings = 0;
        $skippedMappings = 0;

        $pdo->beginTransaction();

        try {
            $roleIds = [];

            foreach ($roleNames as $slug => $name) {
                $roleFind->execute([$slug, $name]);
                $matches = $roleFind->fetchAll(PDO::FETCH_ASSOC);

                if (
                    count($matches) !== 1 ||
                    $matches[0]['slug'] !== $slug ||
                    $matches[0]['name'] !== $name
                ) {
                    throw new RuntimeException(
                        "Missing or conflicting role: {$slug}."
                    );
                }

                $roleIds[$slug] = (int) $matches[0]['id'];
            }

            foreach ($permissions as $permission) {
                $permissionFind->execute([
                    $permission['name'],
                    $permission['slug'],
                ]);

                $matches = $permissionFind->fetchAll(PDO::FETCH_ASSOC);

                if (count($matches) > 1) {
                    throw new RuntimeException(
                        "Conflicting permission records: {$permission['slug']}."
                    );
                }

                if (count($matches) === 1) {
                    $existing = $matches[0];

                    if (
                        $existing['name'] !== $permission['name'] ||
                        $existing['slug'] !== $permission['slug']
                    ) {
                        throw new RuntimeException(
                            "Permission name/slug conflict: {$permission['slug']}."
                        );
                    }

                    $permissionId = (int) $existing['id'];
                    $skippedPermissions++;
                } else {
                    $permissionInsert->execute([
                        $permission['name'],
                        $permission['slug'],
                        $permission['description'],
                    ]);

                    $permissionId = (int) $pdo->lastInsertId();
                    $createdPermissions++;
                }

                foreach ($permission['roles'] as $roleSlug) {
                    if (!isset($roleIds[$roleSlug])) {
                        throw new RuntimeException(
                            "Unknown role in permission data: {$roleSlug}."
                        );
                    }

                    $roleId = $roleIds[$roleSlug];

                    $mappingFind->execute([$permissionId, $roleId]);
                    $mappingMatches = $mappingFind->fetchAll(PDO::FETCH_ASSOC);

                    if (count($mappingMatches) > 1) {
                        throw new RuntimeException(
                            "Duplicate permission-role mapping detected."
                        );
                    }

                    if (count($mappingMatches) === 1) {
                        $skippedMappings++;
                        continue;
                    }

                    $mappingInsert->execute([$permissionId, $roleId]);
                    $createdMappings++;
                }
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }

        CLI::info("Created permissions: {$createdPermissions}\n");
        CLI::info("Existing permissions skipped: {$skippedPermissions}\n");
        CLI::info("Created role mappings: {$createdMappings}\n");
        CLI::info("Existing mappings skipped: {$skippedMappings}\n");
        CLI::success("Default permissions seeded successfully!\n");
    }
}
