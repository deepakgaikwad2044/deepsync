<?php

namespace App\Controllers;

use App\Config\Auth;

class AdminLogoutController
{
    public function logout(): void
    {
        Auth::logout();
        redirect(route("admin.login"));
        exit;
    }
}
