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

            // 1️⃣ CONNECT WITHOUT DB
            $pdo = new PDO("$driver:host=$host;port=$port", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 2️⃣ CHECK DB EXISTS
            $check = $pdo->query("SHOW DATABASES LIKE '$dbname'");
            $exists = $check->rowCount() > 0;

            if ($exists) {
                CLI::warning("⚠ Database already exists: $dbname");
            } else {
                $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                CLI::success("✔ Database created: $dbname");
            }

            // 3️⃣ CONNECT DB
            $pdo = new PDO("$driver:host=$host;port=$port;dbname=$dbname", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            CLI::info("📦 Connected to database");

            // 4️⃣ TABLES
            $tables = [

                'migrations' => "
                    CREATE TABLE IF NOT EXISTS migrations (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        migration VARCHAR(255),
                        batch INT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ",

                'users' => "
                    CREATE TABLE IF NOT EXISTS users (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(100) NOT NULL,
                        email VARCHAR(100) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        avtar VARCHAR(255) DEFAULT '/storage/default_avtar/default_avtar.png',
                        refresh_token VARCHAR(255) NULL,
                        refresh_token_expiry DATETIME NULL,
                        role_as TINYINT DEFAULT 0,
                        account_status TINYINT DEFAULT 1,
                        intro_seen TINYINT DEFAULT 0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )
                ",

                'password_resets' => "
                    CREATE TABLE IF NOT EXISTS password_resets (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        email VARCHAR(100) NOT NULL,
                        token VARCHAR(255) NOT NULL,
                        expires_at TIMESTAMP NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ",

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

            // 5️⃣ EXISTING TABLES
            $existingTables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

            foreach ($tables as $name => $sql) {

                if (in_array($name, $existingTables)) {
                    CLI::warning("⚠ Table exists: $name");
                    continue;
                }

                $pdo->exec($sql);
                CLI::success("➕ Created: $name");
            }

            // 6️⃣ SEED
            $docs = require __DIR__ . '/../Seeders/docs_data.php';

            $stmt = $pdo->prepare("
                INSERT INTO docs (title, slug, content)
                VALUES (:title, :slug, :content)
                ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    content = VALUES(content)
            ");

            foreach ($docs as $doc) {

                $title   = $doc['title']   ?? $doc[0] ?? null;
                $slug    = $doc['slug']    ?? $doc[1] ?? null;
                $content = $doc['content'] ?? $doc[2] ?? null;

                if (!$title || !$slug || !$content) {
                    CLI::warning("⚠ Skipped invalid doc");
                    continue;
                }

                $stmt->execute([
                    ':title' => $title,
                    ':slug' => $slug,
                    ':content' => $content,
                ]);

                CLI::info("✔ Synced: $slug");
            }

            (new Migrate())->handle();

            (new SeedRoles())->handle($args);

            (new SeedPermissions())->handle($args);

            CLI::success("📚 Migration completed successfully!");

        } catch (\Throwable $e) {
            CLI::error("❌ Fatal Error: " . $e->getMessage());
            throw $e;
        }
    }
}
