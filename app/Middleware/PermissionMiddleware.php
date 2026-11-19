<?php

namespace App\Middleware;

use App\Config\Auth;
use App\Config\JWTAuth;

class PermissionMiddleware
{
    public function handle(?string $permissionParameter, bool $isWeb = false): void
    {
        $permission = is_string($permissionParameter) ? trim($permissionParameter) : "";

        if ($permission === "" || preg_match("/\A.{1,100}\z/usD", $permission) !== 1 || preg_match("/[\x00-\x1F\x7F]/", $permission) === 1) {
            $this->deny($isWeb, 403, "Forbidden");
        }

        if ($isWeb) {
            if (!Auth::check()) {
                redirect(route("user.login"));
                exit;
            }
            if (!Auth::hasPermission($permission)) {
                $this->deny(true, 403, "Forbidden");
            }
            return;
        }

        if (!JWTAuth::check()) {
            $this->deny(false, 401, "Unauthenticated");
        }
        if (!JWTAuth::hasPermission($permission)) {
            $this->deny(false, 403, "Forbidden");
        }
    }

    private function deny(bool $isWeb, int $status, string $message): void
    {
        http_response_code($status);
        if ($isWeb) {
            echo $message;
        } else {
            header("Content-Type: application/json");
            echo json_encode(["error" => $message]);
        }
        exit;
    }
}
