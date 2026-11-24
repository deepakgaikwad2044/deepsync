<?php
namespace App\Middleware;

use App\Config\JWTAuth;
use App\Helpers\JWTHelper;
use App\Models\User;

class JWTMiddleware
{
    public static function handle()
    {
        JWTAuth::reset();
        header("Content-Type: application/json");
        $headers = function_exists("getallheaders") ? getallheaders() : [];
        if (!is_array($headers)) { $headers = []; }
        $authHeader = null;
        foreach ($headers as $name => $value) {
            if (is_string($name) && strtolower($name) === "authorization") {
                $authHeader = $value;
                break;
            }
        }
        if ($authHeader === null) {
            $authHeader = $_SERVER["HTTP_AUTHORIZATION"] ?? $_SERVER["REDIRECT_HTTP_AUTHORIZATION"] ?? null;
        }

        if (!is_string($authHeader) || $authHeader === "") {
            http_response_code(401);
            echo json_encode(["error" => "Authorization header missing"]);
            exit;
        }
        if (preg_match("/^Bearer\\s+([^\\s]+)$/iD", trim($authHeader), $matches)) {
            $token = $matches[1];
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Token not provided"]);
            exit;
        }

        $decoded = JWTHelper::validateToken($token);
        if (!$decoded || !isset($decoded->data) || !is_object($decoded->data)) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid or expired token"]);
            exit;
        }

        $userId = $decoded->data->id ?? null;
        if ((!is_int($userId) && !(is_string($userId) && ctype_digit($userId))) || (int) $userId <= 0) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid token identity"]);
            exit;
        }

        try {
            $user = User::findById((int) $userId);
        } catch (\Throwable $e) {
            http_response_code(503);
            echo json_encode(["error" => "Authentication service unavailable"]);
            exit;
        }

        if (!$user) {
            http_response_code(401);
            echo json_encode(["error" => "Authenticated user not found"]);
            exit;
        }

        JWTAuth::setUser($user);
        if (!JWTAuth::check()) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid token identity"]);
            exit;
        }
        $_REQUEST["auth_user"] = $decoded->data;
    }
}
