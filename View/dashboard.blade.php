@extends("layouts.layouts")
@section("content")

<style>

.top_navbar{
  background: #fff;
  box-shadow: 0 4px 12px rgba(0,0,0,.08);
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

                <a class="navbar-brand" href="#">
                    <img src="/brands/logo.png" width="50" height="50"
                         class="img-fluid rounded-circle brand_logo"/>
                </a>

                <div class="d-flex align-items-center">

                    <div class="d-flex align-items-center mr-4 p-2">
                        
                        <img class="user_profile rounded-circle shadow"
                             src="{{ $user['avtar'] }}" />

                        <p class="username mb-0 ml-2">
                            {!! shortname($user['name']) !!}
                        </p>

                    </div>

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