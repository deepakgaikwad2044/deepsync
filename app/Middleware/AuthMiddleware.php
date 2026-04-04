<?php

namespace App\Middleware;

use App\Config\Auth;   // ✅ CORRECT namespace

class AuthMiddleware
{
    public function handle()
    {
        if (!Auth::check()) {
            redirect(route("user.login"));
            exit;
        }
    }
}
