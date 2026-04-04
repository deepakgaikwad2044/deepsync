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

        // ❌ Check if table is reserved
        if (in_array(strtolower($table), $reservedTables)) {
            CLI::error("Migration for table '{$table}' is not allowed (reserved name).\n");
            return;
        }

        // 🗂 Migration directory
        $migrationDir = __DIR__ . "/../../database/migrations/";

        // 🔍 Check if a migration for this table already exists (inside file)
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
    }

    // 🔥 Table name extractor
    private function extractTableName($name)
    {
        // create_users_table → users
        if (preg_match('/create_(.*?)_table/', $name, $matches)) {
            return $matches[1];
        }

        // fallback
        return strtolower($name);
    }
}