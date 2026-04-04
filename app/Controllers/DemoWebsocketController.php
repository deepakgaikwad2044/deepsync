<?php
namespace App\Controllers;
use App\Core\Response;
use App\Models\User;
class DemoWebsocketController
{
  public function index()
  {
    view("websocket.all");
  }

  public function countedusers()
  {
    $inactive_users = User::query()
      ->where("account_status", 0)
      ->count();

    $total_user = User::query()->count();

    return Response::success("Live count", [
      "inactive_users" => $inactive_users,
      "total_user" => $total_user,
    ]);
  }
}

?>
