<?php

namespace App\Middleware;

use App\Core\Redis;
use App\Core\Response;

class RateLimitMiddleware
{
    public function handle(
        int $maxAttempts = 60,
        int $decayMinutes = 1
    ): void {
        $maxAttempts = max(1, $maxAttempts);
        $decaySeconds = max(1, $decayMinutes) * 60;

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        $key = 'deepsync:rate_limit:' . hash(
            'sha256',
            $ip . '|' . $method . '|' . $uri
        );

        $script = <<<'LUA'
local current = redis.call('INCR', KEYS[1])

if current == 1 then
    redis.call('EXPIRE', KEYS[1], ARGV[1])
end

local ttl = redis.call('TTL', KEYS[1])

return {current, ttl}
LUA;

        try {
            $result = Redis::connection()->eval(
                $script,
                1,
                $key,
                $decaySeconds
            );

            $current = (int) $result[0];
            $ttl = max(1, (int) $result[1]);

        } catch (\Throwable $e) {
            error_log(
                '[RateLimitMiddleware] Redis error: ' . $e->getMessage()
            );

            return;
        }

        $remaining = max(0, $maxAttempts - $current);

        header("X-RateLimit-Limit: {$maxAttempts}");
        header("X-RateLimit-Remaining: {$remaining}");

        if ($current > $maxAttempts) {
            header("Retry-After: {$ttl}");

            Response::error(
                "Too many requests. Please try again later.",
                429
            );
        }
    }
}
