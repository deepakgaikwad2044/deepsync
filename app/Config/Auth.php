<?php
namespace App\Config;

use App\Models\User;
use App\Services\AuthorizationService;

class Auth
{
    protected static $user = null;
    protected static ?AuthorizationService $authorization = null;

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
     * Get legacy role value.
     * Do not use this method for RBAC authorization.
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
        return self::user() !== null;
    }

    /**
     * Get the authorization service.
     */
    protected static function authorizationService(): AuthorizationService
    {
        if (self::$authorization === null) {
            self::$authorization = new AuthorizationService();
        }

        return self::$authorization;
    }

    /**
     * Get all roles assigned to the authenticated user.
     */
    public static function roles(): array
    {
        $userId = (int) (self::id() ?? 0);

        if ($userId <= 0) {
            return [];
        }

        return self::authorizationService()->rolesForUser($userId);
    }

    /**
     * Check an assigned role.
     */
    public static function hasRole(string $roleSlug): bool
    {
        $userId = (int) (self::id() ?? 0);

        if ($userId <= 0) {
            return false;
        }

        return self::authorizationService()->hasRole(
            $userId,
            $roleSlug
        );
    }

    /**
     * Check an assigned permission.
     */
    public static function hasPermission(string $permissionSlug): bool
    {
        $userId = (int) (self::id() ?? 0);

        if ($userId <= 0) {
            return false;
        }

        return self::authorizationService()->hasPermission(
            $userId,
            $permissionSlug
        );
    }

    /**
     * Check whether the user has at least one permission.
     */
    public static function hasAnyPermission(array $permissions): bool
    {
        $userId = (int) (self::id() ?? 0);

        if ($userId <= 0) {
            return false;
        }

        return self::authorizationService()->hasAnyPermission(
            $userId,
            $permissions
        );
    }

    /**
     * Check whether the user has every permission.
     */
    public static function hasAllPermissions(array $permissions): bool
    {
        $userId = (int) (self::id() ?? 0);

        if ($userId <= 0) {
            return false;
        }

        return self::authorizationService()->hasAllPermissions(
            $userId,
            $permissions
        );
    }

    /**
     * Logout
     */
    public static function logout()
    {
        self::$user = null;
        self::$authorization = null;

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

    /**
     * Attempt login
     */
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

        unset($user["password"]);

        self::$user = $user;
        self::$authorization = null;

        return $user;
    }
}
