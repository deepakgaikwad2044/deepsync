<?php
namespace App\Controllers;
use App\Models\User;
use App\Core\Validator;
use App\Core\Response;
use App\WebSockets\Realtime\Channels\UserChannel;

class RegisterController
{
    public function index()
    {
        view("auth.register");
    }

    public function register()
{
    $data = $_POST;

    // 🔥 normalize input first
    $data["name"] = trim($data["name"] ?? "");
    $data["email"] = strtolower(trim($data["email"] ?? ""));

    $validator = new Validator();

    $result = $validator->validate($data, [
        "name" => "required|len:3",
        "email" => "required|email|unique:users,email",
        "password" => "required|len:6|confirmed",
        "confirm_password" => "required",
    ]);

    // ❌ validation failed
    if (!$result["status"]) {

        $_SESSION["errors"] = $result["errors"];
        $_SESSION["old"] = $data;

        redirect(route("user.register"));
        return;
    }

    try {

        // ✅ register only ONCE
        $user = User::register([
            "name" => $data["name"],
            "email" => $data["email"],
            "password" => $data["password"],
        ]);

        if (!$user) {

            Response::error("user did not register");
            return;
        }

        // ✅ stats
        $inactive_users = User::query()
            ->where("account_status", 0)
            ->count();

        $total_user = User::query()->count();

        // ✅ realtime channel
        $UserChannel = new UserChannel();

        $UserChannel->send("userupdated", [
            "inactive_users" => $inactive_users,
            "total_user" => $total_user,
        ]);

        // ✅ login user
        $_SESSION["user_id"] = $user["id"];

        redirect(route("user.dashboard"));
      

    } catch (\Exception $e) {

        $_SESSION["errors"]["form"] = $e->getMessage();
        $_SESSION["old"] = $data;

        redirect(route("user.register"));
        return;
    }
}
}
?>
