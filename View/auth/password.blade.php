@extends("layouts.layouts")
@section("content")
@php
    $errors = errors();
    $success = success();
    $verified = $_SESSION['verified'] ?? false;
@endphp

<style>
:root {
  --brand:#8e44ad;
  --brand-dark:#6c3483;
  --error-color:#e74c3c;
  --success-color:#28a745;
}

/* 🌈 BACKGROUND */
body {
  background: linear-gradient(135deg,#f3f0ff,#eef1f6);
  font-family: system-ui;
}

/* CONTAINER */
.container {
  max-width:700px;
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:20px;
}

/* 💎 CARD */
.card {
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(12px);
  padding:32px;
  border-radius:20px;
  width:100%;
  max-width:460px;

  box-shadow:0 20px 60px rgba(0,0,0,.08);
  border:1px solid rgba(0,0,0,0.05);

  transition:0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
}

/* HEADER */
.header {
  display:flex;
  align-items:center;
  margin-bottom:20px;
}

.header h4 {
  font-weight:600;
}

.header a {
  color:var(--brand);
  font-size:18px;
  margin-right:12px;
  text-decoration:none;
  padding:6px;
  border-radius:10px;
  transition:0.2s ease;
}

.header a:hover {
  background: rgba(142,68,173,0.1);
}

/* LABEL */
label {
  font-weight:600;
  margin-bottom:6px;
  display:block;
}

/* INPUT */
.form-control {
  width:100%;
  padding:12px 14px;
  border-radius:14px;
  border:1px solid rgba(0,0,0,0.1);
  outline:none;
  transition:0.25s ease;
}

.form-control:focus {
  border-color:var(--brand);
  box-shadow:0 0 0 3px rgba(142,68,173,.2);
}

/* BUTTONS */
.btn-primary, .btn-success {
  width:100%;
  padding:12px;
  border:none;
  border-radius:14px;
  color:#fff;
  margin-top:14px;
  cursor:pointer;
  font-weight:500;
  transition:0.25s ease;
}

/* VERIFY BTN */
.btn-primary {
  background: linear-gradient(135deg,var(--brand),var(--brand-dark));
}

/* UPDATE BTN */
.btn-success {
  background: linear-gradient(135deg,#28a745,#1e7e34);
}

.btn-primary:hover,
.btn-success:hover {
  transform: translateY(-2px);
  box-shadow:0 10px 25px rgba(0,0,0,0.15);
}

/* ERROR */
.error-text { 
  color:var(--error-color); 
  font-size:13px; 
  margin-top:6px; 
}

/* SUCCESS */
.success-text { 
  color:var(--success-color); 
  text-align:center; 
  margin-bottom:12px; 
  font-weight:600;
}

/* SMALL UX TOUCH */
input:focus {
  background:#fff;
}

/* ANIMATION */
.card, .btn-primary, .btn-success {
  will-change: transform;
}
</style>

<div class="container">
  <div class="card">

    <!-- HEADER -->
    <div class="header">
      <a href="{{ route('user.profile.edit') }}">
        <i class="fas fa-arrow-left"></i>
      </a>
      <h4>Change Password</h4>
    </div>

    <!-- SUCCESS -->
    @if(!empty($success))
      <div class="success-text">{{ $success }}</div>
    @endif

    <!-- FORM ERROR -->
    @if(!empty($errors['form']))
      <div class="error-text">{{ $errors['form'] }}</div>
    @endif

    <!-- VERIFY PASSWORD -->
    @if(!$verified)

      <form action="{{ route('user.password.verify') }}" method="post">
        @csrf

        <label>Current Password</label>
        <input type="password" name="cpass" class="form-control">

               @error('cpass')
    <div class="error-text">{{ $message }}</div>
@enderror

        <button class="btn-primary">Verify</button>
      </form>

    @else

      <!-- UPDATE PASSWORD -->
      <form action="{{ route('user.password.update') }}" method="post">
        @csrf

        <label>New Password</label>
        <input type="password" name="npass" class="form-control">

               @error('npass')
    <div class="error-text">{{ $message }}</div>
@enderror

        <button class="btn-success">Update Password</button>
      </form>

    @endif

  </div>
</div>
@endsection