<?php

namespace App\Middleware;

use App\Config\Auth;
use App\Config\JWTAuth;

class RoleMiddleware
{
    public function handle(?string $roleParameter, bool $isWeb = false): void
    {
        $role = is_string($roleParameter) ? trim($roleParameter) : "";

        if ($role === "" || !preg_match("/^[a-z0-9][a-z0-9_-]*$/iD", $role)) {
            $this->deny($isWeb, 403, "Forbidden");
        }

        if ($isWeb) {
            if (!Auth::check()) {
                redirect(route("user.login"));
                exit;
            }
            if (!Auth::hasRole($role)) {
                $this->deny(true, 403, "Forbidden");
            }
            return;
        }

        if (!JWTAuth::check()) {
            $this->deny(false, 401, "Unauthenticated");
        }
        if (!JWTAuth::hasRole($role)) {
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
