<?php

require __DIR__ . "/vendor/autoload.php";

use React\EventLoop\Factory;
use React\Socket\SocketServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;
use Clue\React\Redis\Factory as RedisFactory;
use App\Websockets\Realtime\DS_Realtime;

/*
|--------------------------------------------------------------------------
| Create Event Loop
|--------------------------------------------------------------------------
*/

$loop = Factory::create();

/*
|--------------------------------------------------------------------------
| Create WebSocket Instance
|--------------------------------------------------------------------------
*/

$ds = new DS_Realtime();

/*
|--------------------------------------------------------------------------
| Create Socket Server
|--------------------------------------------------------------------------
*/

$socket = new SocketServer("0.0.0.0:8080", [], $loop);

$server = new IoServer(new HttpServer(new WsServer($ds)), $socket, $loop);

echo "🔥 DS Realtime Server Running at ws://localhost:8080\n";

/*
|--------------------------------------------------------------------------
| Redis Subscriber
|--------------------------------------------------------------------------
*/

$redisFactory = new RedisFactory($loop);

$redisFactory->createClient("redis://127.0.0.1:6379")->then(
  function ($redis) use ($ds) {
    echo "✅ Connected to Redis\n"; // 🔥 Subscribe to ALL channels
    $redis->psubscribe("*");
    $redis->on("pmessage", function ($pattern, $channel, $message) use ($ds) {
      echo "📩 Redis [$channel]: $message\n";
      $ds->channels->broadcast($channel, $message);
    });
  },
  function ($error) {
    echo "❌ Redis Error: " . $error->getMessage() . "\n";
  }
);
/* |-------------------------------------------------------------------------- |
Run Loop
|-------------------------------------------------------------------------- */
$loop->run();
