<?php

namespace CLI\Commands;

use App\Core\Console\CLI;
use PDO;
use Throwable;

class MigrateStatus
{
    public function handle()
    {
        $host = env('DB_HOST');
        $port = env('DB_PORT') ?: 3306;
        $db   = env('DB_NAME');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');

        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$db";

            $charset = env('DB_CHARSET');
            if (!empty($charset)) {
                $dsn .= ";charset=$charset";
            }

            $pdo = new PDO(
                $dsn,
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

            // Read existing migration records only. Never create or alter tables.
            $records = $pdo->query(
                "SELECT migration, batch FROM migrations"
            )->fetchAll();

            $migrated = [];

            foreach ($records as $record) {
                $migrated[$record['migration']] = $record['batch'];
            }

            $files = glob(__DIR__ . '/../../Database/migrations/*.php');

            CLI::info("📝 Migrations Status:\n");
            CLI::info(str_pad("Migration", 50) . str_pad("Batch", 10) . "Status\n");
            CLI::info(str_repeat("-", 70) . "\n");

            foreach ($files as $file) {
                $name = basename($file);

                if (array_key_exists($name, $migrated)) {
                    $batchText = $migrated[$name] ?? "-";
                    $status = "✅ Run";
                } else {
                    $batchText = "-";
                    $status = "❌ Pending";
                }

                CLI::info(
                    str_pad($name, 50) .
                    str_pad((string) $batchText, 10) .
                    $status . "\n"
                );
            }

            CLI::info("\n");

        } catch (Throwable $e) {
            CLI::error(
                "❌ Migration status check failed: " .
                $e->getMessage() . "\n"
            );
        }
    }
}
