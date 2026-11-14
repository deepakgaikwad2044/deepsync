<?php

namespace CLI\Commands;

use App\Core\Console\CLI;
use PDO;
use Throwable;

class Migrate
{
    public function handle()
    {
        $host = env('DB_HOST');
        $port = env('DB_PORT') ?: 3306;
        $db   = env('DB_NAME');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');

        try {
            $pdo = new PDO(
                "mysql:host=$host;port=$port;dbname=$db",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

            // Create migrations table first
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS migrations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    migration VARCHAR(255),
                    batch INT
                )
            ");

            // Check batch column
            $checkColumn = $pdo->query(
                "SHOW COLUMNS FROM migrations LIKE 'batch'"
            );

            if ($checkColumn->rowCount() == 0) {
                $pdo->exec(
                    "ALTER TABLE migrations ADD batch INT DEFAULT 1"
                );
            }

            // Get current batch
            $batch = $pdo->query(
                "SELECT MAX(batch) FROM migrations"
            )->fetchColumn();

            $batch = $batch ? $batch + 1 : 1;

            // Get migration files
            $migrationDir = __DIR__ . '/../../database/migrations/';
            $files = glob($migrationDir . '*.php');

            if (empty($files)) {
                CLI::info("ℹ No migration files found.\n");
                return;
            }

            foreach ($files as $file) {
                $name = basename($file);

                // Check if already migrated
                $check = $pdo->prepare(
                    "SELECT id FROM migrations WHERE migration = ?"
                );

                $check->execute([$name]);

                if ($check->fetch()) {
                    continue;
                }

                try {
                    require_once $file;

                    // Extract migration class name
                    $className = str_replace('.php', '', $name);
                    $className = preg_replace('/^[0-9_]+/', '', $className);

                    if (!class_exists($className)) {
                        throw new \RuntimeException(
                            "Migration class not found: $className"
                        );
                    }

                    $instance = new $className($pdo);

                    // Execute migration
                    $instance->up();

                    // Record only after successful execution
                    $stmt = $pdo->prepare("
                        INSERT INTO migrations (migration, batch)
                        VALUES (?, ?)
                    ");

                    $stmt->execute([$name, $batch]);

                    CLI::info("✅ Migrated: $name\n");

                } catch (Throwable $e) {
                    CLI::error(
                        "❌ Migration failed: $name\n" .
                        $e->getMessage() . "\n"
                    );

                    throw $e;
                }
            }

            CLI::info("✔ All pending migrations processed successfully!\n");

        } catch (Throwable $e) {
            CLI::error(
                "❌ Migration error: " . $e->getMessage() . "\n"
            );
            throw $e;
        }
    }
}
