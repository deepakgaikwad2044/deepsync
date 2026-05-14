@extends("layouts.layouts")

@section("content")

@php
    $profileImg = $user["avtar"] ?? asset("images/default.png");
@endphp

<style>
:root{
  --brand:#8e44ad;
  --brand-dark:#6c3483;
}

/* FULL BACKGROUND */
body{
  background: radial-gradient(circle at top, #f3eaff, #eef1f5);
}

/* CENTER WRAPPER */
.container{
  max-width:780px;
  min-height:100vh;
}

/* CARD - NEW PREMIUM LOOK */
.profile-card{
  background: #ffffff;
  border-radius: 24px;
  padding: 36px;
  box-shadow: 0 25px 60px rgba(0,0,0,.10);
  border: 1px solid rgba(0,0,0,0.06);
  position: relative;
}

/* HEADER */
.profile-header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  margin-bottom:25px;
}

.profile-header h4{
  font-weight:700;
  letter-spacing:.5px;
}

/* BACK BUTTON */
.back-link{
  width:42px;
  height:42px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius:12px;
  background:#f3f3f3;
  text-decoration:none;
  color:#333;
}

/* IMAGE */
.profile-preview{
  width:130px;
  height:130px;
  border-radius:50%;
  object-fit:cover;
  border:4px solid rgba(142,68,173,.25);
  box-shadow:0 10px 25px rgba(0,0,0,.08);
  transition:0.3s ease;
}

.profile-preview:hover{
  transform: scale(1.08);
}

/* CENTER IMAGE */
.preview-wrapper{
  display:flex;
  justify-content:center;
  margin-bottom:25px;
}

/* LABEL */
.form-label{
  font-weight:600;
  font-size:14px;
  margin-bottom:6px;
}

/* INPUT */
.form-control{
  border-radius:12px;
  padding:12px 14px;
  border:1px solid #ddd;
  transition:0.25s;
}

.form-control:focus{
  border-color:var(--brand);
  box-shadow:0 0 0 3px rgba(142,68,173,.15);
}

/* BUTTONS */
.btn-primary{
  background:linear-gradient(135deg,var(--brand),var(--brand-dark));
  border:none;
  border-radius:14px;
  padding:12px;
  font-weight:600;
  width:100%;
}

/* PASSWORD LINK */
.change_password_text{
  font-size:13px;
  color:var(--brand);
  font-weight:600;
  text-decoration:none;
}

.change_password_text:hover{
  text-decoration:underline;
}

/* ERROR */
.invalid-feedback{
  font-size:12px;
  color:#e74c3c;
}

/* FORM GROUP SPACING */
.mb-3{
  margin-bottom:18px !important;
}
</style>

<div class="container d-flex align-items-center justify-content-center">
  <div class="w-100 my-5">

    <div class="profile-card">

      <!-- HEADER -->
      <div class="profile-header">
        <a href="{{ route('user.dashboard') }}" class="back-link">
          <i class="fas fa-arrow-left"></i>
        </a>

        <h4>Edit Profile</h4>

        <div style="width:42px"></div>
      </div>

      <!-- IMAGE -->
      <div class="preview-wrapper">
        <img id="profilePreview" src="{{ $profileImg }}" class="profile-preview">
      </div>

      <!-- FORM -->
      <form action="{{ route('user.profile.update') }}" method="post" enctype="multipart/form-data">
        @csrf

        <!-- NAME -->
        <div class="mb-3">
          <label class="form-label">Full Name</label>

          <input type="text" name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $user['name'] ?? '') }}"
            placeholder="Enter your name">

          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- EMAIL -->
        <div class="mb-3">
          <label class="form-label">Email Address</label>

          <input type="email" name="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $user['email'] ?? '') }}"
            placeholder="Enter your email">

          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- IMAGE -->
        <div class="mb-3">
          <label class="form-label">Profile Image</label>

          <input type="file" name="profile" id="image"
            class="form-control @error('profile') is-invalid @enderror"
            accept="image/*">

          @error('profile')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- PASSWORD -->
        <div class="text-end mb-3">
          <a href="{{ route('user.password.edit') }}" class="change_password_text">
            Change Password
          </a>
        </div>

        <!-- BUTTON -->
        <button class="btn btn-primary">
          Update Profile
        </button>

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