<?php

namespace CLI\Commands;

use App\Core\Console\CLI;

class MakeComponent
{
    public function handle($argv)
    {
        $input = $argv[2] ?? null;

        if (!$input) {
            CLI::error("Component name required\n");
            return;
        }

        /**
         * Support:
         * button
         * forms/input
         * ui/cards/stat
         */
        $input = trim($input, '/');

        $parts = explode('/', $input);

        $name = array_pop($parts);
        $folders = $parts;

        $className = ucfirst($name);
        $fileName  = $name . '.pra.php';

        $basePath = __DIR__ . '/../../views/components';

        // build directory path
        $dirPath = $basePath;

        if (!empty($folders)) {
            $dirPath .= '/' . implode('/', $folders);
        }

        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $filePath = $dirPath . '/' . $fileName;

        if (file_exists($filePath)) {
            CLI::warning("Component already exists: $input\n");
            return;
        }

        /**
         * Convert folder/name → x-folder-name
         */
        $xName = strtolower(str_replace('/', '-', $input));

        $template = <<<PHP
<div class="component component-{$xName}">

    <!-- {$className} Component -->
    <div class="component-box">

        <!-- Props -->
        @php
            \$props = \$props ?? [];
        @endphp

        <h3>{$className} Component</h3>

        <p>This is {$className} component</p>

        <!-- Slot support -->
        {{ \$slot ?? '' }}

    </div>

</div>
PHP;

        file_put_contents($filePath, $template);

        CLI::info("✅ Component created: $filePath\n");
        CLI::info("🧩 Usage: <x-{$name}></x-{$name}>\n");
    }
}