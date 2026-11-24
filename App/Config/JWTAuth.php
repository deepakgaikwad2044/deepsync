<?php
namespace App\Config;

use App\Services\AuthorizationService;

class JWTAuth
{
    protected static ?array $user = null;
    protected static ?AuthorizationService $authorization = null;

    public static function setUser(?array $user): void
    {
        $id = $user["id"] ?? null;
        if ((!is_int($id) && !(is_string($id) && ctype_digit($id))) || (int) $id <= 0) {
            self::reset();
            return;
        }
        self::$user = $user;
        self::$authorization = null;
    }

    public static function user(): ?array
    {
        return self::$user;
    }

    public static function id(): ?int
    {
        if (self::$user === null) return null;
        $id = self::$user["id"] ?? null;
        if ((!is_int($id) && !(is_string($id) && ctype_digit($id))) || (int) $id <= 0) return null;
        return (int) $id;
    }

    public static function check(): bool
    {
        return self::id() !== null;
    }

    protected static function authorizationService(): AuthorizationService
    {
        if (self::$authorization === null) {
            self::$authorization = new AuthorizationService();
        }
        return self::$authorization;
    }

    public static function roles(): array
    {
        $id = self::id();
        return $id === null ? [] : self::authorizationService()->rolesForUser($id);
    }

    public static function hasRole(string $roleSlug): bool
    {
        $id = self::id();
        return $id !== null && self::authorizationService()->hasRole($id, $roleSlug);
    }

    public static function hasPermission(string $permissionSlug): bool
    {
        $id = self::id();
        return $id !== null && self::authorizationService()->hasPermission($id, $permissionSlug);
    }

    public static function hasAnyPermission(array $permissions): bool
    {
        $id = self::id();
        return $id !== null && self::authorizationService()->hasAnyPermission($id, $permissions);
    }

    public static function hasAllPermissions(array $permissions): bool
    {
        $id = self::id();
        return $id !== null && self::authorizationService()->hasAllPermissions($id, $permissions);
    }

    public static function reset(): void
    {
        self::$user = null;
        self::$authorization = null;
    }
}
