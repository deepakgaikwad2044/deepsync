<?php

namespace Database;

class Blueprint
{
    public $table;
    public $columns = [];
    protected $lastIndex;

    public function __construct($table)
    {
        $this->table = $table;
    }

    // 🆔 ID
    public function id()
    {
        $this->columns[] = "id INT AUTO_INCREMENT PRIMARY KEY";
        $this->lastIndex = count($this->columns) - 1;
        return $this;
    }

    // 🔤 STRING
    public function string($column, $length = 255)
    {
        $this->columns[] = "$column VARCHAR($length)";
        $this->lastIndex = count($this->columns) - 1;
        return $this;
    }

    // 🔢 INTEGER
    public function int($column)
    {
        $this->columns[] = "$column INT";
        $this->lastIndex = count($this->columns) - 1;
        return $this;
    }

    // ✅ BOOLEAN (0 / 1)
    public function boolean($column)
    {
        $this->columns[] = "$column TINYINT(1)";
        $this->lastIndex = count($this->columns) - 1;
        return $this;
    }

    // ⏱ TIMESTAMPS (FIXED 🔥)
    public function timestamps()
    {
        $this->columns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
    }

    // ⭐ DEFAULT VALUE
    public function default($value)
    {
        if (is_string($value)) {
            $value = "'$value'";
        }

        $this->columns[$this->lastIndex] .= " DEFAULT $value";
        return $this;
    }

    // ❓ NULLABLE
    public function nullable()
    {
        $this->columns[$this->lastIndex] .= " NULL";
        return $this;
    }
}