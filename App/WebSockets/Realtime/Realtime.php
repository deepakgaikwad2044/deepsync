<?php
namespace App\WebSockets\Realtime;

use App\WebSockets\Realtime\Broadcaster;

class Realtime
{
    protected function broadcast(string $channel, string $event, array $data = []): void
    {
        Broadcaster::broadcast($channel, $event, $data);
    }
}