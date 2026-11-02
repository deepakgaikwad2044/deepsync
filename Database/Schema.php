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

            $sql = "CREATE TABLE $table (";
            $sql .= implode(', ', $blueprint->columns);

            if (!empty($blueprint->indexes)) {
                $sql .= ", " . implode(', ', $blueprint->indexes);
            }

            $sql .= ") ENGINE=" . $blueprint->getEngine() . "
                     DEFAULT CHARSET=" . $blueprint->getCharset() . "
                     COLLATE=" . $blueprint->getCollation();

            self::$pdo->exec($sql);

            echo "✅ Table '$table' created\n";

        } catch (\PDOException $e) {

            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1050) {
                echo "❌ Table '$table' already exists\n";
            } else {
                echo "❌ " . $e->getMessage() . "\n";
            }

            // Prevent migration from being recorded as successful
            throw $e;
        }
    }

    public static function hasTable($table)
    {
        $stmt = self::$pdo->query(
            "SHOW TABLES LIKE " . self::$pdo->quote($table)
        );

        return $stmt->rowCount() > 0;
    }

    public static function drop($table)
    {
        self::$pdo->exec("DROP TABLE $table");
    }

    public static function dropIfExists($table)
    {
        $previousState = self::$pdo
            ->query("SELECT @@FOREIGN_KEY_CHECKS")
            ->fetchColumn();

        self::$pdo->exec("SET FOREIGN_KEY_CHECKS=0");

        try {
            self::$pdo->exec("DROP TABLE IF EXISTS $table");
        } finally {
            self::$pdo->exec(
                "SET FOREIGN_KEY_CHECKS=" . (int) $previousState
            );
        }
    }
}
