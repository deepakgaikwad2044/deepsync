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
        $authUserId = $_REQUEST["auth_user"]->id;

        $user = User::query()
            ->where("id", $authUserId)
            ->first();

        if (!$user) {
            return Response::error("User not found");
        }

        return Response::success("User retrieved successfully", $user);
    }

    public function show($id)
    {
        $authUserId = $_REQUEST["auth_user"]->id;

        $user = User::query()
            ->where("id", $id)
            ->where("id", $authUserId)
            ->first();

        if (!$user) {
            return Response::error("User not found or access denied", 403);
        }

        return Response::success("User retrieved successfully", $user);
    }

    public function store()
    {
        return Response::error("Access denied", 403);
    }

    public function update($id)
    {
        $authUserId = $_REQUEST["auth_user"]->id;

        if ((int) $id !== (int) $authUserId) {
            return Response::error("Access denied", 403);
        }

        $input = Request::input();
        $name = $input["name"] ?? null;
        $email = $input["email"] ?? null;
        $password = $input["password"] ?? null;

        if (!$name || !$email || !$password) {
            return Response::error("All fields are required");
        }

        $updated = User::updateWhere("id", $id, [
            "name" => $name,
            "email" => $email,
            "password" => password_hash($password, PASSWORD_BCRYPT),
        ]);

        if (!$updated) {
            return Response::error("User not found or not updated");
        }

        $user = User::query()
            ->where("id", $id)
            ->first();

        return Response::success("User successfully updated", $user);
    }

    public function updatePartial($id)
    {
        $authUserId = $_REQUEST["auth_user"]->id;

        if ((int) $id !== (int) $authUserId) {
            return Response::error("Access denied", 403);
        }

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
        $authUserId = $_REQUEST["auth_user"]->id;

        if ((int) $id !== (int) $authUserId) {
            return Response::error("Access denied", 403);
        }

        $user = User::query()
            ->where("id", $id)
            ->first();

        if (!$user) {
            return Response::error("User not found", 404);
        }

        $deleted = User::deleteWhere("id", $id);

        if (!$deleted) {
            return Response::error("User could not be deleted");
        }

        return Response::success("User successfully deleted");
    }
}
