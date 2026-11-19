<?php

namespace CLI\Commands;

use PDO;
use App\Core\Console\CLI;
use CLI\Seeders\RolesSeeder;

class SeedRoles
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

            RolesSeeder::run($pdo);
        } catch (\Throwable $e) {
            CLI::error("Role seeding failed: " . $e->getMessage() . "\n");
            throw $e;
        }
    }
}
