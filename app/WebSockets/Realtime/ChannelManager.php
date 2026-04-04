<?php
namespace App\WebSockets\Realtime;

use Ratchet\ConnectionInterface;

class ChannelManager
{
    protected array $channels = [];

    public function subscribe(string $channel, ConnectionInterface $conn): void
    {
        $this->channels[$channel][$conn->resourceId] = $conn;
    }

    public function unsubscribe(ConnectionInterface $conn): void
    {
        foreach ($this->channels as $channel => $clients) {
            unset($this->channels[$channel][$conn->resourceId]);
        }
    }

    public function broadcast(string $channel, string $message): void
    {
        if (!isset($this->channels[$channel])) return;

        foreach ($this->channels[$channel] as $client) {
            $client->send($message);
        }
    }
}
