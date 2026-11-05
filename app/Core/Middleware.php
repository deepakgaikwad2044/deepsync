<?php

namespace App\Core;

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\JWTMiddleware;
use App\Middleware\VerifyCsrfToken;
use App\Middleware\RateLimitMiddleware;

class Middleware
{
    public static function handle(array $middlewares)
    {
        foreach ($middlewares as $middleware) {

            [$name, $parameterString] = array_pad(
                explode(':', $middleware, 2),
                2,
                null
            );

            switch ($name) {

                case "auth":
                    (new AuthMiddleware())->handle();
                    break;

                case "guest":
                    (new GuestMiddleware())->handle();
                    break;

                case "web":
                    (new VerifyCsrfToken())->handle();
                    break;

                case "JWT":
                    (new JWTMiddleware())->handle();
                    break;

                case "throttle":

                    $parameters = $parameterString !== null
                        ? array_map('trim', explode(',', $parameterString))
                        : [];

                    $maxAttempts = isset($parameters[0])
                        && ctype_digit($parameters[0])
                            ? (int) $parameters[0]
                            : 60;

                    $decayMinutes = isset($parameters[1])
                        && ctype_digit($parameters[1])
                            ? (int) $parameters[1]
                            : 1;

                    (new RateLimitMiddleware())->handle(
                        $maxAttempts,
                        $decayMinutes
                    );

                    break;
            }
        }
    }
}
