<?php
namespace App\Config;

use App\Models\User;

class Auth
{
  protected static $user = null;

  /**
   * Get authenticated user
   */
  public static function user()
  {
    if (!isset($_SESSION["user_id"])) {
      return null;
    }

    if (self::$user === null) {
      self::$user = User::findById($_SESSION["user_id"]);
    }

    return self::$user;
  }

  /**
   * Get authenticated user ID
   */
  public static function id()
  {
    return self::user()["id"] ?? null;
  }

  /**
   * Get authenticated user name
   */
  public static function username()
  {
    return self::user()["name"] ?? null;
  }

  /**
   * Get avatar
   */
  public static function avatar()
  {
    return self::user()["avtar"] ?? null;
  }

  /**
   * Get role
   */
  public static function role()
  {
    return self::user()["role"] ?? null;
  }

  /**
   * Check login
   */
  public static function check()
  {
    return isset($_SESSION["user_id"]);
  }

  /**
   * Logout
   */
  public static function logout()
  {
    unset($_SESSION["user_id"]);
    self::$user = null;
  }

  public static function attempt(array $credentials)
  {
    $email = $credentials["email"] ?? null;
    $password = $credentials["password"] ?? null;

    if (!$email || !$password) {
      return false;
    }

    // Find user by email
    $user = User::Query()
      ->where("email", $email)
      ->firstRaw();

    if (!$user) {
      return false;
    }

    // Verify password (assuming passwords hashed)
    if (!password_verify($password, $user["password"])) {
      return false;
    }

    // Login success: set session & cache user
    $_SESSION["user_id"] = $user["id"];
    self::$user = $user;

    return $user;
  }
}
