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
  background:rgba(255,255,255,0.85);
  backdrop-filter:blur(12px);

  border-radius:20px;
  padding:34px;

  box-shadow:0 25px 60px rgba(0,0,0,.08);
  border:1px solid rgba(0,0,0,0.05);

  transition:0.3s ease;
}

.register-card:hover{
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

/* INPUT GROUP */
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

/* FORM ERROR BOX */
.form-error{
  background:rgba(231,76,60,.1);
  border-left:4px solid var(--danger);
  padding:12px 14px;
  border-radius:10px;
  font-size:14px;
  color:var(--danger);
  margin-bottom:18px;
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
  .register-card{ padding:26px; }
  .btn-group{ flex-direction:column; }
}

</style>

<div class="register-container">
  <div class="register-card">

    <div class="brand">
      <h1>{{ env('APP_NAME') }}</h1>
      <p>Create your account</p>
    </div>

    <form action="{{ route('user.register.verify') }}" method="post" novalidate>

     @csrf

      @if(!empty($errors["form"]))
        <div class="form-error">
          {{ $errors["form"] }}
        </div>
      @endif

      {{-- NAME --}}
      <div class="form-group {{ !empty($errors['name']) ? 'has-error' : '' }}">
        <label>Name</label>
        <input
          type="text"
          name="name"
          placeholder="Deepak Gaikwad"
          value="{{ old('name') }}"
        >

               @error('name')
          <div class="error-text">{{ $message }}</div>
@enderror
      </div>

      {{-- EMAIL --}}
      <div class="form-group {{ !empty($errors['email']) ? 'has-error' : '' }}">
        <label>Email</label>
        <input
          type="email"
          name="email"
          placeholder="deepak@gmail.com"
          value="{{ old('email') }}"
        >

        
         @error('email')
          <div class="error-text">{{ $message }}</div>
@enderror
      </div>

      {{-- PASSWORD --}}
      <div class="form-group {{ !empty($errors['password']) ? 'has-error' : '' }}">
        <label>Password</label>
        <input
          type="password"
          name="password"
          placeholder="Minimum 6 characters"
        >
    
         @error('password')
          <div class="error-text">{{ $message }}</div>
@enderror
      </div>

      {{-- CONFIRM PASSWORD --}}
      <div class="form-group {{ !empty($errors['confirm_password']) ? 'has-error' : '' }}">
        <label>Confirm Password</label>
        <input
          type="password"
          name="confirm_password"
          placeholder="Confirm password"
        >

       @error('confirm_password')
          <div class="error-text">{{ $message }}</div>
@enderror
      </div>

      <div class="btn-group">
        <button type="submit" class="btn-primary">
          Sign Up
        </button>

        <a href="{{ route('user.login') }}" class="btn-link btn-outline">
          Login
        </a>
      </div>

    </form>

  </div>
</div>

@endsection


