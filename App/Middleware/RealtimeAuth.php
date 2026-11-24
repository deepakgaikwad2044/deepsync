<?php

namespace App\Middleware;

class RealtimeAuth
{
    public function handle($request, $next)
    {
        $token = $request->header('X-REALTIME-TOKEN') 
            ?? $_GET['token'] 
            ?? null;

        $validToken = getenv('REALTIME_TOKEN') ?: 'DEEPSYNC_SECURE';

        if (!$token || !hash_equals($validToken, $token)) {

            http_response_code(401);

            echo json_encode([
                "status" => false,
                "message" => "Unauthorized realtime access"
            ]);

            exit;
        }

        return $next($request);
    }
}
