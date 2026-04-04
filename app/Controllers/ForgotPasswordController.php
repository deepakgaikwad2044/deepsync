<?php

namespace App\Controllers;

use App\Models\User;
use App\Core\PasswordReset;
use App\Mail\DeepsyncMail;
use App\Core\Validator;
use App\Core\Response;

class ForgotPasswordController
{
  public function showForgotForm()
  {
    return view("auth.forgot-password");
  }

  public function sendResetLink()
  {
    $email = request("email");

    $validator = new Validator();

    $result = $validator->validate(
      ["email" => $email],
      [
        "email" => "required|email|exists:users,email",
      ]
    );

    if (!$result["status"]) {
      $_SESSION["errors"] = $result["errors"];
      $_SESSION["old"] = ["email" => $email];

      redirect(route("user.forgot.password"));
      return;
    }

    $token = bin2hex(random_bytes(32));
    $expiresAt = date("Y-m-d H:i:s", strtotime("+30 minutes"));

    // Delete old tokens
    PasswordReset::deleteWhere("email", $email);

    // Insert new token
    PasswordReset::create([
      "email" => $email,
      "token" => $token,
      "expires_at" => $expiresAt,
      "created_at" => date("Y-m-d H:i:s"),
    ]);

    $resetLink = env("APP_URL") . "/users/reset/password/$token";

    DeepsyncMail::send(
      $email,
      "Reset Your Password",
      "Click the link below to reset your password:\n\n$resetLink\n\nThis link expires in 30 minutes."
    );

    set_flash("success", "Password reset link sent to your email");
    redirect(route("user.forgot.password"));
  }

  public function showResetForm($token)
  {
    $record = PasswordReset::Query()
      ->where("token", $token)
      ->where("expires_at", ">", date("Y-m-d H:i:s"))
      ->first();

    if (!$record) {
      set_flash("error", "Reset link expired or invalid");
      redirect("/users/reset/password/$token");
    }

    return view("auth.reset-password", ["token" => $token]);
  }

  public function resetPassword()
  {
    $token = request("token");
    $validator = new Validator();

    $password = request("password");
    $confirm_password = request("confirm_password");

    $result = $validator->validate(
      [
        "password" => $password,
        "confirm_password" => $confirm_password,
      ],
      [
        "password" => "required|len:6|confirmed",
        "confirm_password" => "required",
      ]
    );

    $resetLinkURL = "/users/reset/password/$token";

    if (!$result["status"]) {
      $_SESSION["errors"] = $result["errors"];
      $_SESSION["old"] = $result;
      redirect($resetLinkURL);
      return;
    }

    $record = PasswordReset::Query()
      ->where("token", $token)
      ->first();
    if (!$record) {
      set_flash("error", "Invalid reset token");
      redirect($resetLinkURL);
    }

    User::updateWhere("email", $record["email"], [
      "password" => password_hash($password, PASSWORD_BCRYPT),
    ]);

    PasswordReset::deleteWhere("token", $token);

    set_flash("success", "Password reset successfully. Please login.");
    redirect(route("user.login"));
  }
}
