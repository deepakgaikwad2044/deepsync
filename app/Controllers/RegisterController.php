<?php
namespace App\Controllers;
use App\Models\User;
use App\Core\Validator;

class RegisterController
{
  public function index()
  {
    view("auth.register");
  }

  public function register()
  {
    $data = $_POST;

    $validator = new Validator();

    $result = $validator->validate($data, [
      "name" => "required|len:3",
      "email" => "required|email|unique:users,email",
      "password" => "required|len:6|confirmed",
      "confirm_password" => "required",
    ]);

    if (!$result["status"]) {
      $_SESSION["errors"] = $result["errors"];
      $_SESSION["errors"]["confirm_password"] = "confirm password required ";
      $_SESSION["old"] = $data;
      redirect(route("user.register"));
      return;
    }

    try {
      $user = User::register($data);
      $_SESSION["user_id"] = $user["id"];
      redirect(route("user.dashboard"));
    } catch (\Exception $e) {
      $_SESSION["errors"]["form"] = $e->getMessage();
      $_SESSION["old"] = $data;
      redirect(route("user.register"));
    }
  }
}
?>
