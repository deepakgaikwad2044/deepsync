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

/* BACKGROUND */
body{
  margin:0;
  font-family:system-ui,-apple-system,"Segoe UI",sans-serif;
  background:var(--bg);
  color:var(--text);
}

/* CONTAINER */
.register-container{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:20px;
}

/* 💎 CARD */
.register-card{
  width:100%;
  max-width:460px;

  background:rgba(255,255,255,0.88);
  backdrop-filter:blur(14px);

  border-radius:24px;
  padding:36px;

  box-shadow:
    0 20px 60px rgba(0,0,0,.08),
    0 8px 20px rgba(142,68,173,.08);

  border:1px solid rgba(255,255,255,.5);

  transition:.3s ease;
}

.register-card:hover{
  transform:translateY(-4px);
}

/* BRAND */
.brand{
  text-align:center;
  margin-bottom:28px;
}

.brand-logo{
  width:72px;
  height:72px;
  object-fit:cover;
  border-radius:18px;
  margin-bottom:16px;

  box-shadow:0 10px 25px rgba(142,68,173,.25);
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
  color:var(--muted);
  font-size:14px;
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

.input-icon{
  position:absolute;
  top:50%;
  left:14px;
  transform:translateY(-50%);

  color:#999;
  font-size:15px;
}

input{
  width:100%;
  padding:14px 14px 14px 44px;

  border-radius:16px;
  border:1px solid rgba(0,0,0,.08);

  background:#fff;

  font-size:15px;

  transition:.25s ease;
}

input:focus{
  outline:none;

  border-color:var(--brand);

  box-shadow:
    0 0 0 4px rgba(142,68,173,.12);
}

/* ERROR */
.has-error input{
  border-color:var(--danger);
}

.error-text{
  margin-top:7px;
  font-size:13px;
  color:var(--danger);
}

/* ALERT */
.form-error{
  background:rgba(231,76,60,.08);
  border:1px solid rgba(231,76,60,.15);

  color:var(--danger);

  padding:14px;
  border-radius:14px;

  margin-bottom:20px;
  font-size:14px;
}

/* SUCCESS */
.success-alert{
  background:rgba(39,174,96,.08);
  border:1px solid rgba(39,174,96,.15);

  color:var(--success);

  padding:14px;
  border-radius:14px;

  margin-bottom:20px;
  font-size:14px;
}

/* BUTTONS */
.btn-group{
  display:flex;
  gap:12px;
  margin-top:28px;
}

button,
.btn-link{
  flex:1;

  padding:14px;
  border-radius:16px;

  border:none;

  font-size:15px;
  font-weight:600;

  cursor:pointer;
  text-decoration:none;
  text-align:center;

  transition:.25s ease;
}

/* PRIMARY */
.btn-primary{
  background:linear-gradient(
    135deg,
    var(--brand),
    var(--brand-dark)
  );

  color:#fff;
}

.btn-primary:hover{
  transform:translateY(-2px);

  box-shadow:
    0 12px 25px rgba(142,68,173,.28);
}

/* OUTLINE */
.btn-outline{
  background:#fff;

  border:1px solid rgba(142,68,173,.25);

  color:var(--brand);
}

.btn-outline:hover{
  background:rgba(142,68,173,.06);
}

/* DIVIDER */
.auth-divider{
  position:relative;
  text-align:center;

  margin:24px 0;
}

.auth-divider::before{
  content:"";
  position:absolute;
  top:50%;
  left:0;

  width:100%;
  height:1px;

  background:rgba(0,0,0,.08);
}

.auth-divider span{
  position:relative;

  background:#fff;

  padding:0 12px;

  font-size:13px;
  color:#888;
}

/* MOBILE */
@media(max-width:480px){

  .register-card{
    padding:26px;
  }

  .btn-group{
    flex-direction:column;
  }

}
</style>

<div class="register-container">

  <div class="register-card">

    <!-- BRAND -->
    <div class="brand">

      <img
        src="/brands/logo.png"
        class="brand-logo"
        alt="logo"
      >

      <h1>{{ env('APP_NAME') }}</h1>

      <p>Create your account and start building</p>

    </div>


    <!-- FORM ERROR -->
    @if(!empty($errors['form']))
      <div class="form-error">
        {{ $errors['form'] }}
      </div>
    @endif

    <!-- FORM -->
    <form
      action="{{ route('user.register.verify') }}"
      method="post"
      novalidate
    >

      @csrf

      <!-- NAME -->
      <div class="form-group {{ !empty($errors['name']) ? 'has-error' : '' }}">

        <label>Full Name</label>

        <div class="input-wrapper">

          <i class="fa fa-user input-icon"></i>

          <input
            type="text"
            name="name"
            placeholder="Deepak Gaikwad"
            value="{{ old('name') }}"
          >

        </div>

        @error('name')
          <div class="error-text">
            {{ $message }}
          </div>
        @enderror

      </div>

      <!-- EMAIL -->
      <div class="form-group {{ !empty($errors['email']) ? 'has-error' : '' }}">

        <label>Email Address</label>

        <div class="input-wrapper">

          <i class="fa fa-envelope input-icon"></i>

          <input
            type="email"
            name="email"
            placeholder="deepak@gmail.com"
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

          <i class="fa fa-lock input-icon"></i>

          <input
            type="password"
            name="password"
            placeholder="Minimum 6 characters"
          >

        </div>

        @error('password')
          <div class="error-text">
            {{ $message }}
          </div>
        @enderror

      </div>

      <!-- CONFIRM PASSWORD -->
      <div class="form-group {{ !empty($errors['confirm_password']) ? 'has-error' : '' }}">

        <label>Confirm Password</label>

        <div class="input-wrapper">

          <i class="fa fa-shield-halved input-icon"></i>

          <input
            type="password"
            name="confirm_password"
            placeholder="Confirm password"
          >

        </div>

        @error('confirm_password')
          <div class="error-text">
            {{ $message }}
          </div>
        @enderror

      </div>

      <!-- BUTTONS -->
      <div class="btn-group">

        <button type="submit" class="btn-primary">
          Create Account
        </button>

        <a
          href="{{ route('user.login') }}"
          class="btn-link btn-outline"
        >
          Login
        </a>

      </div>

    </form>

    <!-- DIVIDER -->
    <div class="auth-divider">
      <span>Deep Sync Framework</span>
    </div>

  </div>

</div>

@endsection


