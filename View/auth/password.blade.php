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

body {
  background: linear-gradient(135deg,#f5f3fb,#fff);
  font-family: system-ui;
}

.container {
  max-width:700px;
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:20px;
}

.card {
  background:#fff;
  padding:30px;
  border-radius:18px;
  width:100%;
  max-width:480px;
  border-top:5px solid var(--brand);
  box-shadow:0 20px 50px rgba(142,68,173,.15);
}

.header {
  display:flex;
  align-items:center;
  margin-bottom:20px;
}

.header a {
  color:var(--brand);
  font-size:20px;
  margin-right:12px;
  text-decoration:none;
}

.form-control {
  width:100%;
  padding:12px;
  border-radius:10px;
  border:1px solid #ddd;
  outline:none;
}

.form-control:focus {
  border-color:var(--brand);
  box-shadow:0 0 0 3px rgba(142,68,173,.2);
}

.btn-primary {
  width:100%;
  padding:12px;
  border:none;
  border-radius:10px;
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  color:#fff;
  margin-top:12px;
  cursor:pointer;
}

.btn-success {
  width:100%;
  padding:12px;
  border:none;
  border-radius:10px;
  background:#28a745;
  color:#fff;
  margin-top:12px;
  cursor:pointer;
}

.error-text { 
  color:var(--error-color); 
  font-size:13px; 
  margin-top:5px; 
}

.success-text { 
  color:var(--success-color); 
  text-align:center; 
  margin-bottom:10px; 
  font-weight:600;
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

        @if(!empty($errors['cpass']))
          <div class="error-text">{{ $errors['cpass'] }}</div>
        @endif

        <button class="btn-primary">Verify</button>
      </form>

    @else

      <!-- UPDATE PASSWORD -->
      <form action="{{ route('user.password.update') }}" method="post">
        @csrf

        <label>New Password</label>
        <input type="password" name="npass" class="form-control">

        @if(!empty($errors['npass']))
          <div class="error-text">{{ $errors['npass'] }}</div>
        @endif

        <button class="btn-success">Update Password</button>
      </form>

    @endif

  </div>
</div>
@endsection