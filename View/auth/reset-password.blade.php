@extends("layouts.layouts")

@section('content')

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
  --brand:#8e44ad;
  --brand-dark:#6c3483;
  --bg:linear-gradient(135deg,#f3eaff,#f9f6ff);
  --text:#2c2c2c;
  --muted:#777;
  --danger:#e74c3c;
  --success:#27ae60;
}

/* BACKGROUND */
body{
  margin:0;
  font-family:system-ui,-apple-system,"Segoe UI",sans-serif;
  background:var(--bg);
  color:var(--text);
}

/* CONTAINER */
.login-container{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:20px;
}

/* 💎 CARD */
.login-card{
  width:100%;
  max-width:440px;
  background:rgba(255,255,255,0.85);
  backdrop-filter:blur(12px);

  border-radius:20px;
  padding:34px;

  box-shadow:0 25px 60px rgba(0,0,0,.08);
  border:1px solid rgba(0,0,0,0.05);

  transition:0.3s ease;
}

.login-card:hover{
  transform:translateY(-5px);
}

/* BRAND */
.brand{
  text-align:center;
  margin-bottom:22px;
}

.brand h1{
  margin:0;
  font-size:24px;
  font-weight:700;
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}

.brand p{
  margin-top:6px;
  font-size:14px;
  color:var(--muted);
}

/* ALERTS */
.alert{
  padding:12px;
  border-radius:12px;
  margin-bottom:14px;
  font-size:14px;
  text-align:center;
}

.alert-success{
  background:rgba(39,174,96,.1);
  color:var(--success);
}

.alert-error{
  background:rgba(231,76,60,.1);
  color:var(--danger);
}

/* FORM */
.form-group{
  margin-bottom:18px;
}

label{
  display:block;
  margin-bottom:6px;
  font-size:14px;
  font-weight:600;
}

input{
  width:100%;
  padding:13px 14px;
  border-radius:14px;
  border:1px solid rgba(0,0,0,0.1);
  font-size:15px;
  transition:.25s ease;
}

input:focus{
  border-color:var(--brand);
  box-shadow:0 0 0 3px rgba(142,68,173,.2);
}

/* ERROR */
.has-error input{
  border-color:var(--danger);
}

.error-text{
  margin-top:6px;
  font-size:13px;
  color:var(--danger);
}

/* BUTTONS */
.btn-group{
  display:flex;
  gap:12px;
  margin-top:24px;
}

button,a{
  flex:1;
  padding:13px;
  border-radius:14px;
  font-size:15px;
  font-weight:500;
  text-align:center;
  border:none;
  text-decoration:none;
  cursor:pointer;
  transition:0.25s ease;
}

/* PRIMARY */
.btn-primary{
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  color:#fff;
}

.btn-primary:hover{
  transform:translateY(-2px);
  box-shadow:0 10px 25px rgba(142,68,173,.3);
}

/* OUTLINE */
.btn-outline{
  background:transparent;
  border:1px solid var(--brand);
  color:var(--brand);
}

.btn-outline:hover{
  background:rgba(142,68,173,0.08);
}

/* MOBILE */
@media(max-width:480px){
  .btn-group{ flex-direction:column; }
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
      
@csrf

      <input type="hidden" name="token" value="{{ $token }}">

      {{-- PASSWORD --}}
      <div class="form-group {{ !empty($errors['password']) ? 'has-error' : '' }}">
        <label>New Password</label>
        <input type="password" name="password" placeholder="Enter new password">

                 @error('password')
          <div class="error-text">{{ $message }}</div>
@enderror
      </div>

      {{-- CONFIRM PASSWORD --}}
      <div class="form-group {{ !empty($errors['confirm_password']) ? 'has-error' : '' }}">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Confirm password">

                 @error('confirm_password')
          <div class="error-text">{{ $message }}</div>
@enderror
      </div>

      <div class="btn-group">
        <button type="submit" class="btn-primary">Update Password</button>
        <a href="{{ route('user.login') }}" class="btn-outline">Back to Login</a>
      </div>

    </form>

  </div>
</div>
@endsection