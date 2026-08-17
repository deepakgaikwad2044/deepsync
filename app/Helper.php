<?php
use App\Config\Auth;
use App\Core\Env;


function env(string $key, $default = null)
{
    return Env::get($key, $default);
}


// Function to unset name attribute submit
unset($_POST["submit"]);

// Function to handle route location
function redirect($url)
{
  header("location:$url");
  exit();
}


function set_flash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function get_flash($key) {
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}


/* ================= VIEW HELPER ================= */

function view($file_path, $data = [])
{
    $pranchi = new \App\Core\Pranchi();

    // dot notation support
    $path = str_replace(".", DIRECTORY_SEPARATOR, $file_path);

    echo $pranchi->render($path, $data);
}

//handle includes file
function includes($file_path)
{
  $path = str_replace("\\", DIRECTORY_SEPARATOR, $file_path);

  $path = str_replace(".", DIRECTORY_SEPARATOR, $path);

  $file = APP_ROOT . DIRECTORY_SEPARATOR . "views/" . $path . ".php";

  if (file_exists($file)) {
    return require $file;
  } else {
    echo "Page not found " . $file;
  }
}


function shortname($val){
   $shortname =  strlen($val) > 6
        ? substr($val, 0, 6) . "..."
        : $val;
     echo $shortname;
}

function shortString($val, $limit){
   $shortname =  strlen($val) > $limit
        ? substr($val, 0, $limit) . "..."
        : $val;
     return $shortname;
}

function timeDef($time){
    // Get the current time and the time of notification creation
    $now = time();
    $created_at = strtotime($time);

    // Calculate the time difference in seconds
    $time_difference = $now - $created_at;

    // Determine the human-readable time format
    if ($time_difference < 60) {
        return 'Just now';
    } elseif ($time_difference < 3600) { // Less than 1 hour
        $minutes = floor($time_difference / 60);
        return $minutes . ' min' . ($minutes == 1 ? '' : '') . ' ';
    } elseif ($time_difference < 86400) { // Less than 1 day
        $hours = floor($time_difference / 3600);
        return $hours . ' hr' . ($hours == 1 ? '' : 's') . ' ';
    } elseif ($time_difference < 604800) { // Less than 1 week
        $days = floor($time_difference / 86400);
        return $days . ' day' . ($days == 1 ? '' : 's') . ' ';
    }else{
      return date('d M Y h:i a', $created_at);
    }
}


function csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}


function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}


function verify_csrf()
{
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("Invalid CSRF token");
    }
}



function setFlash($key, $value)
{
    $_SESSION[$key] = $value;
}

function flash($key)
{
    $value = $_SESSION[$key] ?? null;
    unset($_SESSION[$key]);
    return $value;
}


function route($name, $params = [])
{
    return \App\Core\Router::route($name, $params);
}

function dd(...$vars)
{
    echo "<style>
        body { background:#0f172a; color:#e2e8f0; font-family: monospace; }
        .dd-box {
            background:#020617;
            border:1px solid #334155;
            padding:20px;
            margin:20px;
            border-radius:10px;
            overflow:auto;
        }
    </style>";

    foreach ($vars as $var) {
        echo "<div class='dd-box'>";
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
        echo "</div>";
    }

    die();
}

