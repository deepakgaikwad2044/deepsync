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

    // ---- AUTH CHECK (example) ----
    $user = User::query()
      ->where("email", $data["email"])
      ->firstRaw();

    if (!$user || !password_verify($data["password"], $user["password"])) {
      $_SESSION["errors"]["password"] = "Invalid email or password";
      $_SESSION["old"] = $data;

      redirect(route("user.login"));
      return;
    }

    // login success
    $_SESSION["user_id"] = $user["id"];
    redirect(route("user.dashboard"));

    // ---- LOGIN SUCCESS ----
    $_SESSION["user_id"] = $user["id"];

    unset($_SESSION["errors"], $_SESSION["old"]);

    redirect(route("user.dashboard"));
  }
}
