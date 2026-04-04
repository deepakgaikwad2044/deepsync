<?php

namespace Database;

use PDO;

class Migration
{
    protected static $pdo;

    public function __construct($pdo)
    {
        self::$pdo = $pdo;
        Schema::setConnection($pdo);
    }
}