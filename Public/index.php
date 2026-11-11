<?php

declare(strict_types=1);

/* =========================
   SESSION
========================= */
/* =========================
   SESSION SECURITY
========================= */

// Reject uninitialized session IDs.
ini_set("session.use_strict_mode", "1");

// Enable Secure only when the current request uses HTTPS.
$isHttps = !empty($_SERVER["HTTPS"])
    && strtolower((string) $_SERVER["HTTPS"]) !== "off";

session_set_cookie_params([
    "lifetime" => 0,
    "path" => "/",
    "secure" => $isHttps,
    "httponly" => true,
    "samesite" => "Lax",
]);

/* =========================
   SESSION
========================= */
if (!@session_start()) {
    error_log("[Deep Sync] Session initialization failed.");
    http_response_code(503);
    header("Content-Type: text/plain; charset=UTF-8");
    exit("Service temporarily unavailable.");
}

/* =========================
   TIMEZONE
========================= */
date_default_timezone_set("Asia/Kolkata");

/* =========================
   ROOT PATH
========================= */
define("APP_ROOT", dirname(__DIR__));

/* =========================
   COMPOSER AUTOLOADER
========================= */
require_once APP_ROOT . "/vendor/autoload.php";

/* =========================
   CORE FILES
========================= */
require_once APP_ROOT . "/App/Core/Env.php";
require_once APP_ROOT . "/App/Core/helpers.php";

use App\Core\Env;

/* =========================
   ENV LOAD (EARLY)
========================= */
Env::load(APP_ROOT . "/.env");

/* =========================
   PRANCHI CORE INIT
========================= */
$GLOBALS['pranchi'] = new \App\Core\Pranchi();

/* =========================
   BOOTSTRAP COMPONENT SYSTEM
========================= */
require_once APP_ROOT . "/bootstrap/components.php";

/* =========================
   ROUTER
========================= */
use App\Core\Router;

/* safer route loading */
$routes = [
    APP_ROOT . "/Routes/web.php",
    APP_ROOT . "/Routes/api.php",
];

foreach ($routes as $routeFile) {
    if (file_exists($routeFile)) {
        require_once $routeFile;
    } else {
        throw new RuntimeException("Route file missing: {$routeFile}");
    }
}

/* =========================
   DISPATCH WITH SAFETY
========================= */
try {
    Router::dispatch();
} catch (\Throwable $e) {
    http_response_code(500);
    echo "Application Error: " . $e->getMessage();
}