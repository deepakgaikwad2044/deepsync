<?php

namespace CLI\Commands;
use App\Core\Console\CLI;

class MakeController
{
    public function handle($argv)
    {
       
        $name = isset($argv[2]) ? ucfirst($argv[2]) : null;

        if (!$name) {
             CLI::error("Controller name required\n");
            return;
        }
        
        $name = $name."Controller";

        $path = __DIR__ . '/../../app/Controllers/' . $name . '.php';

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        if (file_exists($path)) {
            CLI::warning("Controller already exists\n");
            return;
        }

        $template = "<?php

namespace App\Controllers;

class $name
{
    public function index()
    {
        
    }
}
";

        file_put_contents($path, $template);

        CLI::info("✅️ Controller created: $name\n");
    }
}