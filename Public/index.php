<?php
session_start();

//Function to set Timezone
date_default_timezone_set("Asia/kolkata");

//Function to set Root Directory
define("APP_ROOT", dirname(__DIR__));

//load to autoload file
require_once APP_ROOT . "/vendor/autoload.php";

use App\Core\Env;

require_once APP_ROOT . "/App/Core/Env.php";
require_once APP_ROOT . "/App/Core/helpers.php";

Env::load(base_path(".env"));

//Function to get file name with namespace
spl_autoload_register(function ($class) {
  $classFile = str_replace("\\", DIRECTORY_SEPARATOR, $class . ".php");

  $classpath = APP_ROOT . "/" . $classFile;

  if (file_exists($classpath)) {
    require_once $classpath;
    // echo $classpath;
  }
});

use App\Core\Router;

// 📌 Load routes
require_once APP_ROOT . "/Routes/web.php";

require_once APP_ROOT . "/Routes/api.php";

// 🚀 Dispatch route
Router::dispatch();


?>

