<?php

namespace CLI\Commands;
use App\Core\Console\CLI;

class AppKeyGenerateCommand
{
    public function handle()
    {
        // Random secure token (64 chars)
        $token = bin2hex(random_bytes(32));

        $envPath = __DIR__ . '/../../.env';

        // Agar .env nahi hai to create karo
        if (!file_exists($envPath)) {
            copy(__DIR__ . '/../../.env.example', $envPath);
        }

        $env = file_get_contents($envPath);

        // Replace existing REALTIME_TOKEN ya add new
        if (preg_match('/^APP_KEY=.*/m', $env)) {
            $env = preg_replace('/^APP_KEY=.*/m', "APP_KEY={$token}", $env);
        } else {
            $env .= "\APP_KEY={$token}\n";
        }

        file_put_contents($envPath, $env);

        echo "\n=====================================\n";
        echo "   🔐 Deep Sync Token APP KEY Generator\n";
        echo "=====================================\n\n";

        CLI::info("✅ APP_KEY generated successfully!\n");
        CLI::info("👉 {$token}\n\n");
    }
}