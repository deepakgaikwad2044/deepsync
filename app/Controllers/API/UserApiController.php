<?php
namespace App\Controllers\Api;

use App\Helpers\JWTHelper;
use App\Core\Response;
use App\Core\Request;
use App\Models\User;

class UserApiController
{
  public function index()
  {
    // GET /users
  }

  public function show($id)
  {
    // GET /users/{id}
    return response::success("get", "hello world");
  }
  
  public function store()
  {
    // POST /users
    $param = $_POST;
    $name = trim($param["name"] ?? "");
    $email = trim($param["email"] ?? "");
    $password = trim($param["password"] ?? "");

    if (!$name || !$email || !$password) {
      return response::error("All fields are required");
    }

    // Check existing user
    if (
      User::query()
        ->where("email", $email)
        ->first()
    ) {
      return response::error("User already exists");
    }

    // Create user
    User::create([
      "name" => $name,
      "email" => $email,
      "password" => password_hash($password, PASSWORD_BCRYPT),
    ]);

    $user = User::query()
      ->where("email", $email)
      ->first();

    return response::success("user successfull created", $user);
  }

  public function update($id)
  {
    $input = Request::input();
    $name = $input["name"] ?? null;
    $email = $input["email"] ?? null;
    $password = $input["password"] ?? null;

    // Validation
    if (!$name || !$email || !$password) {
      return Response::error("All fields are required");
    }

    // Update user
    $updated = User::updateWhere("id", $id, [
      "name" => $name,
      "email" => $email,
      "password" => password_hash($password, PASSWORD_BCRYPT),
    ]);

    if (!$updated) {
      return Response::error("User not found or not updated");
    }

    // Fetch updated user
    $user = User::query()
      ->where("id", $id)
      ->first();

    return Response::success("User successfully updated", $user);
  }

  public function updatePartial($id)
  {
    $input = Request::input();

    $data = [];

    if (isset($input["name"])) {
      $data["name"] = $input["name"];
    }

    if (isset($input["email"])) {
      $data["email"] = $input["email"];
    }

    if (isset($input["password"])) {
      $data["password"] = password_hash($input["password"], PASSWORD_BCRYPT);
    }

    if (empty($data)) {
      return Response::error("Nothing to update");
    }

    $updated = User::updateWhere("id", $id, $data);

    if (!$updated) {
      return Response::error("User not found or not updated");
    }

    $user = User::query()
      ->where("id", $id)
      ->first();

    return Response::success("User partially updated", $user);
  }

  public function destroy($id)
  {
    // DELETE /users/{id}
    $user = User::Query()
      ->where("id", $id)
      ->first();
    if (!$user) {
      return response::error("user not  found");
    }

    $deleted = User::deleteWhere("id", $id);

    if (!$deleted) {
      return Response::error("User not found or not updated");
    }

    return response::success("user successfully  deleted");
  }
}
