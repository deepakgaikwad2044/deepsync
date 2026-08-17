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
  --success:#27ae60;
}

/* PAGE */
body{
  margin:0;
  font-family:system-ui,-apple-system,"Segoe UI",sans-serif;
  background:var(--bg);
  color:var(--text);
  overflow-x:hidden;
}

/* CONTAINER */
.login-container{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:20px;
}

/* CARD */
.login-card{
  width:100%;
  max-width:450px;

  background:rgba(255,255,255,0.82);
  backdrop-filter:blur(16px);

  border-radius:24px;
  padding:38px 32px;

  border:1px solid rgba(255,255,255,0.4);

  box-shadow:
    0 25px 60px rgba(0,0,0,.08),
    0 8px 20px rgba(142,68,173,.08);

  position:relative;
  overflow:hidden;

  transition:.35s ease;
}

.login-card:hover{
  transform:translateY(-5px);
}

/* GLOW */
.login-card::before{
  content:"";
  position:absolute;
  top:-80px;
  right:-80px;
  width:180px;
  height:180px;
  background:rgba(142,68,173,.12);
  border-radius:50%;
}

/* BRAND */
.brand{
  text-align:center;
  margin-bottom:28px;
  position:relative;
  z-index:2;
}

.brand-logo{
  width:72px;
  height:72px;
  margin:auto auto 16px;
  border-radius:20px;

  background:linear-gradient(
    135deg,
    var(--brand),
    var(--brand-dark)
  );

  display:flex;
  align-items:center;
  justify-content:center;

  color:#fff;
  font-size:28px;
  font-weight:700;

  box-shadow:0 12px 25px rgba(142,68,173,.25);
}

.brand h1{
  margin:0;
  font-size:28px;
  font-weight:700;

  background:linear-gradient(
    135deg,
    var(--brand),
    var(--brand-dark)
  );

  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}

.brand p{
  margin-top:8px;
  font-size:14px;
  color:var(--muted);
}

/* ALERTS */
.alert{
  padding:13px 14px;
  border-radius:14px;
  margin-bottom:18px;
  font-size:14px;
  font-weight:500;
  animation:fadeIn .3s ease;
}

.alert-success{
  background:rgba(39,174,96,.12);
  color:var(--success);
  border:1px solid rgba(39,174,96,.18);
}

.alert-error{
  background:rgba(231,76,60,.10);
  color:var(--danger);
  border:1px solid rgba(231,76,60,.18);
}

/* FORM */
.form-group{
  margin-bottom:20px;
}

label{
  display:block;
  margin-bottom:8px;
  font-size:14px;
  font-weight:600;
}

/* INPUT */
.input-wrapper{
  position:relative;
}

input{
  width:100%;
  padding:14px 16px;
  border-radius:16px;

  border:1px solid rgba(0,0,0,.08);

  background:#fff;

  font-size:15px;

  transition:.25s ease;
}

input:focus{
  border-color:var(--brand);

  box-shadow:
    0 0 0 4px rgba(142,68,173,.15);

  outline:none;
}

/* ERROR */
.has-error input{
  border-color:var(--danger);
}

.error-text{
  margin-top:7px;
  font-size:13px;
  color:var(--danger);
  font-weight:500;
}

/* FORGOT */
.forgot-wrap{
  text-align:right;
  margin-top:-4px;
  margin-bottom:20px;
}

#forgot_password{
  font-size:13px;
  color:var(--brand);
  text-decoration:none;
  font-weight:600;
}

#forgot_password:hover{
  text-decoration:underline;
}

/* BUTTONS */
.btn-group{
  display:flex;
  gap:12px;
  margin-top:26px;
}

/* BUTTON */
button,
.btn-link{
  flex:1;

  padding:14px;
  border-radius:16px;

  font-size:15px;
  font-weight:600;

  cursor:pointer;

  border:none;
  text-decoration:none;

  transition:.25s ease;

  text-align:center;
}

/* PRIMARY */
.btn-primary{
  background:linear-gradient(
    135deg,
    var(--brand),
    var(--brand-dark)
  );

  color:#fff;

  box-shadow:0 10px 25px rgba(142,68,173,.22);
}

.btn-primary:hover{
  transform:translateY(-2px);
  box-shadow:0 15px 30px rgba(142,68,173,.30);
}

/* OUTLINE */
.btn-outline{
  background:#fff;
  color:var(--brand);
  border:1px solid rgba(142,68,173,.25);
}

.btn-outline:hover{
  background:rgba(142,68,173,.06);
}

/* ANIMATION */
@keyframes fadeIn{
  from{
    opacity:0;
    transform:translateY(-5px);
  }
  to{
    opacity:1;
    transform:none;
  }
}

/* MOBILE */
@media(max-width:480px){

  .login-card{
    padding:30px 24px;
  }

  .btn-group{
    flex-direction:column;
  }

}
</style>

<div class="login-container">

  <div class="login-card">

    <div class="brand">

      <div class="brand-logo">
        DS
      </div>

      <h1>{{ env('APP_NAME') }}</h1>

      <p>
        Secure access to your workspace
      </p>

    </div>

@flashError

@flashSuccess

    <form action="{{ route('user.login.verify') }}" method="POST">

      @csrf

      <!-- EMAIL -->

      <div class="form-group {{ !empty($errors['email']) ? 'has-error' : '' }}">

        <label>Email Address</label>

        <div class="input-wrapper">

          <input
            type="email"
            name="email"
            placeholder="Enter your email"
            value="{{ old('email') }}"
          >

        </div>

        @error('email')
          <div class="error-text">
            {{ $message }}
          </div>
        @enderror

      </div>

      <!-- PASSWORD -->

      <div class="form-group {{ !empty($errors['password']) ? 'has-error' : '' }}">

        <label>Password</label>

        <div class="input-wrapper">

          <input
            type="password"
            name="password"
            placeholder="Enter your password"
          >

        </div>

        @error('password')
          <div class="error-text">
            {{ $message }}
          </div>
        @enderror

      </div>

      <!-- FORGOT -->

      <div class="forgot-wrap">

        <a
          href="{{ route('user.forgot.password') }}"
          id="forgot_password"
        >
          Forgot password?
        </a>

      </div>

      <!-- BUTTONS -->

      <div class="btn-group">

        <button type="submit" class="btn-primary">
          Login
        </button>

        <a href="/register" class="btn-link btn-outline">
          Register
        </a>
        
   
    </form>
    

  </div>

</div>


@endsection


