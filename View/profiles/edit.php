<?php
$errors = errors();
$success = success();
$old = old();
?>
<?php includes("layouts.header"); ?>

<?php
if (isset($_SESSION["pass_match"])) {
  unset($_SESSION["pass_match"]);
}
$profileImg = $user["avtar"] ?? "/public/images/default.png";
?>

<style>
:root{
  --brand:#8e44ad;
  --brand-dark:#6c3483;
}

.container{
  max-width:720px;
  min-height:100vh;
}

.profile-card{
  background:#fff;
  border-radius:18px;
  padding:28px;
  box-shadow:0 15px 40px rgba(0,0,0,.08);
  border-top:5px solid var(--brand);
}

.form-label{
  font-weight:600;
}

.form-control{
  border-radius:12px;
  padding:12px;
}

.form-control:focus{
  border-color:var(--brand);
  box-shadow:0 0 0 3px rgba(142,68,173,.2);
}

.btn-primary{
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  border:none;
  border-radius:12px;
  padding:10px 22px;
}

.btn-primary:hover{
  opacity:.95;
}

.change_password_text{
  font-size:.9rem;
  font-weight:600;
  color:var(--brand);
  text-decoration:none;
}

.change_password_text:hover{
  color:var(--brand-dark);
  text-decoration:underline;
}

.profile-preview{
  width:150px;
  height:150px;
  border-radius:14px;
  object-fit:cover;
  border:3px solid rgba(142,68,173,.2);
}

.back-link{
  color:var(--brand);
  font-size:18px;
}
</style>

<div class="container d-flex align-items-center justify-content-center">
  <div class="w-100 my-5">

    <div class="profile-card">

      <!-- HEADER -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="<?= Route("user.dashboard") ?>" class="me-3 back-link">
          <i class="fas fa-arrow-left"></i>
        </a>
        <h4 class="mb-0 fw-bold">Edit User Profile</h4>
      </div>


      <form action="<?= route(
        "user.profile.update"
      ) ?>" method="post" enctype="multipart/form-data" novalidate>
        <?= csrf_field() ?>

        <!-- NAME -->
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input
            type="text"
            name="name"
            class="form-control <?= !empty($errors["name"])
              ? "is-invalid"
              : "" ?>"
            value="<?= htmlspecialchars(
              $old["name"] ?? ($user["name"] ?? "")
            ) ?>"
          >
          <?php if (!empty($errors["name"])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars(
              $errors["name"]
            ) ?></div>
          <?php endif; ?>
        </div>

        <!-- EMAIL -->
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input
            type="email"
            name="email"
            class="form-control <?= !empty($errors["email"])
              ? "is-invalid"
              : "" ?>"
            value="<?= htmlspecialchars(
              $old["email"] ?? ($user["email"] ?? "")
            ) ?>"
          >
          <?php if (!empty($errors["email"])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars(
              $errors["email"]
            ) ?></div>
          <?php endif; ?>
        </div>

        <!-- PROFILE IMAGE -->
        <div class="mb-3">
          <label class="form-label">Profile Image</label>
          <input
            type="file"
            name="profile"
            id="profile"
            class="form-control <?= !empty($errors["image"])
              ? "is-invalid"
              : "" ?>"
            accept="image/jpeg,image/png,image/jpg"
          >
          <?php if (!empty($errors["image"])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars(
              $errors["image"]
            ) ?></div>
          <?php endif; ?>
        </div>

        <!-- PREVIEW -->
        <div class="mb-4">
          <img
            id="profilePreview"
            src="<?= $profileImg ?>"
            class="profile-preview"
          >
        </div>

        <!-- CHANGE PASSWORD -->
        <div class="text-end mb-4">
          <a href="<?= route(
            "user.password.edit"
          ) ?>" class="change_password_text">
            Change Password
          </a>
        </div>

        <!-- SUBMIT -->
        <button class="btn btn-primary">
          Update Profile
        </button>

      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('profile').addEventListener('change', e => {
  const file = e.target.files[0];
  if (file) {
    document.getElementById('profilePreview').src =
      URL.createObjectURL(file);
  }
});
</script>

<?php includes("layouts.footer"); ?>
