<?php

namespace App\Config;

use App\Core\Env;

class JWTConfig
{
    public static function secret(): string
    {
        $secret = Env::get('JWT_SECRET');

        if (!is_string($secret) || strlen($secret) < 64) {
            throw new \RuntimeException(
                'JWT_SECRET must be configured with at least 64 characters.'
            );
        }

        return $secret;
    }

    public static function issuer(): string
    {
        $issuer = Env::get('APP_URL');

        if (!is_string($issuer) || $issuer === '') {
            throw new \RuntimeException(
                'APP_URL must be configured for JWT issuer validation.'
            );
        }

        $parts = parse_url($issuer);

        if (
            !is_array($parts) ||
            !isset($parts['scheme'], $parts['host']) ||
            !in_array(strtolower($parts['scheme']), ['http', 'https'], true) ||
            isset($parts['user']) ||
            isset($parts['pass']) ||
            isset($parts['query']) ||
            isset($parts['fragment'])
        ) {
            throw new \RuntimeException(
                'APP_URL must be a valid absolute HTTP(S) URL.'
            );
        }

        return $issuer;
    }

    public const AUDIENCE = 'deepsync-api';
    public const ALGO = 'HS256';
    public const EXPIRE = 120;
}
