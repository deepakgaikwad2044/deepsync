<?php

namespace App\Core;

use App\Middleware\VerifyCsrfToken;

class Kernel
{
  protected array $middleware = [VerifyCsrfToken::class];

  public function handle()
  {
    foreach ($this->middleware as $middleware) {
      (new $middleware())->handle();
    }
  }
}
