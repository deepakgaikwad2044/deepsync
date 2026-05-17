<?php

namespace App\Core\Components;

class Registry
{
    protected static ?ComponentManager $manager = null;

    public static function setManager(ComponentManager $manager)
    {
        self::$manager = $manager;
    }

    public static function manager(): ComponentManager
    {
        return self::$manager;
    }

    public static function register(string $name, string $view)
    {
        self::$manager?->register($name, $view);
    }
}