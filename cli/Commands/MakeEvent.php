<?php

namespace CLI\Commands;
use App\Core\Console\CLI;
class MakeEvent
{
    public function handle($argv)
    {
   $name = isset($argv[2]) ? ucfirst($argv[2]) : null;
   
        if (!$name) {
            CLI::error("Event name required\n");
            return;
        }

        // ✅ Avoid double Event (ClientEventEvent fix)
        if (!str_ends_with($name, 'Event')) {
            $baseName = $name;
            $name .= "Event";
        } else {
            $baseName = str_replace('Event', '', $name);
        }

        $channel = $baseName . "Channel";

        $path = __DIR__ . '/../../app/WebSockets/Events/' . $name . '.php';

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        if (file_exists($path)) {
            CLI::warning("Event already exists: $name\n");
            return;
        }

        $template = <<<PHP
<?php
namespace App\WebSockets\Events;

use App\Core\Helpers\Realtime;

class $name
{
    public static function dispatch(
       
    ): void {

        \$realtime = new Realtime();

        \$realtime->$channel(
            "{$baseName}updated",
            [
               
            ]
        );
    }
}
PHP;

        file_put_contents($path, $template);

        CLI::info("✅  Event created: $name\n");
    }
}