@extends('layouts.layouts')

@section('content')
@php
$errors = errors();
@endphp
<style>
:root{
  --brand: #8e44ad;
  --brand-dark: #6c3483;
  --bg: #f5f3fb;
  --text: #2c2c2c;
  --muted: #777;
  --danger: #e74c3c;
}

*{
  box-sizing: border-box;
}

body{
  margin:0;
  font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
  background: linear-gradient(135deg, var(--bg), #fff);
  color: var(--text);
}

.login-container{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:18px;
}

.login-card{
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
  margin-bottom:22px;
}

.brand h1{
  margin:0;
  font-size:26px;
  font-weight:700;
  color:var(--brand);
}

.brand p{
  margin-top:6px;
  font-size:14px;
  color:var(--muted);
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
  transition:.2s;
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

.btn-group{
  display:flex;
  gap:12px;
  margin-top:26px;
}

button,
.btn-link{
  flex:1;
  padding:13px;
  border-radius:12px;
  font-size:15px;
  font-weight:500;
  cursor:pointer;
  text-align:center;
  border:none;
  text-decoration:none;
}

.btn-primary{
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  color:#fff;
}

.btn-primary:hover{
  opacity:.95;
}

.btn-outline{
  background:transparent;
  border:1px solid var(--brand);
  color:var(--brand);
}

@media(max-width:480px){
  .login-card{ padding:24px; }
  .btn-group{ flex-direction:column; }
}


#forgot_password {
       font-size:14px;
       color:var(--brand);
       text-decoration:none;
       font-weight:500;
}
</style>


<div class="login-container">
  <div class="login-card">

    <div class="brand">
      <h1>{{ env('APP_NAME') }}</h1>
      <p>Secure access to your workspace</p>
    </div>

    <form action="{{ route('user.login.verify') }}" method="post">

    @if(get_flash('success'))
    {{ get_flash('success') }}
@endif

      @if(get_flash('error'))
        <div class="error-alert">
          {{ get_flash('error') }}
        </div>
      @endif

      {!! csrf_field() !!}

      <!-- EMAIL -->
      <div class="form-group {{ !empty($errors['email']) ? 'has-error' : '' }}">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter email" value="{{ old('email') }}">

        @if(!empty($errors['email']))
          <div class="error-text">
            {{ $errors['email'] }}
          </div>
        @endif
      </div>

      <!-- PASSWORD -->
      <div class="form-group {{ !empty($errors['password']) ? 'has-error' : '' }}">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password">

        @if(!empty($errors['password']))
          <div class="error-text">
            {{ $errors['password'] }}
          </div>
        @endif
      </div>

      <div style="text-align:right; margin-top:-8px; margin-bottom:16px;">
        <a href="{{ route('user.forgot.password') }}" id="forgot_password">
          Forgot password?
        </a>
      </div>

      <div class="btn-group">
        <button type="submit" class="btn-primary">Login</button>
        <a href="/register" class="btn-link btn-outline">Register</a>
      </div>

    </form>

  </div>
</div>

@endsection