<?php

namespace CLI\Commands;

use App\Core\Console\CLI;

class MakeModel
{
    public function handle($argv)
    {
        $name = isset($argv[2]) ? ucfirst($argv[2]) : null;

        if (!$name) {
            CLI::error("Model name required\n");
            return;
        }

        $path = __DIR__ . '/../../app/Models/' . $name . '.php';

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        if (file_exists($path)) {
            CLI::warning("Model already exists\n");
            return;
        }

        $template = "<?php

namespace App\Models;
use App\Core\Model;

class $name extends Model
{
    protected static \$table;
    protected static \$primaryKey;
    protected array \$hidden = ['password'];
    
    protected static array \$searchable = [];
}
";

        file_put_contents($path, $template);

        CLI::info("✅ Model created: $name\n");
    }
}