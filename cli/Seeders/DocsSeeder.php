<?php

namespace CLI\Seeders;

use PDO;
use App\Core\Console\CLI;

class DocsSeeder
{
    public static function run(PDO $pdo)
    {
        $docs = require __DIR__ . "/docs_data.php";

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

        CLI::success("📚 Docs synced successfully!\n");
    }
}