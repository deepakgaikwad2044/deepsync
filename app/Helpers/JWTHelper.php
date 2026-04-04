<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Config\JWTConfig;
use Exception;

class JWTHelper
{
  public static function generateToken(array $data): string
  {
    $issuedAt = time();
    $expireAt = $issuedAt + JWTConfig::EXPIRE;

    $payload = [
      "iat" => $issuedAt,
      "exp" => $expireAt,
      "data" => $data,
    ];

    return JWT::encode($payload, JWTConfig::SECRET, JWTConfig::ALGO);
  }

  public static function validateToken(string $token)
  {
    try {
      $decoded = JWT::decode(
        $token,
        new Key(JWTConfig::SECRET, JWTConfig::ALGO)
      );
      return $decoded;
    } catch (Exception $e) {
      return null;
    }
  }
}
