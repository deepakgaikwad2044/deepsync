<?php
namespace App\Core;

use App\Config\Database;
use PDO;
use ReflectionClass;

class Model
{
  protected static $table;
  protected static $primaryKey = "id";

  protected array $where = [];
  protected array $relations = [];
  protected array $with = [];
  protected string $select = "*";
  protected ?string $orderBy = null;
  protected ?int $limit = null;
  protected array $hidden = [];
  protected static array $searchable = [];
  protected array $groupBy = [];
  protected array $selects = ["*"];
  protected array $joins = [];

  protected static function db()
  {
    return new Database();
  }

  public static function __callStatic($method, $arguments)
  {
    if (!method_exists(static::class, $method)) {
      throw new \BadMethodCallException(
        "Method {$method} does not exist in " . static::class
      );
    }

    return (new static())->$method(...$arguments);
  }

  public function select($columns)
  {
    $this->select = is_array($columns) ? implode(", ", $columns) : $columns;
    return $this;
  }

  public function groupBy(string ...$columns): static
  {
    $this->groupBy = $columns;
    return $this;
  }

  public static function query(): static
  {
    return new static();
  }

  public function where($column, $operator, $value = null)
  {
    if ($value === null) {
      $this->where[] = [$column, "=", $operator];
    } else {
      $this->where[] = [$column, $operator, $value];
    }
    return $this;
  }

  public function with(...$relations)
  {
    $this->with = array_merge($this->with, $relations);
    return $this;
  }

  protected function guessForeignKey()
  {
    $class = strtolower((new ReflectionClass($this))->getShortName());
    return $class . "_id";
  }

  protected function relationName($model)
  {
    return strtolower((new ReflectionClass($model))->getShortName());
  }

  public function hasOne($model, $foreignKey = null, $localKey = null)
  {
    $this->relations[] = [
      "type" => "one",
      "model" => $model,
      "foreignKey" => $foreignKey ?? $this->guessForeignKey(),
      "localKey" => $localKey ?? static::$primaryKey,
      "name" => $this->relationName($model),
    ];
    return $this;
  }

  public function hasMany($model, $foreignKey = null, $localKey = null)
  {
    $this->relations[] = [
      "type" => "many",
      "model" => $model,
      "foreignKey" => $foreignKey ?? $this->guessForeignKey(),
      "localKey" => $localKey ?? static::$primaryKey,
      "name" => $this->relationName($model),
    ];
    return $this;
  }

  protected function applyHidden(&$row): void
  {
    foreach ($this->hidden as $field) {
      if (is_array($row) && array_key_exists($field, $row)) {
        unset($row[$field]);
      }

      if (is_object($row) && property_exists($row, $field)) {
        unset($row->$field);
      }
    }
  }

  public function join(
    string $table,
    string $first,
    string $operator,
    string $second
  ): static {
    $this->joins[] = "INNER JOIN {$table} ON {$first} {$operator} {$second}";
    return $this;
  }

