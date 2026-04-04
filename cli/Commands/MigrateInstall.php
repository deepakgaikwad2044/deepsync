<?php
namespace CLI\Commands;

use App\Core\Env;
use App\Core\Console\CLI;
use PDO;

class MigrateInstall
{
    public function handle($args)
    {
        // ENV se settings lo
        $driver = env("DB_CONNECTION");
        $host = env("DB_HOST");
        $port = env("DB_PORT");
        $user = env("DB_USERNAME");
        $pass = env("DB_PASSWORD");
        $dbname = env("DB_NAME");

        try {
            // 1️⃣ Connect to MySQL without database first
            $pdo = new PDO("$driver:host=$host;port=$port", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 2️⃣ Check if database exists
            $check = $pdo->query("SHOW DATABASES LIKE '$dbname'");
            if ($check->rowCount() == 0) {
                // Create database if not exists
                $pdo->exec("CREATE DATABASE `$dbname`");
                CLI::success("✅ Database '$dbname' created successfully!\n");
            } else {
                CLI::warning("Database '$dbname' already exists!\n");
            }

            // 3️⃣ Now connect to the actual database
            $pdo = new PDO("$driver:host=$host;port=$port;dbname=$dbname", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // ----------------------------
            // Tables creation logic
            // ----------------------------
            $tables = [
                'migrations' => "
                    CREATE TABLE migrations (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        migration VARCHAR(255),
                        batch INT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ",
                'users' => "
                    CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avtar VARCHAR(255) DEFAULT '/storage/default_avtar/default_avtar.png',
 refresh_token VARCHAR(255) NULL,
refresh_token_expiry DATETIME NULL ,
    role_as TINYINT DEFAULT 0,
    account_status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
                ",
                'password_resets' => "
                    CREATE TABLE password_resets (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        email VARCHAR(100) NOT NULL,
                        token VARCHAR(255) NOT NULL,
                        expires_at TIMESTAMP NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                "
            ];

            foreach ($tables as $name => $sql) {
                $check = $pdo->query("SHOW TABLES LIKE '$name'");
                if ($check->rowCount() > 0) {
                    CLI::warning("Table '$name' already exists!\n");
                } else {
                    $pdo->exec($sql);
                    CLI::info("✅ Table '$name' created successfully!\n");
                }
            }

        } catch (\PDOException $e) {
            CLI::error("Error: " . $e->getMessage() . "\n");
        }
    }
}