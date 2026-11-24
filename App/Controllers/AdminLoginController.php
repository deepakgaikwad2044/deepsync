<?php

namespace App\Controllers;

use App\Config\Auth;
use App\Core\Validator;

class AdminLoginController
{
    public function index(): void
    {
        if (Auth::check() && Auth::hasRole("admin")) {
            redirect(route("admin.dashboard"));
            return;
        }

        view("admin.auth.login");
    }

    public function login(): void
    {
        $data = $_POST;
        $validator = new Validator();

        $result = $validator->validate($data, [
            "email" => "required|email|exists:users,email",
            "password" => "required|len:6",
        ]);

        if (!$result["status"]) {
            $_SESSION["errors"] = $result["errors"];
            $_SESSION["old"] = $data;
            redirect(route("admin.login"));
            return;
        }

        if (!Auth::attempt($data, "admin")) {
            $_SESSION["errors"]["password"] = "Invalid Admin credentials";
            $_SESSION["old"] = ["email" => $data["email"] ?? ""];
            redirect(route("admin.login"));
            return;
        }

        unset($_SESSION["errors"], $_SESSION["old"]);
        redirect(route("admin.dashboard"));
    }
}
