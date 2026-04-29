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
  --bg:#f5f3fb;
  --text:#2c2c2c;
  --muted:#777;
  --danger:#e74c3c;
  --success:#27ae60;
}

*{ box-sizing:border-box; }

body{
  margin:0;
  font-family:system-ui,-apple-system,"Segoe UI",sans-serif;
  background:linear-gradient(135deg,var(--bg),#fff);
}

.container{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:18px;
}

.card{
  width:100%;
  max-width:460px;
  background:#fff;
  border-radius:18px;
  padding:32px;
  box-shadow:0 20px 50px rgba(142,68,173,.18);
  border-top:5px solid var(--brand);
}

.brand{
  text-align:center;
  margin-bottom:20px;
}

.brand h1{
  margin:0;
  font-size:26px;
  color:var(--brand);
}

.brand p{
  margin-top:6px;
  font-size:14px;
  color:var(--muted);
}

.alert{
  padding:12px;
  border-radius:10px;
  margin-bottom:16px;
  font-size:14px;
}

.alert-error{
  background:#fdecea;
  color:var(--danger);
}

.alert-success{
  background:#eafaf1;
  color:var(--success);
}

.form-group{
  margin-bottom:18px;
}

label{
  display:block;
  margin-bottom:6px;
  font-size:14px;
  font-weight:500;
}

input{
  width:100%;
  padding:13px 14px;
  border-radius:12px;
  border:1px solid #ddd;
  font-size:15px;
  outline:none;
}

input:focus{
  border-color:var(--brand);
  box-shadow:0 0 0 3px rgba(142,68,173,.15);
}

.has-error input{
  border-color:var(--danger);
}

.error-text{
  margin-top:6px;
  font-size:13px;
  color:var(--danger);
}

button,a{
  width:100%;
  padding:13px;
  border-radius:12px;
  font-size:15px;
  font-weight:500;
  border:none;
  cursor:pointer;
  text-align:center;
  text-decoration:none;
}

.btn-primary{
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  color:#fff;
}

.btn-link{
  margin-top:12px;
  display:block;
  background:transparent;
  color:var(--brand);
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