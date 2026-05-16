@extends("layouts.layouts")
@section("content")

<style>

/* ================================
   ROOT
================================ */

:root{
  --brand:#8e44ad;
  --brand-dark:#6c3483;

  --brand-gradient:
  linear-gradient(135deg,#6f42c1,#8e44ad);

  --glass:rgba(255,255,255,0.72);
}

/* ================================
   BODY
================================ */

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

/* ================================
   MAIN WRAPPER
================================ */

.main_wrapper{
  position:relative;
  z-index:2;
}

/* ================================
   INTRO SCREEN
================================ */

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

/* ================================
   GLOW
================================ */

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

/* ================================
   LOGO
================================ */

.pranchi_logo{
  width:120px;
  height:120px;

  border-radius:2rem!important;

  display:flex;
  align-items:center;
  justify-content:center;

  box-shadow:
  0 0 40px rgba(142,68,173,.45),
  0 0 90px rgba(142,68,173,.25);

  animation:
  logoFloat 2.5s ease infinite alternate,
  logoEnter 1.2s ease;
}

.pranchi_logo img{
  border-radius:2rem!important;
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

/* ================================
   SUBTITLE
================================ */

.pranchi_subtitle{
  margin-top:14px;

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

/* ================================
   LOADING BAR
================================ */

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

  background:var(--brand-gradient);

  animation:loading 4s ease forwards;
}

@keyframes loading{
  to{
    width:100%;
  }
}

/* ================================
   PARTICLES
================================ */

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

/* ================================
   TOP NAVBAR
================================ */

.top_navbar{
  background:rgba(255,255,255,0.85);

  backdrop-filter:blur(12px);
  -webkit-backdrop-filter:blur(12px);

  box-shadow:0 5px 25px rgba(0,0,0,0.06);

  padding:12px 16px;

  position:sticky;
  top:0;
  z-index:1000;

  border-bottom:1px solid rgba(255,255,255,0.4);
}

/* ================================
   BRAND LOGO
================================ */

.brand_logo{
  border:2px solid rgba(142,68,173,.18);

  box-shadow:
  0 0 20px rgba(142,68,173,.12);

  transition:.3s ease;
}

.brand_logo:hover{
  transform:rotate(5deg) scale(1.05);

  border-color:var(--brand);

  box-shadow:
  0 0 35px rgba(142,68,173,.35);
}

/* ================================
   USER PROFILE
================================ */

.user_profile{
  width:40px;
  height:40px;

  object-fit:cover;

  border:2px solid var(--brand);
}

.username{
  font-size:14px;
  font-weight:600;
  color:#222;
}

/* ================================
   USER BOX
================================ */

.top_navbar .d-flex.align-items-center.mr-4{
  padding:6px 10px;

  border-radius:12px;

  transition:.25s ease;
}

.top_navbar .d-flex.align-items-center.mr-4:hover{
  background:rgba(142,68,173,.08);
}

/* ================================
   HAMBURGER ICON
================================ */

.bars_icon{
  font-size:22px;

  cursor:pointer;

  padding:8px 10px;

  border-radius:10px;

  transition:.25s ease;

  color:#333;
}

.bars_icon:hover{
  background:rgba(142,68,173,.12);

  color:var(--brand);

  transform:scale(1.1);
}

/* ================================
   PRANCHI MEANING SECTION
================================ */

.pranchi_meaning{
  display:flex;
  justify-content:center;

  padding:60px 20px;

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

/* ================================
   CARD
================================ */

.meaning_card{
  width:100%;
  max-width:900px;

  position:relative;
  overflow:hidden;

  background:rgba(255,255,255,0.72);

  backdrop-filter:blur(14px);
  -webkit-backdrop-filter:blur(14px);

  border:1px solid rgba(255,255,255,0.45);

  border-radius:32px;

  padding:50px 45px;

  box-shadow:
  0 10px 40px rgba(0,0,0,0.08),
  0 0 50px rgba(142,68,173,0.08);
}

.meaning_card::before{
  content:"";

  position:absolute;
  top:-120px;
  right:-120px;

  width:260px;
  height:260px;

  background:
  radial-gradient(circle,
  rgba(142,68,173,.15),
  transparent 70%);
}

/* ================================
   TITLE
================================ */

.meaning_title{
  text-align:center;
  margin-bottom:12px;
}

.meaning_title img{
  max-width:320px;
  width:100%;
}

/* ================================
   SUBTITLE
================================ */

.meaning_subtitle{
  text-align:center;

  font-size:15px;
  color:#666;

  margin-bottom:40px;

  letter-spacing:2px;
  text-transform:uppercase;
}

/* ================================
   LIST
================================ */

.meaning_list{
  display:flex;
  flex-direction:column;
  gap:20px;
}

/* ================================
   ITEM
================================ */

.meaning_item{
  display:flex;
  align-items:flex-start;
  gap:20px;

  padding:22px 24px;

  border-radius:24px;

  background:rgba(142,68,173,0.04);

  border:1px solid rgba(142,68,173,0.08);

  transition:.35s ease;
}

.meaning_item:hover{
  transform:translateY(-4px) scale(1.01);

  background:rgba(142,68,173,0.08);

  box-shadow:
  0 12px 30px rgba(142,68,173,0.10);
}

/* ================================
   ICON
================================ */

.meaning_item span{
  min-width:58px;
  height:58px;

  border-radius:20px;

  display:flex;
  align-items:center;
  justify-content:center;

  background:var(--brand-gradient);

  color:#fff;

  font-size:24px;
  font-weight:800;

  box-shadow:
  0 8px 20px rgba(142,68,173,.25);
}

/* ================================
   TEXT
================================ */

.meaning_item h4{
  margin-bottom:6px;

  font-size:20px;
  font-weight:700;

  color:#222;
}

.meaning_item p{
  margin-bottom:0;

  color:#666;

  line-height:1.7;

  font-size:15px;
}

/* ================================
   MOBILE
================================ */

@media(max-width:768px){

  .pranchi_logo{
    width:90px;
    height:90px;
  }

  .pranchi_subtitle{
    font-size:13px;
    text-align:center;
    padding:0 15px;
  }

  .loading_line{
    width:180px;
  }

  .particle{
    display:none;
  }

  .pranchi_meaning{
    padding:30px 12px;
  }

  .meaning_card{
    padding:28px 20px;
    border-radius:24px;
  }

  .meaning_title img{
    max-width:220px;
  }

  .meaning_subtitle{
    font-size:12px;
    margin-bottom:25px;
    letter-spacing:1px;
  }

  .meaning_list{
    gap:14px;
  }

  .meaning_item{
    flex-direction:column;
    gap:14px;

    padding:18px;

    border-radius:18px;
  }

  .meaning_item span{
    width:48px;
    height:48px;

    min-width:48px;

    font-size:18px;

    border-radius:16px;
  }

  .meaning_item h4{
    font-size:17px;
  }

  .meaning_item p{
    font-size:13px;
    line-height:1.6;
  }

  .username{
    font-size:12px;
  }

  .user_profile{
    width:35px;
    height:35px;
  }

  .bars_icon{
    font-size:18px;
    padding:8px 10px;
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
    <img class="img-fluid" src="/brands/pra.jpg">
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
       <img class="img-fluid" src="/brands/pra2.png">
        </h1>

        <p class="meaning_subtitle">
          Next Generation Blade Engine
        </p>

        <div class="meaning_list">

  <div class="meaning_item">
    <span>P</span>
    <div>
      <h4>Powerful</h4>
      <p>High-speed rendering with robust template capabilities.</p>
    </div>
  </div>

  <div class="meaning_item">
    <span>R</span>
    <div>
      <h4>Reactive</h4>
      <p>Dynamic UI handling with a modern development workflow.</p>
    </div>
  </div>

  <div class="meaning_item">
    <span>A</span>
    <div>
      <h4>Advanced</h4>
      <p>Advanced directives, layouts, and reusable components.</p>
    </div>
  </div>

  <div class="meaning_item">
    <span>N</span>
    <div>
      <h4>Native</h4>
      <p>Seamless integration with core PHP architecture.</p>
    </div>
  </div>

  <div class="meaning_item">
    <span>C</span>
    <div>
      <h4>Clean</h4>
      <p>Readable syntax with a clean and maintainable structure.</p>
    </div>
  </div>

  <div class="meaning_item">
    <span>H</span>
    <div>
      <h4>Hybrid</h4>
      <p>A perfect blend of traditional and modern templating.</p>
    </div>
  </div>

  <div class="meaning_item">
    <span>I</span>
    <div>
      <h4>Intelligent</h4>
      <p>Smart caching and optimized rendering performance.</p>
    </div>
  </div>

</div>

      </div>

    </div>

  </div>

</div>

@endsection