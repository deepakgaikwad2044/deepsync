<?php

namespace CLI\Commands;

use App\Core\Console\CLI;
use PDO;

class Migrate
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
        
        $checkColumn = $pdo->query("SHOW COLUMNS FROM migrations LIKE 'batch'");
if ($checkColumn->rowCount() == 0) {
    $pdo->exec("ALTER TABLE migrations ADD batch INT DEFAULT 1");
}

        // migrations table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                batch INT
            )
        ");

        // current batch
        $batch = $pdo->query("SELECT MAX(batch) FROM migrations")->fetchColumn();
        $batch = $batch ? $batch + 1 : 1;

        $files = glob(__DIR__ . '/../../database/migrations/*.php');

        foreach ($files as $file) {
            $name = basename($file);

            $check = $pdo->prepare("SELECT * FROM migrations WHERE migration = ?");
            $check->execute([$name]);

            if ($check->fetch()) {
                continue;
            }

            require_once $file;

            $className = str_replace('.php', '', $name);
            $className = preg_replace('/^[0-9_]+/', '', $className);

            $instance = new $className($pdo);
            $instance->up();

            $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
            $stmt->execute([$name, $batch]);

            CLI::info("✅ Migrated: $name\n");
        }
    }
}