<?php

namespace CLI\Commands;

use App\Core\Console\CLI;
use PDO;

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
            $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
        } catch (\PDOException $e) {
            CLI::error("❌ Database connection failed: " . $e->getMessage() . "\n");
            return;
        }

        // Ensure migrations table exists
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                batch INT
            )
        ");

        $files = glob(__DIR__ . '/../../database/migrations/*.php');

        CLI::info("📝 Migrations Status:\n");
        CLI::info(str_pad("Migration", 50) . str_pad("Batch", 10) . "Status\n");
        CLI::info(str_repeat("-", 70) . "\n");

        foreach ($files as $file) {
            $name = basename($file);

            $stmt = $pdo->prepare("SELECT batch FROM migrations WHERE migration = ?");
            $stmt->execute([$name]);
            $batch = $stmt->fetchColumn();

            $status = $batch ? "✅ Run" : "❌ Pending";
            $batchText = $batch ? $batch : "-";

            CLI::info(str_pad($name, 50) . str_pad($batchText, 10) . $status . "\n");
        }

        CLI::info("\n");
    }
}