<?php

namespace CLI\Commands;

use App\Core\Console\CLI;

class MakeView
{
    public function handle(array $argv): void
    {
        $name = $argv[2] ?? null;

        if (!$name) {
            CLI::error("⚠ View name required\n");
            return;
        }

        // Only folder.filename notation is allowed.
        $parts = explode('.', $name);

        if (count($parts) !== 2) {
            CLI::error("⚠ Use dot notation: folder.filename\n");
            return;
        }

        [$folderName, $viewName] = $parts;

        // Reject traversal, separators and unsafe characters.
        $validName = '/\A[a-zA-Z][a-zA-Z0-9_-]*\z/D';

        if (
            !preg_match($validName, $folderName) ||
            !preg_match($validName, $viewName)
        ) {
            CLI::error("⚠ Invalid view name. Use letters, numbers, _ and - only.\n");
            return;
        }

        $rootDir = realpath(__DIR__ . '/../../');

        if ($rootDir === false) {
            CLI::error("⚠ Project root not found.\n");
            return;
        }

        $viewsDir = $rootDir . DIRECTORY_SEPARATOR . 'Views';

        if (!is_dir($viewsDir)) {
            CLI::error("⚠ Views directory not found.\n");
            return;
        }

        $folderPath = $viewsDir . DIRECTORY_SEPARATOR . $folderName;
        $fileName = $viewName . '.pra.php';
        $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;

        if (!is_dir($folderPath)) {
            if (!mkdir($folderPath, 0755, true) && !is_dir($folderPath)) {
                CLI::error("⚠ Unable to create view directory.\n");
                return;
            }

            CLI::info("📁 Folder created: $folderPath\n");
        }

        // Confirm the resolved directory remains inside Views.
        $realFolder = realpath($folderPath);
        $realViews = realpath($viewsDir);

        if (
            $realFolder === false ||
            $realViews === false ||
            !str_starts_with(
                $realFolder . DIRECTORY_SEPARATOR,
                $realViews . DIRECTORY_SEPARATOR
            )
        ) {
            CLI::error("⚠ Invalid view directory.\n");
            return;
        }

        if (file_exists($filePath)) {
            CLI::error("⚠ View already exists: $filePath\n");
            return;
        }

        $stub = <<<PHP
<?php includes("layouts.header"); ?>
<!-- View: {$folderName}/{$fileName} -->
<h1>{$folderName}/{$fileName} View</h1>
<?php includes("layouts.footer"); ?>

PHP;

        if (file_put_contents($filePath, $stub, LOCK_EX) === false) {
            CLI::error("⚠ Unable to create view file.\n");
            return;
        }

        CLI::info("✅ View created: $filePath\n");
    }
}
