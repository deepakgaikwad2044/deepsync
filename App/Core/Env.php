<?php

namespace App\Core;

class Env
{
    protected static array $data = [];

    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);

            $key = trim($key);
            $value = trim($value);
            $value = trim($value, "\"'");

            self::$data[$key] = $value;

            putenv("$key=$value");

            $_ENV[$key] = $value;
            $_SERVER[$key] = $value; // 🔥 added
        }
    }

    public static function get(string $key, $default = null)
    {
        $value = self::$data[$key] 
            ?? $_ENV[$key] 
            ?? $_SERVER[$key] 
            ?? getenv($key) 
            ?? $default;

        return match (strtolower((string)$value)) {
            'true'  => true,
            'false' => false,
            'null'  => null,
            default => is_numeric($value) ? $value + 0 : $value,
        };
    }
}
