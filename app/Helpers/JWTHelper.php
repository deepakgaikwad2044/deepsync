<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Config\JWTConfig;

class JWTHelper
{
    public static function generateToken(array $data): string
    {
        $issuedAt = time();
        $expireAt = $issuedAt + JWTConfig::EXPIRE;

        $payload = [
            'iss' => JWTConfig::issuer(),
            'aud' => JWTConfig::AUDIENCE,
            'iat' => $issuedAt,
            'exp' => $expireAt,
            'data' => $data,
        ];

        return JWT::encode(
            $payload,
            JWTConfig::secret(),
            JWTConfig::ALGO
        );
    }

    public static function validateToken(string $token)
    {
        try {
            $decoded = JWT::decode(
                $token,
                new Key(JWTConfig::secret(), JWTConfig::ALGO)
            );

            if (
                !is_object($decoded) ||
                !isset($decoded->iss) ||
                !is_string($decoded->iss) ||
                $decoded->iss !== JWTConfig::issuer()
            ) {
                return null;
            }

            if (
                !isset($decoded->aud) ||
                !is_string($decoded->aud) ||
                $decoded->aud !== JWTConfig::AUDIENCE
            ) {
                return null;
            }

            return $decoded;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
