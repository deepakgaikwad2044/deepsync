<?php
namespace App\WebSockets\Realtime;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class DS_Realtime implements MessageComponentInterface
{
    public ChannelManager $channels;

    protected array $channelConfig = [];

    public function __construct()
    {
        $this->channels = new ChannelManager();

        // Load class-based channels config
        $this->channelConfig = require dirname(__DIR__, 3) . '/config/realtime_channels.php';
    }

    public function onOpen(ConnectionInterface $conn)
    {
        echo "Client Connected\n";
    }

   public function onMessage(ConnectionInterface $from, $msg)
{
    $data = json_decode($msg, true);

    $action = $data['action'] ?? '';
    $channelKey = trim($data['channel'] ?? '');
    $token = $data['token'] ?? null;
    $message = $data['message'] ?? null;

    if (!$channelKey) {
        $from->send(json_encode(["error" => "No channel provided"]));
        return;
    }

    // 🔹 Get channel config by short name
    $channelConfig = $this->channelConfig[$channelKey] ?? null;
    if (!$channelConfig) {
        $from->send(json_encode(["error" => "Channel Not Found"]));
        return;
    }

    $channelClass = $channelConfig['class'];

    // 🔐 Auth check
    if (($channelConfig['auth'] ?? false) && !AuthGuard::check($token)) {
        $from->send(json_encode(["error" => "Unauthorized"]));
        $from->close();
        return;
    }

    if ($action === 'subscribe') {
        // ✅ Subscribe
        $this->channels->subscribe($channelKey, $from);

        $from->send(json_encode([
            "status"  => "Subscribed",
            "channel" => $channelKey
        ]));
    }

    if ($action === 'publish' && $message) {
        // ✅ Broadcast to all subscribers
        $this->channels->broadcast($channelKey, $message);
    }
}

    public function onClose(ConnectionInterface $conn)
    {
        $this->channels->unsubscribe($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }
}