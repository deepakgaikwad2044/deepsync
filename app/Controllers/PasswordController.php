<?php
namespace App\Controllers;
use App\Models\User;

// Import the Auth class
use App\Config\Auth;

class PasswordController
{
    public function index()
    {
        return view("auth.password");
    }

    public function passwordVerify()
    {
        $cur_pass = trim($_POST["cpass"] ?? "");

        $errors = [];

        if ($cur_pass === "") {
            $errors["cpass"] = "Current password is required";
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            $_SESSION["old"] = ["cpass" => $cur_pass];
            $_SESSION["verified"] = false;

            redirect(route("user.password.edit"));
            return;
        }

        $user = User::query()
            ->where("id", Auth::id())
            ->firstRaw();

        if (!$user) {
            $_SESSION["errors"]["form"] = "User not found";
            $_SESSION["verified"] = false;
            redirect(route("user.password.edit"));
            return;
        }

        if (password_verify($cur_pass, $user["password"])) {
            $_SESSION["success"] = "Password verified successfully";
            $_SESSION["verified"] = true;
            unset($_SESSION["errors"]);
        } else {
            $_SESSION["errors"]["cpass"] = "Password not matched";
            $_SESSION["verified"] = false;
        }

        redirect(route("user.password.edit"));
    }

    public function passwordUpdate()
    {
        $new_pass = trim($_POST["npass"] ?? "");

        $errors = [];

        if ($new_pass === "") {
            $errors["npass"] = "Password should not be blank";
        } elseif (strlen($new_pass) < 6) {
            $errors["npass"] = "Password must be at least 6 characters";
        }

        if (!empty($errors)) {
            $_SESSION["errors"] = $errors;
            $_SESSION["verified"] = true;
            redirect(route("user.password.edit"));
            return;
        }

        $user = User::query()
            ->where("id", Auth::id())
            ->firstRaw();

        if (!$user) {
            $_SESSION["errors"]["form"] = "User not found";
            redirect(route("user.password.edit"));
            return;
        }

        // 🔥 SAME PASSWORD CHECK FIX (MAIN BUG WAS HERE)
        if (password_verify($new_pass, $user["password"])) {
            $_SESSION["errors"]["npass"] = "New password cannot be same as old password";
            redirect(route("user.password.edit"));
            return;
        }

        $hash = password_hash($new_pass, PASSWORD_BCRYPT);

        $res = User::update(Auth::id(), [
            "password" => $hash
        ]);

        if ($res) {
            $_SESSION["success"] = "Password updated successfully";
            unset($_SESSION["verified"]);
            unset($_SESSION["errors"]);
        } else {
            $_SESSION["errors"]["form"] = "Password update failed";
        }

        redirect(route("user.dashboard"));
    }
}

