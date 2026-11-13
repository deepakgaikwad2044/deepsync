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
    self::$user = null;

    if (session_status() !== PHP_SESSION_ACTIVE) {
      return;
    }

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      if (!headers_sent()) {
        $cookieOptions = [
          "expires" => time() - 42000,
          "path" => $params["path"],
          "domain" => $params["domain"],
          "secure" => $params["secure"],
          "httponly" => $params["httponly"],
        ];

        if (!empty($params["samesite"])) {
          $cookieOptions["samesite"] = $params["samesite"];
        }

        setcookie(session_name(), "", $cookieOptions);
      }
    }

    session_destroy();
  }


  public static function attempt(array $credentials)
    {
        $email = $credentials["email"] ?? null;
        $password = $credentials["password"] ?? null;

        if (!$email || !$password) {
            return false;
        }

        $user = User::query()
            ->where("email", $email)
            ->firstRaw();

        if (!$user || !password_verify($password, $user["password"])) {
            return false;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);

            unset($_SESSION["csrf_token"]);
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }

        $_SESSION["user_id"] = $user["id"];

        // Never expose or cache the password
        unset($user["password"]);

        self::$user = $user;

        return $user;
    }
}
