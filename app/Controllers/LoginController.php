<?php
namespace App\Controllers;

use App\Models\User;
use App\Config\Auth;
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

      if (!Auth::attempt($data)) {
          $_SESSION["errors"]["password"] = "Invalid email or password";
          $_SESSION["old"] = $data;

          redirect(route("user.login"));
          return;
      }

      User::update(Auth::id(), [
          "intro_seen" => 0
      ]);

      unset($_SESSION["errors"], $_SESSION["old"]);

      redirect(route("user.dashboard"));
  }
}
