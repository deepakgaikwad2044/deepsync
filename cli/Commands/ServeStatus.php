<?php

namespace CLI\Commands;

use App\Core\Console\CLI;

class ServeStatus
{
    public function handle(array $argv): void
    {
        echo "\n🚀 Server Status:\n";

        // Define servers with their commands or PID files
        $servers = [
            'WebSocket Server' => 'socket:serve', // php deep socket:serve
            'Redis Server'     => 'redis:serve',  // php deep redis:serve
        ];

        foreach ($servers as $name => $cmd) {
    if ($this->isRunning($cmd)) {
        CLI::info("- $name\t✅ Running\n");
    } else {
        CLI::error("- $name\t❌ Not running\n");
    }
}

        echo "\n";
    }

    // Check if command is running
    private function isRunning(string $cmd): bool
    {
        // Use `ps` to check process list (Linux/Termux/Unix)
        $output = [];
        exec("ps aux | grep '$cmd' | grep -v grep", $output);

        return count($output) > 0;
    }
}