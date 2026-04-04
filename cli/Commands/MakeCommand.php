<?php

namespace CLI\Command;

use App\Core\Console\CLI;

class MakeCommand
{
    public function handle(array $argv): void
    {
       $name = isset($argv[2]) ? ucfirst($argv[2]) : null;

        if (!$name) {
            CLI::error("⚠ ❌ Command name required\n");
            return;
        }

        // Ensure proper class name
        $className = ucfirst($name);
        $dirPath   = __DIR__; // cli/MyCommand
        $filePath  = $dirPath . "/$className.php";

        if (file_exists($filePath)) {
            CLI::error("⚠ ❌ Command already exists: $className\n");
            return;
        }

        // Boilerplate template
        $stub = <<<PHP
<?php

namespace CLI\MyCommands;

use App\Core\Console\CLI;

class $className
{
    public function handle(array \$argv): void
    {
        CLI::info("✅ $className command executed successfully!\\n");
    }
}

PHP;

        file_put_contents($filePath, $stub);
        CLI::info("🚀 Command created: $filePath\n");
    }
}