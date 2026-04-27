<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Validator;

class LoginController
{
  // Show login page
  public function index()
  {
    view("auth.login");
  }

  public function login()
{
    $data = $_POST;

    $email = $data['email'];
    $password = $data['password'];

    $validator = new Validator();

    $result = $validator->validate($data, [
        "email" => "required|email|exists:users,email",
        "password" => "required|len:6",
    ]);

    if (!$result["status"]) {
        $_SESSION["errors"] = $result["errors"];
        $_SESSION["old"] = $data;

        redirect(route("user.login"));
        return;
    }

    // 🔥 USER FETCH
    $user = User::query()
        ->where('email', $email)
        ->first();

    // 🔥 AUTH CHECK
    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION["errors"]["password"] = "Invalid email or password";
        $_SESSION["old"] = $data;

        redirect(route("user.login"));
        return;
    }

    // 🔥 SUCCESS LOGIN
    $_SESSION["user_id"] = $user['id'];

    unset($_SESSION["errors"], $_SESSION["old"]);

    redirect(route("user.dashboard"));
}
}
