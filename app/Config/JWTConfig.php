<?php
namespace App\Config;

class JWTConfig
{
     public const SECRET = '8e44ad$DeepSync!2025#SecretKey@12345';

  public const ALGO = "HS256";
  public const EXPIRE = 120; // 2 minutes
}
