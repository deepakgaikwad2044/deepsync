@extends("layouts.layouts")
@section("content")

<style>



/* ===== TOP NAVBAR ===== */
.top_navbar {
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);

  box-shadow: 0 2px 15px rgba(0,0,0,0.08);

  padding: 10px 15px;
  position: sticky;
  top: 0;
  z-index: 1000;
}

/* BRAND LOGO */
.brand_logo {
  border: 2px solid rgba(142, 68, 173, 0.2);
  padding: 2px;
  transition: 0.3s ease;
}

.brand_logo:hover {
  transform: rotate(5deg) scale(1.05);
  border-color: #8e44ad;
}

/* USER SECTION */
.user_profile {
  width: 38px;
  height: 38px;
  object-fit: cover;
  border: 2px solid #8e44ad;
}

.username {
  font-size: 14px;
  font-weight: 500;
  color: #222;
}

/* USER BOX */
.top_navbar .d-flex.align-items-center.mr-4 {
  padding: 6px 10px;
  border-radius: 12px;
  transition: 0.25s ease;
}

.top_navbar .d-flex.align-items-center.mr-4:hover {
  background: rgba(142, 68, 173, 0.08);
}

/* HAMBURGER ICON */
.bars_icon {
  font-size: 22px;
  cursor: pointer;
  padding: 8px 10px;
  border-radius: 10px;
  transition: 0.25s ease;
  color: #333;
}

.bars_icon:hover {
  background: rgba(0,0,0,0.08);
  transform: scale(1.1);
}


.user_profile{
  width:40px;
  height:40px;
  object-fit:cover;
}

.username{
  font-weight:600;
}
</style>

<div class="">
    <!--- sidebar--->
    @include("layouts.sidemenu")
  
     <!--- Main Content --->
    <div class="flex-grow-1">
      
         <!--- Top Navbar ---> 
        <nav class="navbar navbar-expand-lg top_navbar">
            <div class="container-fluid">

                <a class="navbar-brand" href="{{ route('home')}}">
                    <img src="/brands/logo.png" width="50" height="50"
                         class="img-fluid rounded-circle brand_logo"/>
                </a>

                <div class="d-flex align-items-center">

                 <a href = "{{ route('user.profile.edit')}}">  <div class="d-flex align-items-center mr-4 p-2">
                        
                        <img class="user_profile rounded-circle shadow"
                             src="{{ $user['avtar'] }}" />

                        <p class="username mb-0 ml-2">
                            {!! shortname($user['name']) !!}
                        </p>

                    </div> 
                    </a>

                    <i class="fas fa-bars bars_icon"></i>

                </div>

            </div>
        </nav>

    </div>

</div>

@endsection

@section("scripts")

  {{--- TOAST SUCCESS  ---}}
@if(isset($_SESSION['success']))
<script>
$(document).ready(function(){
    toastr.success('{{ $_SESSION["success"] }}', 'Success');
});
</script>
<?php unset($_SESSION['success']); ?>
@endif

@endsection