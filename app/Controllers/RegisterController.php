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
            $_SESSION["old"] = $data;

            redirect(route("user.register"));
            return;
        }

        // 🔥 normalize input
        $name = trim($data["name"]);
        $email = strtolower(trim($data["email"]));
        $password = $data["password"];

        try {
            $user = User::register([
                "name" => $name,
                "email" => $email,
                "password" => password_hash($password, PASSWORD_BCRYPT),
            ]);

            $_SESSION["user_id"] = $user["id"];

            redirect(route("user.dashboard"));
            return;

        } catch (\Exception $e) {
            $_SESSION["errors"]["form"] = $e->getMessage();
            $_SESSION["old"] = $data;

            redirect(route("user.register"));
            return;
        }
    }
}
?>
