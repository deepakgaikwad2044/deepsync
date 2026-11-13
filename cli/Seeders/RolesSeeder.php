<?php

namespace CLI\Seeders;

use PDO;
use RuntimeException;
use App\Core\Console\CLI;

class RolesSeeder
{
    public static function run(PDO $pdo): void
    {
        $roles = require __DIR__ . '/roles_data.php';

        $find = $pdo->prepare(
            'SELECT id, name, slug FROM roles WHERE name = ? OR slug = ?'
        );

        $insert = $pdo->prepare(
            'INSERT INTO roles (name, slug, description, created_at, updated_at)
             VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)'
        );

        $created = 0;
        $skipped = 0;

        $pdo->beginTransaction();

        try {
            foreach ($roles as $role) {
                $find->execute([$role['name'], $role['slug']]);
                $matches = $find->fetchAll(PDO::FETCH_ASSOC);

                if (count($matches) > 1) {
                    throw new RuntimeException(
                        "Conflicting role records found for {$role['slug']}."
                    );
                }

                if (count($matches) === 1) {
                    $existing = $matches[0];

                    if (
                        $existing['name'] !== $role['name'] ||
                        $existing['slug'] !== $role['slug']
                    ) {
                        throw new RuntimeException(
                            "Role name/slug conflict for {$role['slug']}; existing record was not modified."
                        );
                    }

                    $skipped++;
                    continue;
                }

                $insert->execute([
                    $role['name'],
                    $role['slug'],
                    $role['description'],
                ]);

                $created++;
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }

        CLI::info("Created roles: {$created}\n");
        CLI::info("Already existing roles skipped: {$skipped}\n");
        CLI::success("Default roles seeded successfully!\n");
    }
}
