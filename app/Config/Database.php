<?php
namespace App\Config;
use PDO;
use App\Core\Env;

class Database
{
  private $driver;
  private $host;
  private $port;
  private $user;
  private $pass;
  private $dbname;
  public $conn;
  private static $instance = null;

  public function __construct()
  {
    $this->driver = env("DB_CONNECTION");
    $this->host = env("DB_HOST");
    $this->port = env("DB_PORT");
    $this->user = env("DB_USERNAME");
    $this->pass = env("DB_PASSWORD");
    $this->dbname = env("DB_NAME");

    $this->conn = null;
    try {
      $this->conn = new PDO(
        "$this->driver:host=$this->host; dbname=$this->dbname",
        $this->user,
        $this->pass
      );
      $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    } catch (PDOException $e) {
      echo "connection error:" . $e->getMessage();
    }
  }

  public static function connect()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance->conn;
  }

  public function query($sql, $params = [])
  {
    $stmt = $this->conn->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }
}
?>
