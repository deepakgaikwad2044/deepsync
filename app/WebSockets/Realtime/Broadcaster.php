<?php
namespace App\WebSockets\Realtime;

use Predis\Client as Redis;

class Broadcaster
{
    protected static function redis(): Redis
    {
        return new Redis([
            'scheme' => 'tcp',
            'host'   => getenv('REDIS_HOST') ?: '127.0.0.1',
            'port'   => getenv('REDIS_PORT') ?: 6379,
        ]);
    }

    /**
     * Publish event to channel
     */
    public static function broadcast(string $channel, string $event, array $data = []): void
    {
        try {
            $payload = [
                "event"   => $event,
                "channel" => $channel,
                "data"    => $data,
                "time"    => time()
            ];

            self::redis()->publish($channel, json_encode($payload));

        } catch (\Exception $e) {
            $logDir = __DIR__ . '/storage/logs';

if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

file_put_contents(
    $logDir . '/realtime.log',
    "[" . date("Y-m-d H:i:s") . "] " . $e->getMessage() . PHP_EOL,
    FILE_APPEND
);
        }
    }
}
