<?php

namespace CLI\Commands;

use App\Core\Console\CLI;

class MakeMigration
{
    public function handle($argv)
    {
        $name = isset($argv[2]) ? ucfirst($argv[2]) : null;

        if (!$name) {
            CLI::error("Migration name required\n");
            return;
        }

        // 🧠 Extract table name
        $table = $this->extractTableName($name);

        // ⚠ Reserved table names
        $reservedTables = ['users', 'password_resets', 'migrations'];

        if (in_array(strtolower($table), $reservedTables)) {
            CLI::error("Migration for table '{$table}' is not allowed (reserved name).\n");
            return;
        }

        // 🗂 Migration directory
        $migrationDir = __DIR__ . "/../../database/migrations/";

        if (!is_dir($migrationDir)) {
            mkdir($migrationDir, 0755, true);
            CLI::info("📁 'migrations' folder created.\n");
        }

        // 🔍 Check existing migration
        $files = glob($migrationDir . "*.php");
        foreach ($files as $file) {
            $content = file_get_contents($file);
            if (strpos($content, "Schema::create('{$table}'") !== false) {
                CLI::error("Migration for table '{$table}' already exists.\n");
                return;
            }
        }

        // 🕒 Timestamp & filename
        $timestamp = date('Y_m_d_His');
        $fileName = "{$timestamp}_{$name}.php";
        $path = $migrationDir . $fileName;

        // 🔥 Migration template
        $template = <<<PHP
<?php

use Database\Migration;
use Database\Schema;

class {$name} extends Migration
{
    public function up()
    {
        Schema::create('{$table}', function(\$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('{$table}');
    }
}
PHP;

        file_put_contents($path, $template);

        CLI::info("✅ Migration created: {$fileName}\n");

        // 🔥 Auto create Model
        $modelName = ucfirst($this->singular($table));
        $this->createModel($modelName);
    }

    // 🔥 Extract table name
    private function extractTableName($name)
    {
        if (preg_match('/create_(.*?)_table/', $name, $matches)) {
            return $matches[1];
        }

        return strtolower($name);
    }

    // 🔥 Singular (countries → country)
    private function singular($word)
    {
        if (substr($word, -3) === 'ies') {
            return substr($word, 0, -3) . 'y';
        }

        if (substr($word, -1) === 's') {
            return substr($word, 0, -1);
        }

        return $word;
    }

    // 🔥 Plural (country → countries)
    private function plural($word)
    {
        if (substr($word, -1) === 'y') {
            return substr($word, 0, -1) . 'ies';
        }

        return $word . 's';
    }

    // 🔥 Create Model
    private function createModel($modelName)
    {
        $modelDir = __DIR__ . "/../../app/Models/";

        if (!is_dir($modelDir)) {
            mkdir($modelDir, 0755, true);
        }

        $path = $modelDir . $modelName . ".php";

        // ❌ Check if exists
        if (file_exists($path)) {
            CLI::warning("⚠ Model {$modelName} already exists.\n");
            return;
        }

        $table = strtolower($this->plural($modelName));

        $template = "<?php

namespace App\Models;

use App\Core\Model;

class {$modelName} extends Model
{
    protected static \$table = '{$table}';
    protected static \$primaryKey = 'id';
    protected array \$hidden = [];
    
    protected static array \$searchable = [];
}
";

        file_put_contents($path, $template);

        CLI::info("✅ Model created: {$modelName}.php\n");
    }
}