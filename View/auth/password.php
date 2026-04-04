<?php $errors = errors();
$success = success();
?>
<?php includes("layouts.header"); ?>
<style>
  :root {
    --brand: #8e44ad;
    --brand-dark: #6c3483;
    --bg-gradient: linear-gradient(135deg, #f5f3fb, #ffffff);
    --text-color: #2c2c2c;
    --muted-color: #777;
    --error-color: #e74c3c;
    --success-color: #28a745;
  }

  body {
    background: var(--bg-gradient);
    color: var(--text-color);
    font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
    margin: 0;
  }

  .container {
    max-width: 700px;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 24px 16px;
  }

  .card {
    background: #fff;
    border-radius: 18px;
    padding: 32px 28px;
    box-shadow: 0 20px 50px rgba(142, 68, 173, 0.15);
    width: 100%;
    max-width: 480px;
    border-top: 5px solid var(--brand);
  }

  .header {
    display: flex;
    align-items: center;
    margin-bottom: 32px;
  }

  .header a {
    color: var(--brand);
    font-size: 1.6rem;
    margin-right: 16px;
    transition: color 0.3s ease;
  }

  .header a:hover {
    color: var(--brand-dark);
  }

  .header h4 {
    font-weight: 700;
    font-size: 1.5rem;
    margin: 0;
    color: var(--text-color);
  }

  label.form-label {
    font-weight: 600;
    color: var(--text-color);
    display: block;
    margin-bottom: 8px;
    font-size: 0.95rem;
  }

  .form-control {
    width: 100%;
    padding: 13px 14px;
    font-size: 1rem;
    border-radius: 12px;
    border: 1.5px solid #ddd;
    outline: none;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
  }

  .form-control:focus {
    border-color: var(--brand);
    box-shadow: 0 0 8px rgba(142, 68, 173, 0.3);
  }

  .btn-primary,
  .btn-success {
    width: 100%;
    padding: 14px 0;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.1rem;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .btn-primary {
    background: linear-gradient(135deg, var(--brand), var(--brand-dark));
    color: #fff;
    margin-top: 16px;
  }

  .btn-primary:hover:not(:disabled) {
    background: var(--brand-dark);
  }

  .btn-success {
    background: #28a745;
    color: #fff;
    margin-top: 16px;
  }

  .btn-success:hover {
    background: #218838;
  }

  button:disabled,
  button[disabled] {
    opacity: 0.6;
    cursor: not-allowed;
  }

  /* Error message style */
  .error-text {
    color: var(--error-color);
    font-size: 0.85rem;
    margin-top: 6px;
  }

  /* Success message style */
  .success-text {
    color: var(--success-color);
    font-size: 0.95rem;
    margin-bottom: 16px;
    font-weight: 600;
    text-align: center;
  }

  /* Responsive */
  @media (max-width: 576px) {
    .container {
      padding: 20px 12px;
    }

    .header h4 {
      font-size: 1.3rem;
    }
  }
</style>

<div class="container">
  <div class="card">

    <div class="header">
      <a href="<?= route(
        "user.profile.edit"
      ) ?>" aria-label="Go back to profile edit">
        <i class="fas fa-arrow-left"></i>
      </a>
      <h4>Change Password</h4>
    </div>

    <?php // Show success or error message from session


    if (!empty($_SESSION["success"])) {
      echo '<div class="success-text">' .
        htmlspecialchars($_SESSION["success"]) .
        "</div>";
      unset($_SESSION["success"]);
    }
    if (!empty($_SESSION["err_pass"])) {
      echo '<div class="error-text">' .
        htmlspecialchars($_SESSION["err_pass"]) .
        "</div>";
      unset($_SESSION["err_pass"]);
    }
    if (!empty($errors["form"])) {
      echo '<div class="error-text">' .
        htmlspecialchars($errors["form"]) .
        "</div>";
    }
    ?>

    <?php if (empty($_SESSION["verified"])): ?>
      <!-- Password verify form -->
      <form action="<?= route(
        "user.password.verify"
      ) ?>" method="post" novalidate>
        <?= csrf_field() ?>

        <label for="cpass" class="form-label">Current Password:</label>
        <input
          type="password"
          class="form-control <?= !empty($errors["cpass"])
            ? "is-invalid"
            : "" ?>"
          id="cpass"
          name="cpass"
          required
          autocomplete="current-password"
          value="<?= htmlspecialchars($old["cpass"] ?? "") ?>"
        >
        <?php if (!empty($errors["cpass"])): ?>
          <div class="error-text"><?= htmlspecialchars(
            $errors["cpass"]
          ) ?></div>
        <?php endif; ?>

        <button type="submit" name="submit" class="btn-primary mt-3">
          Verify
        </button>
      </form>

    <?php else: ?>
      <!-- Password update form -->
      <form action="<?= route(
        "user.password.update"
      ) ?>" method="post" novalidate>
        <?= csrf_field() ?>

        <label for="npass" class="form-label">New Password:</label>
        <input
          type="password"
          class="form-control <?= !empty($errors["npass"])
            ? "is-invalid"
            : "" ?>"
          id="npass"
          name="npass"
          required
          autocomplete="new-password"
        >
        <?php if (!empty($errors["npass"])): ?>
          <div class="error-text"><?= htmlspecialchars(
            $errors["npass"]
          ) ?></div>
        <?php endif; ?>

        <button type="submit" name="submit" class="btn-success mt-3">
          Update Password
        </button>
      </form>
    <?php endif; ?>

  </div>
</div>

<?php includes("layouts.footer"); ?>
