<?php

namespace App\Middleware;

use App\Config\Auth;

class AuthMiddleware
{
    public function handle(?string $guard = null): void
    {
        if (!Auth::check()) {
            if ($guard === "admin") {
                redirect(route("admin.login"));
            } else {
                redirect(route("user.login"));
            }

            exit;
        }
    }
}
