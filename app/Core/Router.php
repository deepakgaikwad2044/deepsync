<?php

namespace App\Core;

class Router
{
    protected static array $routes = [];
    protected static array $namedRoutes = [];
    protected static array $currentGroupMiddleware = [];
    protected static array $lastRoute = [];
    protected static string $currentGroupPrefix = "";

    /* =========================
       GROUP (FIXED 🔥)
    ========================== */
    public static function group(array|string $group, callable $callback)
    {
        $previousMiddleware = self::$currentGroupMiddleware;
        $previousPrefix = self::$currentGroupPrefix;

        if (is_array($group)) {

            // ✅ Middleware merge (FIXED)
            if (isset($group["middleware"])) {
                $newMiddleware = is_array($group["middleware"])
                    ? $group["middleware"]
                    : [$group["middleware"]];

                self::$currentGroupMiddleware = array_unique(
                    array_merge(self::$currentGroupMiddleware, $newMiddleware)
                );
            }

            // ✅ Prefix merge
            if (isset($group["prefix"])) {
                self::$currentGroupPrefix .= "/" . trim($group["prefix"], "/");
            }

        } else {
            // ✅ String group also merge (FIXED)
            self::$currentGroupMiddleware = array_unique(
                array_merge(self::$currentGroupMiddleware, [$group])
            );
        }

        $callback();

        // 🔁 Restore previous state
        self::$currentGroupMiddleware = $previousMiddleware;
        self::$currentGroupPrefix = $previousPrefix;
    }

    /* =========================
       METHODS
    ========================== */
    public static function get(string $uri, array $action)
    {
        self::addRoute("GET", $uri, $action);
        return new static();
    }

    public static function post(string $uri, array $action)
    {
        self::addRoute("POST", $uri, $action);
        return new static();
    }

    public static function put(string $uri, array $action)
    {
        self::addRoute("PUT", $uri, $action);
        return new static();
    }

    public static function delete(string $uri, array $action)
    {
        self::addRoute("DELETE", $uri, $action);
        return new static();
    }

    public static function patch(string $uri, array $action)
    {
        self::addRoute("PATCH", $uri, $action);
        return new static();
    }

    /* =========================
       ADD ROUTE
    ========================== */
    protected static function addRoute(string $method, string $uri, array $action)
    {
        $uri = self::$currentGroupPrefix . "/" . trim($uri, "/");
        $uri = "/" . trim($uri, "/");

        $pattern = preg_replace("#\{(\w+)\}#", "([^/]+)", $uri);
        $pattern = "#^" . $pattern . "$#";

        self::$routes[$method][$uri] = [
            "action" => $action,
            "pattern" => $pattern,
            "uri" => $uri,
            "groupMiddleware" => self::$currentGroupMiddleware,
            "middleware" => [],
            "name" => null,
        ];

        self::$lastRoute = &self::$routes[$method][$uri];
    }

    /* =========================
       NAME
    ========================== */
    public function name(string $name)
    {
        self::$lastRoute["name"] = $name;
        self::$namedRoutes[$name] = self::$lastRoute["uri"];
        return $this;
    }

    /* =========================
       ROUTE MIDDLEWARE
    ========================== */
    public function middleware(string $name)
    {
        self::$lastRoute["middleware"][] = $name;
        return $this;
    }

    /* =========================
       DISPATCH
    ========================== */
    public static function dispatch()
    {
        $method = $_SERVER["REQUEST_METHOD"];

        // 🔥 Method spoofing (form)
        if ($method === "POST" && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        // 🔥 JSON spoofing
        $rawInput = json_decode(file_get_contents("php://input"), true);
        if ($method === "POST" && isset($rawInput['_method'])) {
            $method = strtoupper($rawInput['_method']);
        }

        $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        if (!isset(self::$routes[$method])) {
            self::notFound();
            return;
        }

        $matchedRoute = null;
        $params = [];

        foreach (self::$routes[$method] as $route) {
            if (preg_match($route["pattern"], $uri, $matches)) {

                $matchedRoute = $route;
                array_shift($matches);

                preg_match_all("#\{(\w+)\}#", $route["uri"], $paramNames);
                $paramNames = $paramNames[1];

                $params = $paramNames ? array_combine($paramNames, $matches) : [];
                break;
            }
        }

        if (!$matchedRoute) {
            self::notFound();
            return;
        }

        // 🔥 Merge route + group middleware (FINAL FIX)
        $allMiddleware = array_unique(array_merge(
            $matchedRoute["groupMiddleware"],
            $matchedRoute["middleware"]
        ));

        // ✅ Run middleware
        \App\Core\Middleware::handle($allMiddleware);

        [$controller, $methodName] = $matchedRoute["action"];
        $controllerInstance = new $controller();

        call_user_func_array([$controllerInstance, $methodName], $params);
    }

    /* =========================
       ROUTE HELPER
    ========================== */
    public static function route(string $name, array $params = [])
    {
        if (!isset(self::$namedRoutes[$name])) {
            return "#";
        }

        $uri = self::$namedRoutes[$name];

        foreach ($params as $key => $value) {
            $uri = str_replace("{" . $key . "}", $value, $uri);
        }

        return $uri;
    }

    /* =========================
       404
    ========================== */
    protected static function notFound()
    {
        http_response_code(404);
        (new \App\Controllers\BaseController())->pageNotFound();
    }
}
