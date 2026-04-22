<?php

namespace Database;

class Blueprint
{
    public $table;
    public $columns = [];
    public $indexes = [];

    protected $lastIndex;
    protected $lastColumn;

    protected $refColumn;   // FK reference column
    protected $refTable;    // FK reference table
    protected $engine;
    protected $charset;
    protected $collation;
    protected $afterColumn = null; 

    public function __construct($table)
    {
        $this->table = $table;

        // 👇 ENV values fallback
        $this->engine    = env('DB_ENGINE') ?? 'InnoDB';
        $this->charset   = env('DB_CHARSET') ?? 'utf8mb4';
        $this->collation = env('DB_COLLATION') ?? 'utf8mb4_unicode_ci';
    }

    // 🔐 Getters
    public function getEngine() { return $this->engine; }
    public function getCharset() { return $this->charset; }
    public function getCollation() { return $this->collation; }

    // Optional overrides
    public function engine($engine) { $this->engine = strtoupper($engine); return $this; }
    public function charset($charset) { $this->charset = $charset; return $this; }
    public function collation($collation) { $this->collation = $collation; return $this; }

    // 🆔 ID

public function id($unsigned = true)
{
    $col = "id INT AUTO_INCREMENT PRIMARY KEY";
    if ($unsigned) {
        $col = "id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
    }
    $this->columns[] = $col;
    $this->lastIndex = count($this->columns) - 1;
    $this->lastColumn = "id";
    return $this;
}

// 🔢 INT
public function int($column, $unsigned = false)
{
    $col = "$column INT";
    if ($unsigned) $col .= " UNSIGNED";
    $this->columns[] = $col;
    $this->lastIndex = count($this->columns) - 1;
    $this->lastColumn = $column;
    return $this;
}


// 🔢 UNSIGNED INT
public function unsigned()
{
    // sirf last column ke liye kaam kare
    $this->columns[$this->lastIndex] = str_replace("INT", "INT UNSIGNED", $this->columns[$this->lastIndex]);
    return $this;
}

    // 🔤 STRING
    public function string($column, $length = 255)
    {
        $this->columns[] = "$column VARCHAR($length)";
        $this->lastIndex = count($this->columns) - 1;
        $this->lastColumn = $column;
        return $this;
    }

 
    // 📝 TEXT
    public function text($column)
    {
        $this->columns[] = "$column TEXT";
        $this->lastIndex = count($this->columns) - 1;
        $this->lastColumn = $column;
        return $this;
    }

    // 🔢 TINYINT
    public function tinyint($column, $length = 1)
    {
        $this->columns[] = "$column TINYINT($length)";
        $this->lastIndex = count($this->columns) - 1;
        $this->lastColumn = $column;
        return $this;
    }

    // 🎯 ENUM
    public function enum($column, array $values)
    {
        $vals = "'" . implode("','", $values) . "'";
        $this->columns[] = "$column ENUM($vals)";
        $this->lastIndex = count($this->columns) - 1;
        $this->lastColumn = $column;
        return $this;
    }

    // 📅 DATE
    public function date($column)
    {
        $this->columns[] = "$column DATE";
        $this->lastIndex = count($this->columns) - 1;
        $this->lastColumn = $column;
        return $this;
    }

    // 🕒 DATETIME
    public function datetime($column)
    {
        $this->columns[] = "$column DATETIME";
        $this->lastIndex = count($this->columns) - 1;
        $this->lastColumn = $column;
        return $this;
    }

    // ⭐ DEFAULT
    public function default($value)
    {
        if (strtoupper($value) === 'CURRENT_TIMESTAMP') {
            $this->columns[$this->lastIndex] .= " DEFAULT CURRENT_TIMESTAMP";
        } else {
            if (is_string($value)) $value = "'$value'";
            $this->columns[$this->lastIndex] .= " DEFAULT $value";
        }
        return $this;
    }

    // ❓ NULLABLE
    public function nullable()
    {
        $this->columns[$this->lastIndex] .= " NULL";
        return $this;
    }

    // 🔒 UNIQUE
    public function unique()
    {
        $this->indexes[] = "UNIQUE ({$this->lastColumn})";
        return $this;
    }

    // ⚡ INDEX
    public function index()
    {
        $this->indexes[] = "INDEX ({$this->lastColumn})";
        return $this;
    }

    // 🔗 FOREIGN KEY
    public function foreign($column)
    {
        $this->lastColumn = $column;
        return $this;
    }

    public function references($refColumn)
    {
        $this->refColumn = $refColumn;
        return $this;
    }

    public function on($table)
    {
        $this->refTable = $table;
        $this->indexes[] = "FOREIGN KEY ({$this->lastColumn}) REFERENCES {$table}({$this->refColumn})";
        return $this;
    }

    // 🔥 CASCADE SUPPORT
    public function onDelete($action)
    {
        $last = count($this->indexes) - 1;
        $this->indexes[$last] .= " ON DELETE " . strtoupper($action);
        return $this;
    }

    public function onUpdate($action)
    {
        $last = count($this->indexes) - 1;
        $this->indexes[$last] .= " ON UPDATE " . strtoupper($action);
        return $this;
    }

    // ⏱ TIMESTAMPS
    public function timestamps()
    {
        $this->columns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
    }

 
  public function name($fkName)
{
    $last = count($this->indexes) - 1;
    
    if ($last < 0) return $this; 

    $currentSql = $this->indexes[$last];

    if (strpos($currentSql, 'CONSTRAINT') === false) {
        $this->indexes[$last] = str_replace(
            "FOREIGN KEY", 
            "CONSTRAINT `$fkName` FOREIGN KEY", 
            $currentSql
        );
    }

    return $this;
}


    // 🔹 New method: after()
    public function after($columnName)
    {
        $this->afterColumn = $columnName; // store reference
        // move last added column after $columnName in $columns array
        $last = array_pop($this->columns); // remove last temporarily
        $pos = array_search($columnName, array_map(function($c){
            return preg_replace('/ .*/','',$c); // get column names only
        }, $this->columns));
        if ($pos === false) $pos = count($this->columns)-1; // if not found, append at end
        array_splice($this->columns, $pos+1, 0, [$last]); // insert after
        $this->lastIndex = $pos+1;
        return $this;
    }

    // 🔹 Helper to add column (used by int, string, etc.)
    protected function addColumn($definition)
    {
        if ($this->afterColumn) {
            $last = $definition;
            $pos = array_search($this->afterColumn, array_map(function($c){
                return preg_replace('/ .*/','',$c);
            }, $this->columns));
            array_splice($this->columns, $pos+1, 0, [$last]);
            $this->lastIndex = $pos+1;
            $this->afterColumn = null;
        } else {
            $this->columns[] = $definition;
            $this->lastIndex = count($this->columns)-1;
        }
        $this->lastColumn = preg_replace('/ .*/','',$definition);
    }

}