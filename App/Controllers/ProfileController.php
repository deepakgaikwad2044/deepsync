<?php
namespace App\Controllers;

use App\Models\User;
use App\Core\Validator;

use App\Config\Auth;
use App\Config\File;

class ProfileController extends BaseController
{
  // Function to display the profile edit form
  public function edit()
  { 
     
    $authUser = Auth::user(); // Retrieve the currently authenticated user's data.
    view("auth.profiles.edit", ["user" => $authUser]); // Pass user data to the profile edit view.
  }

public function update()
{
  $data = $_POST;
  $data["profile"] = $_FILES["profile"] ?? null;

  $rules = [
    "name" => "required|len:3",
    "email" => "required|email",
  ];

  if (!empty($data["profile"]["name"])) {
    $rules["profile"] = "image|max:2MB|mimes:jpg,png,webp";
  }

  $validator = new Validator();
  $result = $validator->validate($data, $rules);

  if (!$result["status"]) {
    $_SESSION["errors"] = $result["errors"];
    $_SESSION["old"] = $data;

    redirect(route("user.profile.edit"));
    return;
  }

  $authUserId = Auth::id();
  $avatar = Auth::avatar();

  $updateData = [
    "name" => trim($data["name"]),
    "email" => trim($data["email"]),
  ];

  // Upload new avatar before updating the database.
  $file = $data["profile"];
  $newAvatar = null;

  if (!empty($file["name"])) {
    try {
      $newAvatar = File::upload($file, "profiles");
      $updateData["avtar"] = $newAvatar;
    } catch (\Throwable $e) {
      $_SESSION["errors"]["profile"] = $e->getMessage();
      redirect(route("user.profile.edit"));
      return;
    }
  }

  try {
    $updated = User::update($authUserId, $updateData);
  } catch (\Throwable $e) {
    if ($newAvatar !== null) {
      File::delete($newAvatar);
    }

    $_SESSION["errors"]["profile"] = "Profile update failed.";
    redirect(route("user.profile.edit"));
    return;
  }

  if (!$updated) {
    if ($newAvatar !== null) {
      File::delete($newAvatar);
    }

    redirect(route("user.profile.edit"));
    return;
  }

  // Delete the previous avatar only after database success.
  if (
    $newAvatar !== null &&
    $avatar &&
    File::exists($avatar)
  ) {
    File::delete($avatar);
  }

  setFlash("flash_success", "Profile updated");
  redirect(route("user.dashboard"));
}
}
?>
