<?php

namespace CLI\Commands;

use App\Core\Console\CLI;

class CacheClear
{
    public function handle($argv)
    {
        $cachePath = __DIR__ . '/../../bootstrap/cache/';

        if (!is_dir($cachePath)) {
            CLI::error("Cache directory not found\n");
            return;
        }

        $files = glob($cachePath . '*');

        if (!$files) {
            CLI::warning("Cache already empty\n");
            return;
        }

        $deleted = 0;

        foreach ($files as $file) {

            // skip .gitignore if exists
            if (basename($file) === '.gitignore') {
                continue;
            }

            if (is_file($file)) {
                unlink($file);
                $deleted++;
            }
        }

        CLI::info("✅ Cache cleared successfully ($deleted files deleted)\n");
    }
}