<?php

namespace App\WebSockets\Realtime;

class AuthGuard
{
    public static function check(?string $token): bool
    {
        if (!$token) return false;

        $validToken = 'zserfcygbijmkpl'; // hardcode first

        return hash_equals($validToken, $token);
    }


    /**
     * Optional: Validate private channel access
     * Example: private-user-5
     */
    public static function authorizeChannel(string $channel, array $user = []): bool
    {
        // Example private rule
        if (str_starts_with($channel, 'private-user-')) {

            $userId = str_replace('private-user-', '', $channel);

            if (!isset($user['id'])) {
                return false;
            }

            return (string)$user['id'] === $userId;
        }

        return true; // public channels allowed
    }
}
