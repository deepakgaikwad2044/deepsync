<?php
namespace Routes;
use App\Core\Router;

use App\Controllers\API\AuthApiController;
use App\Controllers\API\UserApiController;

// Api
Router::group(["prefix" => "api"], function () {
  

  // 🔓 Public Routes
  Router::post("login", [AuthApiController::class, "login"]);
  Router::post("refresh-token", [AuthApiController::class, "refreshToken"]);

  // 🔒 Private Routes (JWT Required)
  Router::group(["middleware" => "JWT"], function () {
    Router::post("dummy", [AuthApiController::class, "getDummyData"])->name(
      "get.dummy.data"
    );

    // Users RESTFULL Resource
    Router::get("users", [UserApiController::class, "index"]);
    Router::get("users/{id}", [UserApiController::class, "show"]);

    Router::post("users", [UserApiController::class, "store"]);

    Router::put("users/{id}", [UserApiController::class, "update"]);
    Router::patch("users/{id}", [UserApiController::class, "updatePartial"]);

    Router::delete("users/{id}", [UserApiController::class, "destroy"]);
  });
});
?>