  public function get()
  {
    $sql = "SELECT {$this->select} FROM " . static::$table;
    $params = [];

    // JOINS
    if (!empty($this->joins)) {
      $sql .= " " . implode(" ", $this->joins);
    }

    // WHERE
    if ($this->where) {
      $conditions = [];
      foreach ($this->where as [$col, $op, $val]) {
        $conditions[] = "{$col} {$op} ?";
        $params[] = $val;
      }
      $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    // GROUP BY
    if (!empty($this->groupBy)) {
      $sql .= " GROUP BY " . implode(", ", $this->groupBy);
    }

    if ($this->orderBy) {
      $sql .= " " . $this->orderBy;
    }

    if ($this->limit) {
      $sql .= " LIMIT " . $this->limit;
    }

    $stmt = static::db()->query($sql, $params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ✅ RELATIONS SQL KE BAAD LOAD HONGI
    if (!empty($this->with) && !empty($results)) {
      foreach ($results as &$row) {
        foreach ($this->with as $relation) {
          if (!method_exists(static::class, $relation)) {
            continue;
          }

          // new model instance banate hain
          $modelInstance = new static();

          $rel = $modelInstance->$relation();

          if (!is_array($rel)) {
            continue;
          }

          $model = $rel["model"];
          $foreignKey = $rel["foreignKey"];
          $ownerKey = $rel["ownerKey"];

          if (!isset($row[$foreignKey])) {
            $row[$relation] = null;
            continue;
          }

          $row[$relation] = $model
            ::query()
            ->where($ownerKey, $row[$foreignKey])
            ->first();
        }
      }
    }

    return $results;
  }

  public function first()
  {
    $this->limit = 1;
    $data = $this->get();
    return $data[0] ?? null;
  }

  // Instance find to keep with() relations intact
  public function find($id)
  {
    $this->where[] = [static::$primaryKey, "=", $id];
    return $this->first();
  }

  // Optional static helper for direct find without relations
  public static function findById($id)
  {
    return (new static())->find($id);
  }

  public static function findByEmail($email)
  {
    $table = static::$table;

    $sql = "SELECT * FROM {$table} WHERE email = ? LIMIT 1";

    $stmt = static::db()->query($sql, [$email]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
      foreach ((new static())->hidden as $field) {
        unset($data[$field]);
      }
    }

    return $data;
  }

  public function findByColumn($findByColumn, $value)
  {
    $table = static::$table;

    $sql = "SELECT * FROM {$table} WHERE $findByColumn = ? LIMIT 1";

    $stmt = static::db()->query($sql, [$value]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
      foreach ((new static())->hidden as $field) {
        unset($data[$field]);
      }
    }

    return $data;
  }

  public static function all()
  {
    return (new static())->get();
  }

  public static function collect(...$models): array
  {
    $data = [];

    // current model first (Country)
    $selfName = strtolower(
      (new ReflectionClass(static::class))->getShortName()
    );
    $data[$selfName] = static::all();

    foreach ($models as $model) {
      // allow string: "user"
      if (is_string($model)) {
        $class = "App\\Models\\" . ucfirst($model); // adjust if needed

        if (!class_exists($class)) {
          throw new \Exception("Model {$class} not found");
        }

        $model = $class;
      }

      $name = strtolower((new ReflectionClass($model))->getShortName());
      $data[$name] = $model::all();
    }

    return $data;
  }

  public function orderBy(string $column, string $direction = "ASC")
  {
    $direction = strtoupper($direction);
    if (!in_array($direction, ["ASC", "DESC"])) {
      $direction = "ASC";
    }

    $this->orderBy = "ORDER BY {$column} {$direction}";
    return $this;
  }

  public function firstRaw()
  {
    $sql = "SELECT {$this->select} FROM " . static::$table;
    $params = [];

    if ($this->where) {
      $conditions = [];
      foreach ($this->where as [$col, $op, $val]) {
        $conditions[] = "{$col} {$op} ?";
        $params[] = $val;
      }
      $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " LIMIT 1";

    $stmt = static::db()->query($sql, $params);
    return $stmt->fetch(PDO::FETCH_ASSOC); // raw single row
  }

  public function belongsTo($model, $foreignKey = null, $ownerKey = "id")
  {
    if ($foreignKey === null) {
      $parent = strtolower((new ReflectionClass($model))->getShortName());
      $foreignKey = $parent . "_id";
    }

    return [
      "type" => "belongsTo",
      "model" => $model,
      "foreignKey" => $foreignKey,
      "ownerKey" => $ownerKey,
    ];
  }

  /**
   * Insert new record
   */
  public static function create(array $data)
  {
    $table = static::$table;
    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), "?"));
    $values = array_values($data);

    $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
    $stmt = static::db()->query($sql, $values);

    return $stmt->rowCount() > 0;
  }

  /**
   * Delete records where column = value
   */
  public static function deleteWhere(string $column, $value)
  {
    $table = static::$table;
    $sql = "DELETE FROM {$table} WHERE {$column} = ?";
    $stmt = static::db()->query($sql, [$value]);

    return $stmt->rowCount() > 0;
  }

  /**
   * Update records where column = value
   */
  public static function updateWhere(string $column, $value, array $data)
  {
    $table = static::$table;
    $setParts = [];
    $values = [];
    foreach ($data as $col => $val) {
      $setParts[] = "{$col} = ?";
      $values[] = $val;
    }
    $values[] = $value;

    $setClause = implode(", ", $setParts);
    $sql = "UPDATE {$table} SET {$setClause} WHERE {$column} = ?";

    $stmt = static::db()->query($sql, $values);

    return $stmt->rowCount() > 0;
  }

