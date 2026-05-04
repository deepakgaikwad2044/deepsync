<?php

namespace CLI\Commands;

use PDO;
use App\Core\Console\CLI;

class SeedUpdate
{
    public function handle($args)
    {
        try {
            $pdo = new PDO(
                env("DB_CONNECTION") . ":host=" . env("DB_HOST") . ";port=" . env("DB_PORT") . ";dbname=" . env("DB_NAME"),
                env("DB_USERNAME"),
                env("DB_PASSWORD")
            );

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $docs = require __DIR__ . '/../Seeders/docs_data.php';

            $stmt = $pdo->prepare("
                INSERT INTO docs (title, slug, content)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    content = VALUES(content)
            ");

            foreach ($docs as $doc) {
                $stmt->execute($doc);
                CLI::info("✔ Updated: " . $doc[1] . "\n");
            }

            CLI::success("🔥 Docs updated successfully!\n");

        } catch (\PDOException $e) {
            CLI::error("Error: " . $e->getMessage());
        }
    }
}