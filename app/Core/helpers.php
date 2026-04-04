<?php

/**
 * Get project root path
 */
function base_path(string $path = ''): string
{
    $base = dirname(__DIR__, 2); // project root
    return $path ? $base . '/' . ltrim($path, '/') : $base;
}

function request(string $key = null, $default = null)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($key === null) {
            return $_POST;
        }
        return trim($_POST[$key]) ?? $default;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
    return $default;
}


function errors()
{
    $errors = $_SESSION['errors'] ?? [];
    unset($_SESSION['errors']);
    return $errors;
}


function success()
{
    $msg = $_SESSION['success'] ?? null;
    unset($_SESSION['success']);
    return $msg;
}
function old($key = null, $default = '')
{
    static $used = false;

    $old = $_SESSION['old'] ?? [];

    if (!$used) {
        unset($_SESSION['old']);
        $used = true;
    }

    return $key ? ($old[$key] ?? $default) : $old;
}



