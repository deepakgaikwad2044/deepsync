<?php

namespace CLI\Commands;

use App\Core\Console\CLI;
use PDO;

class MigrateInstall
{
    public function handle($args)
    {
        $driver = env("DB_CONNECTION");
        $host   = env("DB_HOST");
        $port   = env("DB_PORT");
        $user   = env("DB_USERNAME");
        $pass   = env("DB_PASSWORD");
        $dbname = env("DB_NAME");

        try {
            // 1️⃣ Connect without DB
            $pdo = new PDO("$driver:host=$host;port=$port", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 2️⃣ Create DB
            $check = $pdo->query("SHOW DATABASES LIKE '$dbname'");
            if ($check->rowCount() == 0) {
                $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                CLI::success("✅ Database created\n");
            } else {
                CLI::warning("Database exists\n");
            }

            // 3️⃣ Connect DB
            $pdo = new PDO("$driver:host=$host;port=$port;dbname=$dbname", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 4️⃣ Tables
            $tables = [

                'docs' => "
                    CREATE TABLE IF NOT EXISTS docs (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        title VARCHAR(255),
                        slug VARCHAR(255) UNIQUE,
                        content LONGTEXT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
                    )
                "
            ];

            foreach ($tables as $name => $sql) {
                $pdo->exec($sql);
                CLI::info("✔ Table: $name\n");
            }

            // 🔥 5. LOAD SEED FILE
            $docs = require __DIR__ . '/../Seeders/docs_data.php';

            // 🔥 6. UPSERT
            $stmt = $pdo->prepare("
                INSERT INTO docs (title, slug, content)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    content = VALUES(content)
            ");

            foreach ($docs as $doc) {
                $stmt->execute($doc);
                CLI::info("✔ Synced: " . $doc[1] . "\n");
            }

            CLI::success("📚 Docs seeded successfully!\n");

        } catch (\PDOException $e) {
            CLI::error("Error: " . $e->getMessage() . "\n");
        }
    }
}