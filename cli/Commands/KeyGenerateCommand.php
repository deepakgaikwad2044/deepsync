<?php

namespace CLI\Commands;
use App\Core\Console\CLI;

class KeyGenerateCommand
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
        if (preg_match('/^REALTIME_TOKEN=.*/m', $env)) {
            $env = preg_replace('/^REALTIME_TOKEN=.*/m', "REALTIME_TOKEN={$token}", $env);
        } else {
            $env .= "\nREALTIME_TOKEN={$token}\n";
        }

        file_put_contents($envPath, $env);

        echo "\n=====================================\n";
        echo "   🔐 Deep Sync Token Generator\n";
        echo "=====================================\n\n";

        CLI::info("✅ REALTIME_TOKEN generated successfully!\n");
        CLI::info("👉 {$token}\n\n");
    }
}