@extends("layouts.layouts")
@section("content")

@php
    $errors = errors();
    $flash_success = get_flash("success");
    $flash_error = get_flash("error");
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
}

/* CONTAINER */
.container{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:20px;
}

/* 💎 CARD */
.card{
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

.card:hover{
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
  margin-bottom:16px;
  font-size:14px;
  text-align:center;
}

.alert-error{
  background:rgba(231,76,60,.1);
  color:var(--danger);
}

.alert-success{
  background:rgba(39,174,96,.1);
  color:var(--success);
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

/* BUTTON */
button,a{
  width:100%;
  padding:13px;
  border-radius:14px;
  font-size:15px;
  font-weight:500;
  border:none;
  cursor:pointer;
  text-align:center;
  text-decoration:none;
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

/* LINK BUTTON */
.btn-link{
  margin-top:12px;
  display:block;
  background:transparent;
  color:var(--brand);
}

.btn-link:hover{
  text-decoration:underline;
}

</style>

<div class="container">
  <div class="card">

    <div class="brand">
      <h1>Forgot Password</h1>
      <p>We’ll send a reset link to your email</p>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(!empty($flash_success))
      <div class="alert alert-success">
        {{ $flash_success }}
      </div>
    @endif

    @if(!empty($flash_error))
      <div class="alert alert-error">
        {{ $flash_error }}
      </div>
    @endif

    <form action="{{ route('users.forgot.password') }}" method="POST">

      {!! csrf_field() !!}

      {{-- EMAIL --}}
      <div class="form-group {{ !empty($errors['email']) ? 'has-error' : '' }}">
        <label>Email Address</label>

        <input type="email"
               name="email"
               placeholder="Enter registered email"
               value="{{ old('email') }}">

        @if(!empty($errors['email']))
          <div class="error-text">{{ $errors['email'] }}</div>
        @endif
      </div>

      <button type="submit" class="btn-primary">
        Send Reset Link
      </button>

      <a href="{{ route('user.login') }}" class="btn-link">
        Back to Login
      </a>

    </form>

  </div>
</div>

@endsection