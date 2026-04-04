<?php

namespace CLI\Commands;

use App\Core\Console\CLI;

class WSSocket
{
    public function handle()
    {
        echo "🚀 Server running at http://localhost:7000\n";
        // 👇 router file add kiya
       passthru("php realtime-server.php");
    }
}