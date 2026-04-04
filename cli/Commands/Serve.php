<?php

namespace CLI\Commands;
class Serve
{
    public function handle()
    {
        echo "🚀 Server running at http://localhost:7000\n";
        // 👇 router file add kiya
       passthru("php -S localhost:7000 -t public");
    }
}