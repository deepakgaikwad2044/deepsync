<?php

namespace App\Core\Console;

class CLI
{
    protected static function format($text, $colorCode)
    {
        return "\033[" . $colorCode . "m" . $text . "\033[0m";
    }

    public static function info($text)
    {
        echo self::format("ℹ " . $text, "36") . PHP_EOL; // Cyan
    }

    public static function success($text)
    {
        echo self::format("✔ " . $text, "32") . PHP_EOL; // Green
    }

    public static function error($text)
    {
        echo self::format("✖ " . $text, "31") . PHP_EOL; // Red
    }

    public static function warning($text)
    {
        echo self::format("⚠️ " . $text, "33") . PHP_EOL; // Yellow
    }

    public static function line($text)
    {
        echo $text . PHP_EOL;
    }
}