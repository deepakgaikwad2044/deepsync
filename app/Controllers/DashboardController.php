<?php
namespace App\Controllers;

// Import the Auth class
use App\Config\Auth;

// DashboardController extends BaseController
class DashboardController extends BaseController {

  // Show the dashboard
  public function index() {
    // Get the authenticated user's details
    $user = Auth::user();

    // Pass the user data to the "dashboard" view
    view("dashboard", ["user" => $user]);
  }

public function logout()
{

     Auth::logout();
    redirect(route("user.login"));
    exit;
}
}
?>
