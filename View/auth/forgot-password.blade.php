@extends("layouts.layouts")
@section("content")

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

/* BUTTON */
.btn-primary{
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  color:#fff;
  position:relative;
  overflow:hidden;
}

/* LOADING */
.btn-loading{
  display:none;
  align-items:center;
  justify-content:center;
  gap:10px;
}

.btn-primary.loading .btn-text{
  display:none;
}

.btn-primary.loading .btn-loading{
  display:flex;
}

/* SPINNER */
.spinner{
  width:18px;
  height:18px;
  border:2px solid rgba(255,255,255,.4);
  border-top-color:#fff;
  border-radius:50%;
  animation:spin .7s linear infinite;
}

@keyframes spin{
  to{
    transform:rotate(360deg);
  }
}

/* DISABLED */
.btn-primary:disabled{
  opacity:.85;
  cursor:not-allowed;
}

</style>

<div class="container">
  <div class="card">

    <div class="brand">
      <h1>Forgot Password</h1>
      <p>We’ll send a reset link to your email</p>
    </div>

@flashError

@flashSuccess

    <form action="{{ route('users.forgot.password') }}" method="POST">

 @csrf

      {{-- EMAIL --}}
      <div class="form-group {{ !empty($errors['email']) ? 'has-error' : '' }}">
        <label>Email Address</label>

        <input type="email"
               name="email"
               placeholder="Enter registered email"
               value="{{ old('email') }}">

                 @error('email')
          <div class="error-text">{{ $message }}</div>
@enderror
      </div>

      <button type="submit" class="btn-primary" id="submitBtn">
    <span class="btn-text">Send Reset Link</span>

    <span class="btn-loading">
        <span class="spinner"></span>
        Sending...
    </span>
</button>

      <a href="{{ route('user.login') }}" class="btn-link">
        Back to Login
      </a>

    </form>

  </div>
</div>

@endsection

@section("scripts")
<script>
$(document).ready(function () {

    $("form").on("submit", function () {

        const btn = $("#submitBtn");

        btn.addClass("loading");
        btn.prop("disabled", true);

    });

});
</script>
@endsection