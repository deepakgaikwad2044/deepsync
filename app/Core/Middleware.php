<?php
namespace App\Core;

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\VoterGuestMiddleware;
use App\Middleware\JWTMiddleware;
use App\Middleware\VoterMiddleware;
use App\Middleware\VerifyCsrfToken;

class Middleware
{
  public static function handle(array $middlewares)
  {
    foreach ($middlewares as $middleware) {
      switch ($middleware) {
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
      }
    }
  }
}
