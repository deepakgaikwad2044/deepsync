<?php

namespace App\Controllers;

use App\Config\Auth;

class AdminDashboardController
{
    public function index(): void
    {
        view("admin.dashboard", [
            "user" => Auth::user(),
        ]);
    }
}
