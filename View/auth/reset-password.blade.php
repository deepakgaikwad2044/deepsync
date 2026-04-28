@include("layouts.header")

@php
    $errors = $_SESSION["errors"] ?? [];
    $old = $_SESSION["old"] ?? [];
    $token = $token ?? request("token");

    $success = get_flash("success");
    $error = get_flash("error");

    unset($_SESSION["errors"], $_SESSION["old"]);
@endphp

<style>
:root{
  --brand: #8e44ad;
  --brand-dark: #6c3483;
  --bg: #f5f3fb;
  --text: #2c2c2c;
  --muted: #777;
  --danger: #e74c3c;
  --success: #27ae60;
}

* { box-sizing: border-box; }

body {
  margin:0;
  font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
  background: linear-gradient(135deg, var(--bg), #fff);
  color: var(--text);
}

.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 18px;
}

.login-card {
  width: 100%;
  max-width: 460px;
  background: #fff;
  border-radius: 18px;
  padding: 32px;
  box-shadow: 0 20px 50px rgba(142,68,173,.18);
  border-top: 5px solid var(--brand);
}

.brand {
  text-align: center;
  margin-bottom: 22px;
}

.brand h1 {
  margin: 0;
  font-size: 26px;
  font-weight: 700;
  color: var(--brand);
}

.brand p {
  margin-top: 6px;
  font-size: 14px;
  color: var(--muted);
}

.alert {
  padding: 12px;
  border-radius: 10px;
  margin-bottom: 14px;
  font-size: 14px;
}

.alert-success {
  background: #eafaf1;
  color: var(--success);
}

.alert-error {
  background: #fdecea;
  color: var(--danger);
}

.form-group {
  margin-bottom: 18px;
}

label {
  display: block;
  margin-bottom: 6px;
  font-size: 14px;
  font-weight: 500;
}

input {
  width: 100%;
  padding: 13px 14px;
  border-radius: 12px;
  border: 1px solid #ddd;
  font-size: 15px;
  outline: none;
}

input:focus {
  border-color: var(--brand);
  box-shadow: 0 0 0 3px rgba(142,68,173,.15);
}

.has-error input {
  border-color: var(--danger);
}

.error-text {
  margin-top: 6px;
  font-size: 13px;
  color: var(--danger);
}

.btn-group {
  display: flex;
  gap: 12px;
  margin-top: 26px;
}

button, a {
  flex: 1;
  padding: 13px;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 500;
  text-align: center;
  border: none;
  text-decoration: none;
  cursor: pointer;
}

.btn-primary {
  background: linear-gradient(135deg,var(--brand),var(--brand-dark));
  color: #fff;
}

.btn-outline {
  background: transparent;
  border: 1px solid var(--brand);
  color: var(--brand);
}

@media(max-width:480px){
  .btn-group { flex-direction: column; }
}
</style>

<div class="login-container">
  <div class="login-card">

    <div class="brand">
      <h1>Reset Password</h1>
      <p>Create a new secure password</p>
    </div>

    {{-- FLASH MESSAGES --}}
    @if($success)
      <div class="alert alert-success">{{ $success }}</div>
    @endif

    @if($error)
      <div class="alert alert-error">{{ $error }}</div>
    @endif

    <form action="{{ route('user.reset.password.verify') }}" method="post">
      
      {!! csrf_field() !!}

      <input type="hidden" name="token" value="{{ $token }}">

      {{-- PASSWORD --}}
      <div class="form-group {{ !empty($errors['password']) ? 'has-error' : '' }}">
        <label>New Password</label>
        <input type="password" name="password" placeholder="Enter new password">

        @if(!empty($errors['password']))
          <div class="error-text">{{ $errors['password'] }}</div>
        @endif
      </div>

      {{-- CONFIRM PASSWORD --}}
      <div class="form-group {{ !empty($errors['confirm_password']) ? 'has-error' : '' }}">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Confirm password">

        @if(!empty($errors['confirm_password']))
          <div class="error-text">{{ $errors['confirm_password'] }}</div>
        @endif
      </div>

      <div class="btn-group">
        <button type="submit" class="btn-primary">Update Password</button>
        <a href="{{ route('user.login') }}" class="btn-outline">Back to Login</a>
      </div>

    </form>

  </div>
</div>

@include("layouts.footer")