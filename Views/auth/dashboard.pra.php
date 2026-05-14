@extends("layouts.layouts")
@section("content")

<style>

:root{
  --brand:#8e44ad;
  --brand-dark:#6c3483;
  --glass:rgba(255,255,255,0.72);
}

/* ===== BODY ===== */

body{
  min-height:100vh;
  overflow-x:hidden;
  position:relative;

  background:
  radial-gradient(circle at top left,#f3d9ff 0%,transparent 30%),
  radial-gradient(circle at bottom right,#d6e4ff 0%,transparent 30%),
  linear-gradient(180deg,#ffffff,#f5f6fa);

  animation:bgMove 10s ease infinite alternate;
}

@keyframes bgMove{
  from{
    background-position:left top,right bottom;
  }
  to{
    background-position:left 80px,right -80px;
  }
}

/* ===== MAIN CONTENT ===== */

.main_wrapper{
  position:relative;
  z-index:2;
}

/* ===== PRANCHI INTRO ===== */

.pranchi_intro{
  position:fixed;
  inset:0;
  z-index:99999;
  background:#fff;

  display:flex;
  align-items:center;
  justify-content:center;
  flex-direction:column;

  overflow:hidden;

  animation:introHide .9s ease 5s forwards;
}

@keyframes introHide{
  to{
    opacity:0;
    visibility:hidden;
    pointer-events:none;
  }
}

/* GLOW */

.pranchi_glow{
  position:absolute;
  width:420px;
  height:420px;

  background:
  radial-gradient(circle,var(--brand),transparent 70%);

  filter:blur(80px);
  opacity:.35;

  animation:pulseGlow 3s infinite alternate;
}

@keyframes pulseGlow{
  from{
    transform:scale(1);
  }
  to{
    transform:scale(1.4);
  }
}

/* LOGO */

.pranchi_logo{
  width:120px;
  height:120px;

  border-radius:30px;

  background:
  linear-gradient(135deg,var(--brand),#c56cf0);

  display:flex;
  align-items:center;
  justify-content:center;

  font-size:55px;
  font-weight:800;
  color:#fff;

  box-shadow:
  0 0 40px rgba(142,68,173,.45),
  0 0 90px rgba(142,68,173,.25);

  animation:
  logoFloat 2.5s ease infinite alternate,
  logoEnter 1.2s ease;
}

@keyframes logoEnter{
  from{
    transform:scale(.2) rotate(-180deg);
    opacity:0;
  }
  to{
    transform:scale(1) rotate(0);
    opacity:1;
  }
}

@keyframes logoFloat{
  from{
    transform:translateY(-5px);
  }
  to{
    transform:translateY(12px);
  }
}

/* TITLE */

.pranchi_title{
  margin-top:25px;

  font-size:45px;
  font-weight:800;
  letter-spacing:4px;

  color:#222;

  animation:textReveal 1.5s ease;
}

.pranchi_title span{
  color:var(--brand);
  text-shadow:0 0 25px rgba(142,68,173,.35);
}

@keyframes textReveal{
  from{
    opacity:0;
    transform:translateY(40px);
    letter-spacing:15px;
  }
  to{
    opacity:1;
    transform:translateY(0);
    letter-spacing:4px;
  }
}

/* SUBTITLE */

.pranchi_subtitle{
  margin-top:12px;

  color:#666;
  font-size:15px;
  letter-spacing:2px;

  animation:fadeUp 2s ease;
}

@keyframes fadeUp{
  from{
    opacity:0;
    transform:translateY(30px);
  }
  to{
    opacity:1;
    transform:translateY(0);
  }
}

/* LOADING BAR */

.loading_line{
  width:240px;
  height:5px;

  background:#eee;
  border-radius:50px;

  margin-top:35px;
  overflow:hidden;
}

.loading_line span{
  display:block;
  height:100%;
  width:0%;

  border-radius:50px;

  background:
  linear-gradient(90deg,var(--brand),#d980fa);

  animation:loading 4s ease forwards;
}

@keyframes loading{
  to{
    width:100%;
  }
}

/* FLOATING PARTICLES */

.particle{
  position:absolute;
  border-radius:50%;
  background:rgba(142,68,173,.15);

  animation:floatParticle linear infinite;
}

.particle:nth-child(1){
  width:12px;
  height:12px;
  left:10%;
  animation-duration:12s;
}

.particle:nth-child(2){
  width:18px;
  height:18px;
  left:25%;
  animation-duration:9s;
}

.particle:nth-child(3){
  width:10px;
  height:10px;
  left:45%;
  animation-duration:13s;
}

.particle:nth-child(4){
  width:15px;
  height:15px;
  left:70%;
  animation-duration:10s;
}

.particle:nth-child(5){
  width:22px;
  height:22px;
  left:85%;
  animation-duration:14s;
}

@keyframes floatParticle{
  from{
    transform:translateY(100vh) scale(0);
    opacity:0;
  }

  20%{
    opacity:1;
  }

  to{
    transform:translateY(-120px) scale(1.5);
    opacity:0;
  }
}

/* ===== PRANCHI MEANING ===== */

.pranchi_meaning{
  display:flex;
  justify-content:center;
  padding:50px 20px;

  animation:meaningFade 1.2s ease;
}

@keyframes meaningFade{
  from{
    opacity:0;
    transform:translateY(40px);
  }
  to{
    opacity:1;
    transform:translateY(0);
  }
}

.meaning_card{
  width:100%;
  max-width:850px;

  background:rgba(255,255,255,0.7);

  backdrop-filter:blur(12px);
  -webkit-backdrop-filter:blur(12px);

  border:1px solid rgba(255,255,255,0.4);

  border-radius:30px;

  padding:40px;

  box-shadow:
  0 10px 40px rgba(0,0,0,0.08),
  0 0 50px rgba(142,68,173,0.08);
}

.meaning_title{
  text-align:center;

  font-size:50px;
  font-weight:800;

  letter-spacing:4px;

  margin-bottom:10px;

  color:#222;
}

.meaning_title span{
  color:var(--brand);
}

.meaning_subtitle{
  text-align:center;

  color:#777;

  margin-bottom:40px;

  letter-spacing:2px;
}

.meaning_list{
  display:flex;
  flex-direction:column;
  gap:18px;
}

.meaning_item{
  display:flex;
  align-items:flex-start;
  gap:18px;

  padding:18px 20px;

  border-radius:20px;

  background:rgba(142,68,173,0.04);

  transition:.35s ease;

  border:1px solid rgba(142,68,173,0.08);
}

.meaning_item:hover{
  transform:translateX(8px) scale(1.01);

  background:rgba(142,68,173,0.08);

  box-shadow:0 10px 25px rgba(142,68,173,0.08);
}

.meaning_item span{
  min-width:55px;
  height:55px;

  border-radius:18px;

  display:flex;
  align-items:center;
  justify-content:center;

  background:
  linear-gradient(135deg,var(--brand),#c56cf0);

  color:#fff;

  font-size:24px;
  font-weight:800;

  box-shadow:0 8px 20px rgba(142,68,173,.25);
}

.meaning_item h4{
  margin:0;
  font-size:20px;
  font-weight:700;
  color:#222;
}

.meaning_item p{
  margin-top:5px;
  margin-bottom:0;

  color:#666;
  line-height:1.6;
}

/* ===== NAVBAR ===== */

.top_navbar{
  background:var(--glass);

  backdrop-filter:blur(12px);
  -webkit-backdrop-filter:blur(12px);

  box-shadow:0 5px 25px rgba(0,0,0,0.06);

  padding:12px 16px;

  position:sticky;
  top:0;
  z-index:1000;

  border-bottom:1px solid rgba(255,255,255,.4);
}

/* LOGO */

.brand_logo{
  border:2px solid rgba(142,68,173,.18);

  padding:2px;

  transition:.4s ease;

  box-shadow:0 0 20px rgba(142,68,173,.12);
}

.brand_logo:hover{
  transform:rotate(10deg) scale(1.08);

  border-color:var(--brand);

  box-shadow:0 0 35px rgba(142,68,173,.35);
}

/* USER */

.user_profile{
  width:42px;
  height:42px;

  object-fit:cover;

  border:2px solid var(--brand);

  transition:.3s ease;
}

.user_profile:hover{
  transform:scale(1.08);
}

.username{
  font-size:14px;
  font-weight:700;
  color:#222;
}

/* USER BOX */

.top_navbar .d-flex.align-items-center.mr-4{
  padding:8px 12px;
  border-radius:16px;

  transition:.3s ease;
}

.top_navbar .d-flex.align-items-center.mr-4:hover{
  background:rgba(142,68,173,.08);

  transform:translateY(-2px);
}

/* MENU ICON */

.bars_icon{
  font-size:22px;
  cursor:pointer;

  padding:10px 12px;
  border-radius:14px;

  transition:.3s ease;

  color:#333;
}

.bars_icon:hover{
  background:rgba(142,68,173,.12);

  transform:rotate(90deg) scale(1.08);

  color:var(--brand);
}

/* MOBILE */

@media(max-width:768px){

  .meaning_card{
    padding:25px;
  }

  .meaning_title{
    font-size:36px;
  }

  .meaning_item{
    flex-direction:column;
  }

}

</style>

<!-- ===== PRANCHI INTRO ===== -->

<div class="pranchi_intro">

  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>

  <div class="pranchi_glow"></div>

  <div class="pranchi_logo">
    P
  </div>

  <div class="pranchi_title">
    PRAN<span>CHI</span>
  </div>

  <div class="pranchi_subtitle">
    NEXT GENERATION BLADE ENGINE
  </div>

  <div class="loading_line">
    <span></span>
  </div>

</div>

<!-- ===== MAIN WRAPPER ===== -->

<div class="main_wrapper">

  <!-- SIDEBAR -->
  @include("layouts.sidemenu")
  
  <!-- MAIN CONTENT -->
  <div class="flex-grow-1">
      
    <!-- TOP NAVBAR -->
    <nav class="navbar navbar-expand-lg top_navbar">

      <div class="container-fluid">

        <a class="navbar-brand"
           href="{{ route('home')}}">

          <img src="/brands/logo.png"
               width="50"
               height="50"
               class="img-fluid rounded-circle brand_logo"/>

        </a>

        <div class="d-flex align-items-center">

          <a href="{{ route('user.profile.edit')}}">

            <div class="d-flex align-items-center mr-4">

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

    <!-- ===== PRANCHI FULL FORM ===== -->

    <div class="pranchi_meaning">

      <div class="meaning_card">

        <h1 class="meaning_title">
          PRAN<span>CHI</span>
        </h1>

        <p class="meaning_subtitle">
          Next Generation Blade Engine
        </p>

        <div class="meaning_list">

          <div class="meaning_item">
            <span>P</span>
            <div>
              <h4>Powerful</h4>
              <p>Fast rendering aur strong template features.</p>
            </div>
          </div>

          <div class="meaning_item">
            <span>R</span>
            <div>
              <h4>Reactive</h4>
              <p>Dynamic UI handling aur modern workflow support.</p>
            </div>
          </div>

          <div class="meaning_item">
            <span>A</span>
            <div>
              <h4>Advanced</h4>
              <p>Advanced blade directives, layouts aur components.</p>
            </div>
          </div>

          <div class="meaning_item">
            <span>N</span>
            <div>
              <h4>Native</h4>
              <p>Core PHP ke saath native integration.</p>
            </div>
          </div>

          <div class="meaning_item">
            <span>C</span>
            <div>
              <h4>Clean</h4>
              <p>Clean syntax aur readable template structure.</p>
            </div>
          </div>

          <div class="meaning_item">
            <span>H</span>
            <div>
              <h4>Hybrid</h4>
              <p>Traditional + modern templating approach.</p>
            </div>
          </div>

          <div class="meaning_item">
            <span>I</span>
            <div>
              <h4>Intelligent</h4>
              <p>Smart caching aur optimized rendering system.</p>
            </div>
          </div>

        </div>

      </div>

    </div>

  </div>

</div>

@endsection