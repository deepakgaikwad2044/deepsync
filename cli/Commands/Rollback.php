<?php

namespace CLI\Commands;

use PDO;
use Throwable;
use App\Core\Console\CLI;

class Rollback
{
    public function handle()
    {
        try {
            $host = env('DB_HOST');
            $port = env('DB_PORT') ?: 3306;
            $db   = env('DB_NAME');
            $user = env('DB_USERNAME');
            $pass = env('DB_PASSWORD');

            $pdo = new PDO(
                "mysql:host=$host;port=$port;dbname=$db",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $batch = $pdo->query(
                "SELECT MAX(batch) FROM migrations"
            )->fetchColumn();

            if (!$batch) {
                CLI::warning("❌ Nothing to rollback\n");
                return;
            }

            $stmt = $pdo->prepare(
                "SELECT * FROM migrations WHERE batch = ? ORDER BY migration DESC"
            );
            $stmt->execute([$batch]);

            $migrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($migrations as $migration) {
                try {
                    $file = __DIR__ . '/../../database/migrations/' . $migration['migration'];

                    if (!file_exists($file)) {
                        throw new \RuntimeException(
                            "Migration file not found: " . $migration['migration']
                        );
                    }

                    require_once $file;

                    $className = str_replace('.php', '', $migration['migration']);
                    $className = preg_replace('/^[0-9_]+/', '', $className);

                    if (!class_exists($className)) {
                        throw new \RuntimeException(
                            "Migration class not found: " . $className
                        );
                    }

                    $instance = new $className($pdo);
                    $instance->down();

                    $del = $pdo->prepare(
                        "DELETE FROM migrations WHERE migration = ?"
                    );
                    $del->execute([$migration['migration']]);

                    CLI::info(
                        "↩ Rolled back: " . $migration['migration'] . "\n"
                    );

                } catch (Throwable $e) {
                    CLI::error(
                        "❌ Rollback failed: " . $migration['migration'] . "\n" .
                        $e->getMessage() . "\n"
                    );

                    return;
                }
            }

        } catch (Throwable $e) {
            CLI::error(
                "❌ Rollback error: " . $e->getMessage() . "\n"
            );
        }
    }
}
