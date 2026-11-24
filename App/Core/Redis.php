<?php

namespace App\Core;

use Predis\Client;

class Redis
{
    private static ?Client $client = null;

    public static function connection(): Client
    {
        if (self::$client === null) {
            self::$client = new Client([
                'scheme'  => 'tcp',
                'host'    => (string) Env::get('REDIS_HOST', '127.0.0.1'),
                'port'    => (int) Env::get('REDIS_PORT', 6379),
                'timeout' => 2.0,
            ]);
        }

        return self::$client;
    }

    public static function reset(): void
    {
        self::$client = null;
    }
}
