<style>

/* ===== OVERLAY ===== */
.sidenav_overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.55);
  backdrop-filter: blur(3px);

  opacity: 0;
  visibility: hidden;
  transition: 0.3s ease;

  z-index: 9998;
}

.sidenav_overlay.active {
  opacity: 1;
  visibility: visible;
}

/* ===== SIDENAV ===== */
.sidenav {
  position: fixed;
  top: 0;
  left: 0;

  width: 270px;
  height: 100vh;

  /* 💎 Premium gradient */
  background: linear-gradient(180deg, #ffffff, #f5f6fa);

  /* glass feel */
  backdrop-filter: blur(10px);

  box-shadow: 10px 0 30px rgba(0,0,0,0.15);

  z-index: 9999;

  transform: translateX(-110%);
  transition: all 0.35s cubic-bezier(0.25, 1, 0.5, 1);

  border-right: 1px solid rgba(0,0,0,0.05);
}

/* OPEN */
.sidenav.expand_nav {
  transform: translateX(0);
}

/* LINKS */
.sidenav a {
  color: #222 !important;
  text-decoration: none;
  font-weight: 500;
  transition: 0.2s ease;
}

/* MENU ITEMS */
.sidenav .nav-item {
  padding: 12px 12px;
  border-radius: 10px;
  margin-bottom: 12px;
  transition: 0.25s ease;
}

/* hover effect */
.sidenav .nav-item:hover {
  background: rgba(142, 68, 173, 0.08);
  transform: translateX(6px);
}

/* logout special */
.logout {
  margin-top: 20px;
  border-top: 1px solid rgba(0,0,0,0.15);
  padding-top: 15px;
}

/* ICON COLOR (if using material icons) */
.sidenav .material-symbols-outlined {
  color: #8e44ad;
  margin-right: 8px;
}

</style>

<!-- OVERLAY -->
<div class="sidenav_overlay" id="sidenavOverlay"></div>

<!-- SIDENAV -->
<div class="sidenav_container">

  <nav class="sidenav p-3" id="sidenav">

    <ul class="nav flex-column p-3 mt-3">

      <li class="nav-item">
        <div class="d-flex align-items-center">
          <span class="material-symbols-outlined">account_circle</span>
          <a href="{{route('user.profile.edit')}}" class="nav-link">profile</a>
        </div>
      </li>

      <li class="nav-item">
        <div class="d-flex align-items-center">
          <span class="material-symbols-outlined">table</span>
          <a href="<?= route('table.show') ?>" class="nav-link">datatable</a>
        </div>
      </li>

      <li class="nav-item">
        <div class="d-flex align-items-center">
          <span class="material-symbols-outlined">plug_connect</span>
          <a href="<?= route('websocket.all') ?>" class="nav-link">websocket</a>
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

  <script>
  
$(document).ready(function(){

  const sidenav = $(".sidenav");
  const overlay = $("#sidenavOverlay");
  const icon = $(".bars_icon");

  $(document).on("click", ".bars_icon", function(){

    sidenav.toggleClass("expand_nav");
    overlay.toggleClass("active");

    if (sidenav.hasClass("expand_nav")) {
      icon.removeClass("fa-bars").addClass("fa-times"); // FA5 CLOSE ICON
    } else {
      icon.removeClass("fa-times").addClass("fa-bars");
    }

  });

  overlay.on("click", function(){

    sidenav.removeClass("expand_nav");
    overlay.removeClass("active");

    icon.removeClass("fa-times").addClass("fa-bars");

  });

});
 
  </script>
