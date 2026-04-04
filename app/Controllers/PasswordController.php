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

    // Initialize errors array
    $errors = [];

    if (empty($cur_pass)) {
      $errors["cpass"] = "Current password is required";
    }

    if (!empty($errors)) {
      // Save errors and old input in session, then redirect back
      $_SESSION["errors"] = $errors;
      $_SESSION["old"] = ["cpass" => $cur_pass];
      redirect(route("user.password.edit"));
    }

    $auth_id = Auth::id();

    // ORM query to get user by id
    $user = User::query()
      ->where("id", $auth_id)
      ->firstRaw();

    if (!$user) {
      $errors["form"] = "User not found";
      $_SESSION["errors"] = $errors;
      redirect(route("user.password.edit"));
    }

    $db_pass = $user["password"];

    if (password_verify($cur_pass, $db_pass)) {
      $_SESSION["success"] =
        "Password verified, you can now change your password.";
      // Or store a session flag to allow update form to show:
      $_SESSION["verified"] = true;

      redirect(route("user.password.edit"));
    } else {
      $errors["cpass"] = "Password not matched";
      $_SESSION["errors"] = $errors;
      $_SESSION["old"] = ["cpass" => $cur_pass];
      redirect(route("user.password.edit"));
    }
  }

  public function passwordUpdate()
  {
    $new_pass = trim($_POST["npass"] ?? "");

    // Validate password is not empty
    if (empty($new_pass)) {
      $_SESSION["err_pass"] = "Password should not be blank";
      redirect(route("user.password.edit"));
    }

    // Validate minimum length
    if (strlen($new_pass) < 6) {
      $_SESSION["err_pass"] = "Password must be at least 6 characters long.";
      redirect(route("user.password.edit"));
    }

    $auth_id = Auth::id();

    // Fetch user record
    $user = User::query()
      ->where("id", $auth_id)
      ->firstRaw();

    if (!$user) {
      $_SESSION["err_pass"] = "User not found";
      redirect(route("user.password.edit"));
    }

    // Prevent new password same as current
    if (password_verify($new_pass, $user["password"])) {
      $_SESSION["err_pass"] =
        "Current password should not be the same as the new password";
      redirect(route("user.password.edit"));
    }

    // Hash new password
    $password_hash = password_hash($new_pass, PASSWORD_BCRYPT);

    // Update user password
    $res = User::update($auth_id, ["password" => $password_hash]);

    if ($res) {
      $_SESSION["success"] = "Password successfully updated";
      // Clear verification flags after update
      unset($_SESSION["verified"]);
      unset($_SESSION["pass_match"]);
    } else {
      $_SESSION["err_pass"] = "Password not updated";
      unset($_SESSION["verified"]);
      unset($_SESSION["pass_match"]);
    }

    redirect(route("user.dashboard"));
  }
}
?>
