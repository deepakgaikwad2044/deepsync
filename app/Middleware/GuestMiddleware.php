<?php
namespace App\Middleware;

use App\Config\Auth;
use App\Config\VoterAuth;

class GuestMiddleware
{
    public function handle()
    {
        if (Auth::check()) {
            redirect(route('user.dashboard'));
            exit;
        }
    }
}