  public static function update($id, array $data)
  {
    if (empty($data)) {
      return false;
    }

    $table = static::$table;
    $primaryKey = static::$primaryKey;

    $setParts = [];
    $values = [];

    foreach ($data as $column => $value) {
      $setParts[] = "{$column} = ?";
      $values[] = $value;
    }

    // id last me bind hoga
    $values[] = $id;

    $setClause = implode(", ", $setParts);

    $sql = "UPDATE {$table} SET {$setClause} WHERE {$primaryKey} = ?";

    $stmt = static::db()->query($sql, $values);

    return $stmt->rowCount() > 0;
  }

  public function count(string $column = "*"): int
  {
    $sql = "SELECT COUNT({$column}) as total FROM " . static::$table;
    $params = [];

    // JOINS
    if (!empty($this->joins)) {
      $sql .= " " . implode(" ", $this->joins);
    }

    // WHERE
    if (!empty($this->where)) {
      $conditions = [];

      foreach ($this->where as [$col, $op, $val]) {
        $conditions[] = "{$col} {$op} ?";
        $params[] = $val;
      }

      $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    // GROUP BY (optional support)
    if (!empty($this->groupBy)) {
      $sql .= " GROUP BY " . implode(", ", $this->groupBy);
    }

    $stmt = static::db()->query($sql, $params);

    // If GROUP BY is used, return number of grouped rows
    if (!empty($this->groupBy)) {
      return $stmt->rowCount();
    }

    return (int) $stmt->fetchColumn();
  }

  protected static function pluralize(string $word): string
  {
    // Get the class that called this method (child model)
    $calledClass = static::class;

    // Default exceptions for plural forms
    $defaultExceptions = [
      "Country" => "countries",
      "Progress" => "progress", // no plural change
    ];

    // Get plural exceptions from the child model if defined, else default
    $exceptions = property_exists($calledClass, "plural")
      ? $calledClass::$plural
      : $defaultExceptions;

    // Use exception if exists
    if (isset($exceptions[$word])) {
      return $exceptions[$word];
    }

    // Simple English pluralization rules
    $lowerWord = strtolower($word);
    $lastLetter = substr($lowerWord, -1);
    $lastTwo = substr($lowerWord, -2);

    if ($lastTwo === "ss") {
      // e.g. Progress stays progress
      return $lowerWord;
    }

    if ($lastLetter === "y") {
      // City -> Cities
      return substr($lowerWord, 0, -1) . "ies";
    }

    // Default: just add 's'
    return $lowerWord . "s";
  }

  public static function datatable(array $options = []): array
  {
    $page = max(1, (int) ($options["page"] ?? 1));
    $perPage = max(1, (int) ($options["perPage"] ?? 10));
    $search = trim($options["search"] ?? "");
    $orderBy = $options["orderBy"] ?? "id";
    $orderDir = strtoupper($options["orderDir"] ?? "DESC");
    $orderDir = in_array($orderDir, ["ASC", "DESC"], true) ? $orderDir : "DESC";

    $db = static::db();

    $table = static::$table;
    $baseAlias = "t";

    $joins = "";
    $searchableColumns = [];

    /*
    |--------------------------------------------------------------------------
    | Build searchable columns (supports simple & relation based)
    |--------------------------------------------------------------------------
    */
    if (!empty(static::$searchable)) {
      foreach (static::$searchable as $key => $value) {
        // 🔹 Simple column search (base table)
        if (is_string($value)) {
          $searchableColumns[] = "{$baseAlias}.{$value}";
          continue;
        }

        // 🔹 Relation based search
        if (is_array($value)) {
          $relationName = $key;
          $relTable = static::pluralize($relationName);
          $foreignKey = $value["foreign_key"];
          $column = $value["column"];
          $alias = $relTable;

          $joins .= "
                    LEFT JOIN {$relTable} {$alias}
                    ON {$alias}.id = {$baseAlias}.{$foreignKey}
                ";

          $searchableColumns[] = "{$alias}.{$column}";
        }
      }
    }

    /*
    |--------------------------------------------------------------------------
    | Allowed order columns
    |--------------------------------------------------------------------------
    */
    $allowedOrderColumns = array_merge(["{$baseAlias}.id"], $searchableColumns);

    // Prefix base alias if missing
    if (strpos($orderBy, ".") === false) {
      $orderBy = "{$baseAlias}.{$orderBy}";
    }

    // Validate orderBy
    if (!in_array($orderBy, $allowedOrderColumns, true)) {
      $orderBy = "{$baseAlias}.id";
    }

    $offset = ($page - 1) * $perPage;

    /*
    |--------------------------------------------------------------------------
    | WHERE clause (search)
    |--------------------------------------------------------------------------
    */
    $whereParts = [];
    $params = [];

    if ($search !== "" && !empty($searchableColumns)) {
      foreach ($searchableColumns as $col) {
        $whereParts[] = "{$col} LIKE ?";
        $params[] = "{$search}%";
      }
    }

    $whereSql = $whereParts ? "WHERE " . implode(" OR ", $whereParts) : "";

    /*
    |--------------------------------------------------------------------------
    | Total count
    |--------------------------------------------------------------------------
    */
    $totalSql = "
        SELECT COUNT(*)
        FROM {$table} {$baseAlias}
        {$joins}
        {$whereSql}
    ";

    $stmt = $db->query($totalSql, $params);
    $total = (int) $stmt->fetchColumn();

    /*
    |--------------------------------------------------------------------------
    | Fetch data
    |--------------------------------------------------------------------------
    */
    $dataSql = "
        SELECT {$baseAlias}.*
        FROM {$table} {$baseAlias}
        {$joins}
        {$whereSql}
        ORDER BY {$orderBy} {$orderDir}
        LIMIT {$perPage} OFFSET {$offset}
    ";

    $stmt = $db->query($dataSql, $params);
    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);

    /*
    |--------------------------------------------------------------------------
    | After datatable hook
    |--------------------------------------------------------------------------
    */
    if (method_exists(static::class, "afterDatatable")) {
      $rows = static::afterDatatable($rows);
    }

    /*
    |--------------------------------------------------------------------------
    | Apply hidden fields
    |--------------------------------------------------------------------------
    */
    $instance = new static();
    foreach ($rows as $row) {
      $instance->applyHidden($row);
    }

    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */
    return [
      "data" => $rows,
      "meta" => [
        "current_page" => $page,
        "per_page" => $perPage,
        "total" => $total,
        "total_pages" => (int) ceil($total / $perPage),
        "search" => $search,
        "order_by" => $orderBy,
        "order_dir" => $orderDir,
      ],
    ];
  }

