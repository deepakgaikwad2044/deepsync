<style>
   .sidenav {
     box-shadow: 0 .2rem .2rem rgba(0,0,0,.5);
   }
   
   
   .sidenav {
  position: fixed;
  transform: translateX(-100%);
  transition: all 0.3s linear;
  opacity: 0.2;
  background: #fcfbfb;
  z-index: 3;
}

.sidenav.expand_nav {
  transform: translateX(-5%);
  opacity: 1;
}

.sidenav a {
  color: #110d0d !important;
}

.sidenav .nav-item {
  transition: all 0.5s linear;
  color: #333;
  margin-bottom: 1.5rem;
}

.sidenav .nav-item:hover {
  background: rgba(10, 10, 10, 0.1);
  font-weight: bold;
}


.user_profile {
  width: 2.4rem;
  aspect-ratio: 1 / 1;
  border-radius: 50%;
  object-fit: cover;
}

.username {
  font-size: 1rem;
}

.btn {
  text-transform: capitalize;
}

.top_navbar {
  background: #fff;
}

li {
  margin-bottom: 0.5rem;
}

.logout {
  margin: 0.5rem 0;
  border-top: 2px solid #333;
}
 </style>
 
 <div class="sidenav_container">
    <nav class="container-fluid  sidenav p-3" style="width: 250px; height: 100vh;">
  
 <div class="first_div text-center mt-3">
     
  </div>
  
  
  <ul class="nav flex-column p-3 mt-3">
  
 <!-- menu  Link -->

  
   
    <li class="nav-item ">
      <div class="d-flex align-items-center">
<span class="material-symbols-outlined ">account_circle</span>
    <a href="/users/profile/edit" class="nav-link " 
      >profile</a>
      </div>
    </li>
   
    <li class="nav-item ">
      <div class="d-flex align-items-center">
<span class="material-symbols-outlined ">table</span>
    <a href="<?= route('table.show') ?>" class="nav-link " 
      >datatable</a>
      </div>
    </li>
   
    <li class="nav-item ">
      <div class="d-flex align-items-center">
<span class="material-symbols-outlined ">plug_connect</span>
    <a href="<?= route('websocket.all') ?>" class="nav-link " 
      >websocket</a>
      </div>
    </li>
   
    <li class="nav-item logout">
      <div class="d-flex align-items-center">
        <span class="material-symbols-outlined text-danger">logout</span>
  <form action="<?= route('user.logout') ?>" method="post">
   <?= csrf_field() ?>
    <button type="submit" class="btn logout_btn">
        Logout
    </button>
</form>
    

      </div>
    </li>

  </ul>
  
</nav>
 </div>
