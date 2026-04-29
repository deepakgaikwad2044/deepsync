<?php
namespace Routes;
use App\Core\Router;
use App\Core\Middleware;
use App\Middleware\Auth;
use App\Middleware\Guest;
use App\Middleware\Permission;
use App\Controllers\RegisterController;
use App\Controllers\BaseController;
use App\Controllers\LoginController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;
use App\Controllers\PasswordController;
use App\Controllers\ForgotPasswordController;
use App\Controllers\DemoDatatableController;
use App\Controllers\DemoWebsocketController;
use App\Controllers\AppController;


// Web
Router::group("web", function () {
  Router::get("/", [BaseController::class, "home"])->name("home");
  
  
   Router::get("/deepsync/about", [BaseController::class, "about"])->name("deep.about");
  
   Router::get("/deepsync/docs", [BaseController::class, "docs"])->name("deep.docs"); 
  
  Router::get("b", [BaseController::class , "b"]);

  // USER GUEST ROUTES
  Router::group(
    [
      "prefix" => "/",
      "middleware" => "guest",
    ],
    function () {
      //Register Controller
      Router::get("/register", [RegisterController::class, "index"])->name(
        "user.register"
      );

      Router::post("/register", [RegisterController::class, "register"])->name(
        "user.register.verify"
      );

      // Login Controller
      Router::get("/login", [LoginController::class, "index"])->name(
        "user.login"
      );

      Router::post("/login", [LoginController::class, "login"])->name(
        "user.login.verify"
      );
    }
  );

  // USER AUTH ROUTES
  Router::group(
    [
      "prefix" => "users",
      "middleware" => "auth",
    ],
    function () {
      Router::get("/profile/edit", [ProfileController::class, "edit"])->name(
        "user.profile.edit"
      );

      Router::post("/profile/update", [
        ProfileController::class,
        "update",
      ])->name("user.profile.update");

      // User Password Controller
      Router::get("/password/edit", [PasswordController::class, "index"])->name(
        "user.password.edit"
      );

      Router::post("/password/verify", [
        PasswordController::class,
        "passwordVerify",
      ])->name("user.password.verify");

      Router::post("/password/update", [
        PasswordController::class,
        "passwordUpdate",
      ])->name("user.password.update");

      //Dashboard Controller
      Router::get("/dashboard", [DashboardController::class, "index"])->name(
        "user.dashboard"
      );

      Router::post("/logout", [DashboardController::class, "logout"])->name(
        "user.logout"
      );
    }
  );

  // Datatable

  Router::group(
    [
      "prefix" => "datatable",
      "middleware" => "auth",
    ],
    function () {
      Router::get("all", [DemoDatatableController::class, "index"])->name(
        "table.show"
      );

      Router::get("data", [DemoDatatableController::class, "getData"])->name(
        "table.data"
      );

      Router::post("create", [DemoDatatableController::class, "create"])->name(
        "datatable.create"
      );

      Router::post("update", [DemoDatatableController::class, "update"])->name(
        "datatable.update"
      );

      Router::post("delete", [DemoDatatableController::class, "delete"])->name(
        "datatable.delete"
      );
    }
  );

  Router::group(
    [
      "prefix" => "websocket",
    ],
    function () {
      Router::get("all", [DemoWebsocketController::class, "index"])->name(
        "websocket.all"
      );

      Router::get("live/count", [
        DemoWebsocketController::class,
        "countedusers",
      ])->name("websocket.user.count");
    }
  );

  // USER non middleware Routes
  Router::get("/users/forgot/password", [
    ForgotPasswordController::class,
    "showForgotForm",
  ])->name("user.forgot.password");

  Router::post("/users/forgot/password", [
    ForgotPasswordController::class,
    "sendResetLink",
  ])->name("user.forgot.password.verify");

  Router::get("/users/reset/password/{token}", [
    ForgotPasswordController::class,
    "showResetForm",
  ])->name("user.reset.password");

  Router::post("/users/reset/password", [
    ForgotPasswordController::class,
    "resetPassword",
  ])->name("user.reset.password.verify");
});


?>
