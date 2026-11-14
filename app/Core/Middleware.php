<?php

namespace App\Core;

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\JWTMiddleware;
use App\Middleware\VerifyCsrfToken;
use App\Middleware\RateLimitMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\PermissionMiddleware;

class Middleware
{
    public static function handle(array $middlewares, bool $isWeb = false)
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

                case "role":
                    (new RoleMiddleware())->handle($parameterString, $isWeb);
                    break;

                case "permission":
                    (new PermissionMiddleware())->handle($parameterString, $isWeb);
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
                        $decayMinutes,
                        $isWeb
                    );

                    break;
                default:
                    http_response_code(500);
                    header("Content-Type: application/json; charset=UTF-8");
                    echo json_encode(["error" => "Middleware configuration error"]);
                    exit;
            }
        }
    }
}
