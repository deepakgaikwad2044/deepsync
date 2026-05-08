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
     
    unset($_SESSION["verified"]);
    $authUser = Auth::user(); // Retrieve the currently authenticated user's data.
    view("profiles.edit", ["user" => $authUser]); // Pass user data to the profile edit view.
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
    $rules["profile"] = "image|max:2MB|mimes:jpg,png";
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

  // IMAGE UPLOAD ONLY IF FILE EXISTS
  $file = $data["profile"];

  if (!empty($file["name"])) {
    try {
      $newAvatar = File::upload($file, "profiles");

      if ($avatar && File::exists($avatar)) {
        File::delete($avatar);
      }

      $updateData["avtar"] = $newAvatar;

    } catch (\Exception $e) {
      $_SESSION["errors"]["profile"] = $e->getMessage();
      redirect(route("user.profile.edit"));
      return;
    }
  }

  if (User::update($authUserId, $updateData)) {
    setFlash("flash_success", "Profile updated");
    redirect(route("user.dashboard"));
    return;
  }

  redirect(route("user.profile.edit"));
}
}
?>
