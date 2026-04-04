<?php
namespace App\Middleware;

use App\Helpers\JWTHelper;

class JWTMiddleware {

    public static function handle() {
        header('Content-Type: application/json');

        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Authorization header missing']);
            exit;
        }

        $authHeader = $headers['Authorization'];
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Token not provided']);
            exit;
        }

        $decoded = JWTHelper::validateToken($token);

        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token']);
            exit;
        }

        // User authenticated - you can access user data here
        $_REQUEST['auth_user'] = $decoded->data;
    }
}
