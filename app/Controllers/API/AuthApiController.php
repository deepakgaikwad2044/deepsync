<?php
namespace App\Controllers\API;

use App\Config\Auth;
use App\Models\User;
use App\Helpers\JWTHelper;
use App\Core\Response;

class AuthApiController
{
    /* =========================
       LOGIN
    ========================== */
    public function login()
    {
        $email = $_POST["email"] ?? null;
        $password = $_POST["password"] ?? null;

        if (!$email || !$password) {
            return Response::error("Email and password are required", 400);
        }

        $user = Auth::attempt([
            "email" => $email,
            "password" => $password,
        ]);

        if (!$user) {
            return Response::error("Invalid credentials", 401);
        }
        
          $id = $user["id"];

        // ✅ Access Token (JWT)
        $accessToken = JWTHelper::generateToken([
            "id" => $user["id"],
            "email" => $user["email"],
            "name" => $user["name"],
        ]);

        // 🔐 Refresh Token (RAW + HASH)
        $refreshToken = bin2hex(random_bytes(64));
        $hashedToken  = hash("sha256", $refreshToken);

        // 💾 Store hashed token in DB
         User::updateWhere(  "id", $id ,[
            "refresh_token" => $hashedToken,
            "refresh_token_expiry" => date("Y-m-d H:i:s", strtotime("+7 days"))
        ]);

        return Response::success("Login successful", [
            "accessToken" => $accessToken,
            "refreshToken" => $refreshToken, // ✅ IMPORTANT
            "user" => $user,
        ]);
    }

    /* =========================
       REFRESH TOKEN
    ========================== */
    public function refreshToken()
    {
        $refreshToken = $_POST["refresh_token"] ?? null;

        if (!$refreshToken) {
            return Response::error("Refresh token required", 400);
        }

        // 🔐 hash incoming token
        $hashedToken = hash("sha256", $refreshToken);

        $user = User::query()
            ->where("refresh_token", $hashedToken)
            ->first();

        if (!$user) {
            return Response::error("Invalid refresh token", 401);
        }
        
        $id = $user["id"];

        // ⏳ Expiry check
        if (strtotime($user["refresh_token_expiry"]) < time()) {
            return Response::error("Refresh token expired", 401);
        }

        // 🔁 Generate NEW tokens (rotation)
        $newRefreshToken = bin2hex(random_bytes(64));
        $newHashedToken  = hash("sha256", $newRefreshToken);

        $newAccessToken = JWTHelper::generateToken([
            "id" => $user["id"],
            "email" => $user["email"],
            "name" => $user["name"],
        ]);

        // 💾 Update DB
       User::updateWhere("id", $id ,[
            "refresh_token" => $newHashedToken,
            "refresh_token_expiry" => date("Y-m-d H:i:s", strtotime("+7 days"))
        ]);  
        
        return Response::success("Token refreshed", [
            "accessToken" => $newAccessToken,
            "refreshToken" => $newRefreshToken,
        ]);
    }

    /* =========================
       LOGOUT
    ========================== */
    public function logout()
    {
        $refreshToken = $_POST["refresh_token"] ?? null;

        if (!$refreshToken) {
            return Response::error("Refresh token required", 400);
        }

        $hashedToken = hash("sha256", $refreshToken);

        $user = User::query()
            ->where("refresh_token", $hashedToken)
            ->first();
        

        if (!$user) {
            return Response::error("Invalid refresh token", 401);
        }
         $id = $user["id"]; 
        

        // ❌ Remove token from DB
         User::updateWhere("id", $id ,[
    "refresh_token" => null,
    "refresh_token_expiry" => null
]);

        Auth::logout();

        return Response::success("Logout successful");
    }

    /* =========================
       PROTECTED TEST API
    ========================== */
    public function getDummyData()
    {
        $last_id = $_POST["last_id"] ?? 0;

        $data = User::query()
            ->where("id", ">", $last_id)
            ->orderBy("id", "ASC")
            ->get();

        return Response::success("JWT token valid", $data);
    }
}