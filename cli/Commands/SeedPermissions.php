<?php

namespace CLI\Commands;

use PDO;
use App\Core\Console\CLI;
use CLI\Seeders\PermissionsSeeder;

class SeedPermissions
{
    public function handle($args): void
    {
        try {
            $pdo = new PDO(
                env("DB_CONNECTION") . ":host=" . env("DB_HOST")
                . ";port=" . env("DB_PORT")
                . ";dbname=" . env("DB_NAME"),
                env("DB_USERNAME"),
                env("DB_PASSWORD")
            );

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            PermissionsSeeder::run($pdo);
        } catch (\Throwable $e) {
            CLI::error("Permission seeding failed: " . $e->getMessage() . "\n");
        }
    }
}
