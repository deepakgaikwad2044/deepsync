<?php
namespace App\Controllers;

// Import the Auth class
use App\Config\Auth;
use App\Core\Response;
use App\Models\User;

// DashboardController extends BaseController
class DashboardController extends BaseController {

  // Show the dashboard
  public function index() {
    // Get the authenticated user's details
    $user = Auth::user();

    // Pass the user data to the "dashboard" view
    view("auth.dashboard", ["user" => $user]);
  }

public function logout()
{

     Auth::logout();
    redirect(route("user.login"));
    exit;
}



public function markIntroSeen()
{
    $resp = User::update(Auth::id(), [
        'intro_seen' => 1
    ]);

    if ($resp) {
        return Response::success("intro seen updated successfully");
    }

    return Response::error("intro seen not updated");
}
}
?>