  public static function groupDatatable(array $options = []): array
  {
    $page = max(1, (int) ($options["page"] ?? 1));
    $perPage = max(1, (int) ($options["perPage"] ?? 10));
    $search = trim($options["search"] ?? "");
    $orderBy = $options["orderBy"] ?? "total";
    $orderDir = strtoupper($options["orderDir"] ?? "DESC");
    $orderDir = in_array($orderDir, ["ASC", "DESC"], true) ? $orderDir : "DESC";

    $select = $options["select"] ?? "*";
    $joins = $options["joins"] ?? "";
    $groupBy = $options["groupBy"] ?? "";

    $db = static::db();
    $table = static::$table;
    $alias = "t";

    $offset = ($page - 1) * $perPage;

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */
    $whereSql = "";
    $params = [];

    if ($search !== "" && !empty($options["searchColumns"])) {
      $whereParts = [];

      foreach ($options["searchColumns"] as $col) {
        $whereParts[] = "{$col} LIKE ?";
        $params[] = "{$search}%";
      }

      $whereSql = "WHERE " . implode(" OR ", $whereParts);
    }

    /*
    |--------------------------------------------------------------------------
    | TOTAL COUNT (Subquery Based for GROUP BY)
    |--------------------------------------------------------------------------
    */
    $countSql = "
        SELECT COUNT(*) FROM (
            SELECT {$groupBy}
            FROM {$table} {$alias}
            {$joins}
            {$whereSql}
            GROUP BY {$groupBy}
        ) as grouped_table
    ";

    $stmt = $db->query($countSql, $params);
    $total = (int) $stmt->fetchColumn();

    /*
    |--------------------------------------------------------------------------
    | FETCH DATA
    |--------------------------------------------------------------------------
    */
    $dataSql = "
        SELECT {$select}
        FROM {$table} {$alias}
        {$joins}
        {$whereSql}
        GROUP BY {$groupBy}
        ORDER BY {$orderBy} {$orderDir}
        LIMIT {$perPage} OFFSET {$offset}
    ";

    $stmt = $db->query($dataSql, $params);
    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);

    return [
      "data" => $rows,
      "meta" => [
        "current_page" => $page,
        "per_page" => $perPage,
        "total" => $total,
        "total_pages" => (int) ceil($total / $perPage),
      ],
    ];
  }
}
