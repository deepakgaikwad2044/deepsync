<?php

namespace CLI\Commands;

use App\Core\Console\CLI;

class MakeComponent
{
    public function handle(array $argv): void
    {
        $name = $argv[2] ?? null;

        if (!$name) {
            CLI::error("⚠ ❌ Component name required\n");
            return;
        }

        // Support dot notation:
        // card
        // admin.user-card
        $relativePath = str_replace('.', '/', $name) . '.pra.php';

        // Absolute project root
        $rootDir = realpath(__DIR__ . '/../../');

        // views/components path
        $componentsDir = $rootDir . '/views/components';

        // Full file path
        $filePath = $componentsDir . '/' . $relativePath;

        // Create directory if not exists
        $dir = dirname($filePath);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            CLI::info("📁 Folder created: $dir\n");
        }

        // Check if component already exists
        if (file_exists($filePath)) {
            CLI::error("⚠ ❌ Component already exists: $filePath\n");
            return;
        }

        // Component stub
        $componentName = basename($name);

        $stub = <<<PHP
@props([])

<div class="component_{$componentName}">
    {!! \$__slot ?? '' !!}
</div>

PHP;

        file_put_contents($filePath, $stub);

        CLI::info("✅ Component created: $filePath\n");
        CLI::info("🧩 Usage: <x-{$name}></x-{$name}>\n");
    }
}