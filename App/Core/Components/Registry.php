<?php

namespace App\Core\Components;

class Registry
{
    protected static ?ComponentManager $manager = null;

    public static function setManager(ComponentManager $manager): void
    {
        self::$manager = $manager;
    }

    public static function manager(): ComponentManager
    {
        if (!self::$manager) {
            throw new \RuntimeException("ComponentManager not initialized");
        }

        return self::$manager;
    }

    public static function register(string $name, string $view): void
    {
        self::manager()->register($name, $view);
    }
}