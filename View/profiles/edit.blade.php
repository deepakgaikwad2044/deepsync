@extends("layouts.layouts")

@section("content")
@php
    $errors = errors();
    $success = success();
    $old = old();

    if (isset($_SESSION["pass_match"])) {
        unset($_SESSION["pass_match"]);
    }

    $profileImg = $user["avtar"] ?? "/public/images/default.png";
@endphp

<style>
:root{
  --brand:#8e44ad;
  --brand-dark:#6c3483;
}

/* PAGE BG */
body{
  background: linear-gradient(135deg,#f5f6fa,#eef1f5);
}

/* CONTAINER */
.container{
  max-width:760px;
  min-height:100vh;
}

/* CARD */
.profile-card{
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(12px);
  border-radius:20px;
  padding:32px;
  box-shadow:0 20px 50px rgba(0,0,0,.08);
  border:1px solid rgba(0,0,0,0.05);
  transition:0.3s ease;
}

.profile-card:hover{
  transform: translateY(-4px);
}

/* TITLE */
.profile-card h4{
  font-weight:600;
}

/* BACK BUTTON */
.back-link{
  font-size:18px;
  color:#333;
  padding:8px;
  border-radius:10px;
  transition:0.2s ease;
}

.back-link:hover{
  background: rgba(0,0,0,0.06);
}

/* LABEL */
.form-label{
  font-weight:600;
  margin-bottom:6px;
}

/* INPUT */
.form-control{
  border-radius:14px;
  padding:12px 14px;
  border:1px solid rgba(0,0,0,0.1);
  transition:0.25s ease;
}

.form-control:focus{
  border-color:var(--brand);
  box-shadow:0 0 0 3px rgba(142,68,173,.2);
}

/* FILE INPUT SPECIAL */
input[type="file"]{
  padding:10px;
}

/* BUTTON */
.btn-primary{
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  border:none;
  border-radius:14px;
  padding:12px 24px;
  font-weight:500;
  transition:0.25s ease;
}

.btn-primary:hover{
  transform: translateY(-2px);
  box-shadow:0 8px 20px rgba(142,68,173,.3);
}

/* CHANGE PASSWORD LINK */
.change_password_text{
  font-size:.9rem;
  font-weight:600;
  color:var(--brand);
  text-decoration:none;
}

.change_password_text:hover{
  color:var(--brand-dark);
  text-decoration: underline;
}

/* PROFILE IMAGE */
.profile-preview{
  width:140px;
  height:140px;
  border-radius:16px;
  object-fit:cover;
  border:3px solid rgba(142,68,173,.25);
  box-shadow:0 10px 25px rgba(0,0,0,.08);
  transition:0.3s ease;
}

.profile-preview:hover{
  transform: scale(1.05);
}

/* CENTER IMAGE */
.mb-4{
  display:flex;
  justify-content:center;
}

/* ERROR */
.invalid-feedback{
  font-size:13px;
}

/* SMALL ANIMATION */
.form-control, .btn, .profile-preview{
  will-change: transform;
}
</style>

<div class="container d-flex align-items-center justify-content-center">
  <div class="w-100 my-5">

    <div class="profile-card">

      <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('user.dashboard') }}" class="back-link">
          <i class="fas fa-arrow-left"></i>
        </a>
        <h4>Edit User Profile</h4>
      </div>

      <form action="{{ route('user.profile.update') }}" method="post" enctype="multipart/form-data">
        @csrf

        <!-- NAME -->
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input type="text" name="name"
            class="form-control {{ !empty($errors['name']) ? 'is-invalid' : '' }}"
            value="{{ $old['name'] ?? $user['name'] ?? '' }}">

          @if(!empty($errors['name']))
            <div class="invalid-feedback">{{ $errors['name'] }}</div>
          @endif
        </div>

        <!-- EMAIL -->
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email"
            class="form-control {{ !empty($errors['email']) ? 'is-invalid' : '' }}"
            value="{{ $old['email'] ?? $user['email'] ?? '' }}">

          @if(!empty($errors['email']))
            <div class="invalid-feedback">{{ $errors['email'] }}</div>
          @endif
        </div>

        <!-- IMAGE -->
        <div class="mb-3">
          <label class="form-label">Profile Image</label>
          <input type="file" name="profile" id="profile"
            class="form-control {{ !empty($errors['image']) ? 'is-invalid' : '' }}"
            accept="image/*">

          @if(!empty($errors['image']))
            <div class="invalid-feedback">{{ $errors['image'] }}</div>
          @endif
        </div>

        <!-- PREVIEW -->
        <div class="mb-4">
          <img id="profilePreview" src="{{ $profileImg }}" class="profile-preview">
        </div>

        <!-- PASSWORD -->
        <div class="text-end mb-4">
          <a href="{{ route('user.password.edit') }}" class="change_password_text">
            Change Password
          </a>
        </div>

        <button class="btn btn-primary">Update Profile</button>

      </form>

    </div>
  </div>
</div>

@endsection

@section("scripts")
<script>
document.getElementById('profile').addEventListener('change', function(e){
  const file = e.target.files[0];
  if(file){
    document.getElementById('profilePreview').src = URL.createObjectURL(file);
  }
});
</script>
@endsection

