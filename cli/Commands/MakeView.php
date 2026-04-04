<?php

namespace CLI\Commands;

use App\Core\Console\CLI;

class MakeView
{
    public function handle(array $argv): void
    {
        $name = $argv[2] ?? null;

        if (!$name) {
            CLI::error("⚠ ❌ View name required\n");
            return;
        }

        $parts = explode('.', $name);
        if (count($parts) < 2) {
            CLI::error("⚠ ❌ Use dot notation: folder.filename (e.g., client.all)\n");
            return;
        }

        $folderName = $parts[0];
        $fileName   = $parts[1] . '.php';

        // ✅ Absolute project root path
        $rootDir    = realpath(__DIR__ . '/../../'); 
        $viewsDir   = $rootDir . '/View'; 
        $folderPath = $viewsDir . '/' . $folderName;
        $filePath   = $folderPath . '/' . $fileName;

        // Create folder if it doesn't exist
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
            CLI::info("📁 Folder created: $folderPath\n");
        }

        // Check if file already exists
        if (file_exists($filePath)) {
            CLI::error("⚠ ❌ View already exists: $filePath\n");
            return;
        }

        // Boilerplate
        $stub = <<<PHP
        <?php includes("layouts.header"); ?>
  <!-- View: $folderName/$fileName -->
<h1>$folderName/$fileName View</h1>
<?php includes("layouts.footer"); ?>

PHP;

        file_put_contents($filePath, $stub);
        CLI::info("✅ View created: $filePath\n");
    }
}