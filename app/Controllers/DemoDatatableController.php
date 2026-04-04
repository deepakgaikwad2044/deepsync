<?php
namespace App\Controllers;
use App\Core\Response;
use App\Models\User;
// WebSocket Channels
use App\WebSockets\Realtime\Channels\UserChannel;

class DemoDatatableController
{
  public function index()
  {
    view("datatable.all");
  }

  public function getData()
  {
    $query = [
      "page" => (int) ($_GET["page"] ?? 1),
      "perPage" => 5,
      "search" => $_GET["search"] ?? "",
    ];

    $result = User::datatable($query);

    Response::success("users found", $result);
  }

  public function create()
  {
    $data = $_POST;

    $user = User::register($data);

    if ($user) {
      $inactive_users = User::query()
        ->where("account_status", 0)
        ->count();

      $total_user = User::query()->count();

      // User channel
      $UserChannel = new UserChannel();
      $UserChannel->send("userupdated", [
        "inactive_users" => $inactive_users,
        "total_user" => $total_user,
      ]);

      Response::success("user successfully inserted", null);
    } else {
      Response::error("user did not register");
    }
  }

  public function Update()
  {
    $id = $_POST["id"] ?? null;
    $name = $_POST["name"] ?? null;
    $email = $_POST["email"] ?? null;
    $account_status = $_POST["account_status"] ?? null;

    if ($id === null || $name === null || $email === null) {
      return Response::error("Invalid request data");
    }

    $result = User::update((int) $id, [
      "name" => $name,
      "email" => $email,
      "account_status" => $account_status,
    ]);

    if ($result) {
      $inactive_users = User::query()
        ->where("account_status", 0)
        ->count();

      $total_user = User::query()->count();

      // User channel
      $UserChannel = new UserChannel();
      $UserChannel->send("userupdated", [
        "inactive_users" => $inactive_users,
        "total_user" => $total_user,
      ]);
      Response::success("user successfully updated", null);
    } else {
      Response::error("user updation failed");
    }
  }

  public function delete()
  {
    $id = $_POST["id"] ?? null;

    if ($id === null) {
      return Response::error("Invalid request data");
    }

    $result = User::deleteWhere("id", $id);

    if ($result) {
      $inactive_users = User::query()
        ->where("account_status", 0)
        ->count();

      $total_user = User::query()->count();

      // User channel
      $UserChannel = new UserChannel();
      $UserChannel->send("userupdated", [
        "inactive_users" => $inactive_users,
        "total_user" => $total_user,
      ]);
      Response::success("user successfully deleted", null);
    } else {
      Response::error("User delete failed");
    }
  }
}

?>
