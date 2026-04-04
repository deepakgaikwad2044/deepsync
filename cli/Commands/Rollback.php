<?php

namespace CLI\Commands;

use PDO;
use App\Core\Console\CLI;

class Rollback
{
    public function handle()
    {
        $host = env('DB_HOST');
        $port = env('DB_PORT');
        $db   = env('DB_NAME');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');

        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$db",
            $user,
            $pass
        );

        $batch = $pdo->query("SELECT MAX(batch) FROM migrations")->fetchColumn();

        if (!$batch) {
            CLI::warning("❌ Nothing to rollback\n");
            return;
        }

        $stmt = $pdo->prepare("SELECT * FROM migrations WHERE batch = ?");
        $stmt->execute([$batch]);

        $migrations = $stmt->fetchAll();

        foreach ($migrations as $migration) {
            $file = __DIR__ . '/../../database/migrations/' . $migration['migration'];

            if (!file_exists($file)) continue;

            require_once $file;

            $className = str_replace('.php', '', $migration['migration']);
            $className = preg_replace('/^[0-9_]+/', '', $className);

            $instance = new $className($pdo);
            $instance->down();

            $del = $pdo->prepare("DELETE FROM migrations WHERE migration = ?");
            $del->execute([$migration['migration']]);

            CLI::info("↩ Rolled back: " . $migration['migration'] . "\n");
        }
    }
}