<?php

namespace CLI\Commands;
use App\Core\Console\CLI;

class MakeMiddleware
{
    public function handle($argv)
    {
        $name = isset($argv[2]) ? ucfirst($argv[2]) : null;

        if (!$name) {
            CLI::error("Middleware name required\n");
            return;
        }
        
        $name = $name. "Middleware";

        $path = __DIR__ . '/../../app/Middleware/' . $name . '.php';

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        if (file_exists($path)) {
            CLI::warning("Middleware already exists\n");
            return;
        }

 $template = "<?php

namespace App\Middleware;

class $name 
{
   public function handle()
    {
       
    }
}
";

        file_put_contents($path, $template);

        CLI::info("✅  Middleware  created: $name\n");
    }
}