@extends("layouts.layouts")

@section('content')


<style>
:root{
  --brand:#8e44ad;
  --brand-dark:#6c3483;
  --bg:linear-gradient(135deg,#f3eaff,#f9f6ff);
  --text:#2c2c2c;
  --muted:#777;
  --danger:#e74c3c;
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
  margin-bottom:24px;
}

.brand h1{
  margin:0;
  font-size:26px;
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

/* INPUT */
.form-group{
  margin-bottom:18px;
}

label{
  font-size:14px;
  font-weight:600;
  margin-bottom:6px;
  display:block;
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

/* ALERT */
.error-alert{
  background:rgba(231,76,60,.1);
  color:var(--danger);
  padding:10px;
  border-radius:10px;
  margin-bottom:14px;
  text-align:center;
  font-size:14px;
}

/* FORGOT */
#forgot_password{
  font-size:13px;
  color:var(--brand);
  text-decoration:none;
  font-weight:500;
  transition:0.2s;
}

#forgot_password:hover{
  text-decoration:underline;
}

/* BUTTONS */
.btn-group{
  display:flex;
  gap:12px;
  margin-top:24px;
}

button,
.btn-link{
  flex:1;
  padding:13px;
  border-radius:14px;
  font-size:15px;
  font-weight:500;
  cursor:pointer;
  text-align:center;
  border:none;
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
  .login-card{ padding:26px; }
  .btn-group{ flex-direction:column; }
}

</style>


<div class="login-container">
  <div class="login-card">

    <div class="brand">
      <h1>{{ env('APP_NAME') }}</h1>
      <p>Secure access to your workspace</p>
    </div>

    <form action="{{ route('user.login.verify') }}" method="post">

@flashError

@flashSuccess

    @csrf

      <!-- EMAIL -->
      <div class="form-group {{ !empty($errors['email']) ? 'has-error' : '' }}">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter email" value="{{ old('email') }}">

                       @error('email')
          <div class="error-text">{{ $message }}</div>
@enderror

      </div>

      <!-- PASSWORD -->
      <div class="form-group {{ !empty($errors['password']) ? 'has-error' : '' }}">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password">

          @error('password')
          <div class="error-text">{{ $message }}</div>
@enderror
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