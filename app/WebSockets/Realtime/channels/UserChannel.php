<?php
namespace App\WebSockets\Realtime\Channels;

use App\WebSockets\Realtime\Realtime;

class UserChannel extends Realtime
{
    public function send(string $event, array $data = []): void
    {
        $this->broadcast("UserChannel", $event, $data);
    }
}