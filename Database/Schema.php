<?php

namespace Database;

class Schema
{
    protected static $pdo;

    public static function setConnection($pdo)
    {
        self::$pdo = $pdo;
    }

public static function create($table, $callback)
{
    try {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $sql = "CREATE TABLE $table (" . implode(', ', $blueprint->columns) . ")";
        self::$pdo->exec($sql);

        echo "✅ Table '$table' created\n";

    } catch (\PDOException $e) {

        if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1050) {
            echo "❌ Table '$table' already exists\n";
        } else {
            echo "❌ " . $e->getMessage() . "\n";
        }
    }
}
    
    public static function hasTable($table)
{
    $stmt = self::$pdo->query("SHOW TABLES LIKE '$table'");
    return $stmt->rowCount() > 0;
}

    public static function drop($table)
    {
        self::$pdo->exec("DROP TABLE $table");
    }

    public static function dropIfExists($table)
    {
        self::$pdo->exec("DROP TABLE IF EXISTS $table");
    }
}