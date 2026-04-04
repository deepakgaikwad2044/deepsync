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


// Function to get the public image URL
function public_path($filepath)
{
    $file = APP_ROOT . "/public/" . $filepath;

    if (file_exists($file)) {
        // Return the URL path instead of the file path
        $publicUrl = "/public/" . $filepath;
        return $publicUrl;
    } else {
        throw new Exception("404: file not found " . $file);
    }
}

// Function to handle view folder
function view($file_path, $data = [])
{
  $path = str_replace("\\", DIRECTORY_SEPARATOR, $file_path);

  $path = str_replace(".", DIRECTORY_SEPARATOR, $path);

  $file =
    APP_ROOT .
    DIRECTORY_SEPARATOR .
    "view" .
    DIRECTORY_SEPARATOR .
    $path .
    ".php";

  if (file_exists($file)) {
    extract($data);
    require $file;
  } else {
    echo "Page not found " . $file;
  }
}

//handle includes file
function includes($file_path)
{
  $path = str_replace("\\", DIRECTORY_SEPARATOR, $file_path);

  $path = str_replace(".", DIRECTORY_SEPARATOR, $path);

  $file = APP_ROOT . DIRECTORY_SEPARATOR . "view/" . $path . ".php";

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
 

?>
