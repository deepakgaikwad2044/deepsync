<?php

namespace CLI\Commands;

use CLI\Commands\MakeEvent;
use App\Core\Console\CLI;

class MakeChannel
{
    public function handle($argv)
    {
        $name = isset($argv[2]) ? ucfirst($argv[2]) : null;

        if (!$name) {
            CLI::error("Channel name required\n");
            return;
        }

        $channelName   = $name . "Channel";
        $eventBaseName = $name;          // ✅ only base name
        $eventName     = $name . "Event";

        // Paths
        $channelPath = __DIR__ . '/../../app/WebSockets/Realtime/Channels/' . $channelName . '.php';
        $eventPath   = __DIR__ . '/../../app/WebSockets/Events/' . $eventName . '.php';

        // Create folders
        if (!is_dir(dirname($channelPath))) {
            mkdir(dirname($channelPath), 0777, true);
        }

        if (!is_dir(dirname($eventPath))) {
            mkdir(dirname($eventPath), 0777, true);
        }

        // ✅ AUTO CREATE EVENT (FIXED)
        if (!file_exists($eventPath)) {
            $eventCommand = new MakeEvent();
            $eventCommand->handle(["", "", $eventBaseName]); // 🔥 FIX HERE
        } else {
            CLI::warning("Event already exists: $eventName\n");
        }

        // ❌ Prevent duplicate channel
        if (file_exists($channelPath)) {
            CLI::warning(" Channel already exists: $channelName\n");
            return;
        }

        // ✅ Channel Template
        $template = <<<PHP
<?php
namespace App\WebSockets\Realtime\Channels;

use App\WebSockets\Realtime\Realtime;
use App\WebSockets\Events\\$eventName;

class $channelName extends Realtime
{
    public function send(
    ): void
    {
        $eventName::dispatch();
    }
}
PHP;

        file_put_contents($channelPath, $template);

        CLI::info("Channel created: $channelName\n");
    }
}