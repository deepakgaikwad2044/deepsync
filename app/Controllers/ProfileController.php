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

    $validator = new Validator();

    $result = $validator->validate($data, [
      "name" => "required|len:3",
      "email" => "required|email",
    ]);

    if (!$result["status"]) {
      $_SESSION["errors"] = $result["errors"];
      $_SESSION["old"] = $data;
      redirect(route("user.profile.edit"));
      return;
    }

    // ✅ 2. Auth info
    $authUserId = Auth::id();
    $avatar = Auth::avatar();

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $file = $_FILES["profile"] ?? null;

    // ✅ 3. Prepare update data
    $updateData = [
      "name" => $name,
      "email" => $email,
    ];

    // ✅ 4. Image upload
    if ($file && $file["name"] !== "") {
      try {
        $newAvatar = File::upload($file, "profiles");

        if ($avatar && File::exists($avatar)) {
          File::delete($avatar);
        }

        $updateData["avtar"] = $newAvatar;
      } catch (\Exception $e) {
        $_SESSION["errors"]["image"] = $e->getMessage();
        redirect(route("user.profile.edit"));
        exit();
      }
    }

    // ✅ 5. Update DB
    if (User::update($authUserId, $updateData)) {
      setFlash("success", "Profile updated");
      return redirect(route("user.dashboard"));

      exit();
    } else {
      setFlash("errors", [
        "form" => "Profile didn't update",
      ]);
      redirect(route("user.profile.edit"));
      return;
    }
  }
}
?>
