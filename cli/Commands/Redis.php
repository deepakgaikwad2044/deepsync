<?php

namespace CLI\Commands;
use App\Core\Console\CLI;
class Redis
{
    public function handle()
    {
        CLI::info("🚀 Redis Server running ");

        // 👇 router file add kiya
       passthru("redis-server");
    }
}