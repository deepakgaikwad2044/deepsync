<?php
namespace App\Controllers;


// Import the Auth class
use App\Config\Auth;
use App\Models\User;
use App\Models\profile;
use App\Models\order;
use App\Config\Database;
use App\Core\Response;
use App\Core\Blade;

class BaseController
{
  protected $db;

  // Constructor method that runs automatically
  public function __construct()
  {
    // Get the authenticated user's details
    if (!empty($_SESSION["user_id"])) {
      $loggedInUser = Auth::user();

      // Store the user details in the session
      $_SESSION["user"] = $loggedInUser;

      $this->db = Database::connect();
    }
  }

  public function home()
  {
    view("home");
  }
  
  public function pageNotFound()
  {
    return view("err400");
  }
  
  public function b() {
 
$blade = new Blade();

echo $blade->render('test', [
    'name' => '<script>alert("XSS")</script>',
    'users' => ['Deepak', 'Suraj', 'Akash']
]);


  }
}
?>
