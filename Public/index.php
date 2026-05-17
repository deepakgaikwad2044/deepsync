<?php

session_start();

date_default_timezone_set("Asia/kolkata");

define("APP_ROOT", dirname(__DIR__));

require_once APP_ROOT . "/vendor/autoload.php";

require_once APP_ROOT . "/App/Core/Env.php";
require_once APP_ROOT . "/App/Core/helpers.php";

use App\Core\Env;

Env::load(base_path(".env"));

spl_autoload_register(function ($class) {
    $classFile = str_replace("\\", DIRECTORY_SEPARATOR, $class . ".php");
    $classpath = APP_ROOT . "/" . $classFile;

    if (file_exists($classpath)) {
        require_once $classpath;
    }
});

/* =========================
   CREATE PRANCHI FIRST
========================= */
$GLOBALS['pranchi'] = new \App\Core\Pranchi();

/* =========================
   THEN LOAD COMPONENTS
========================= */
require_once APP_ROOT . "/bootstrap/components.php";

use App\Core\Router;

/* =========================
   ROUTES
========================= */
require_once APP_ROOT . "/Routes/web.php";
require_once APP_ROOT . "/Routes/api.php";

/* =========================
   DISPATCH
========================= */
Router::dispatch();