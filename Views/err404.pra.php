@extends('layouts.layouts')
@section('content') 
<style>
:root{
  --brand:#8e44ad;
  --brand-dark:#6c3483;
  --gradient: linear-gradient(135deg, #8e44ad, #6f42c1);
}

body{
  margin:0;
  font-family: system-ui, sans-serif;
  background: radial-gradient(circle at top, #f3eaff, #ffffff);
  overflow:hidden;
}

.error-wrapper{
  height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  text-align:center;
  padding:20px;
}

.card{
  background:#fff;
  padding:50px 30px;
  border-radius:20px;
  box-shadow:0 25px 60px rgba(142,68,173,.2);
  max-width:500px;
  width:100%;
  position:relative;
  overflow:hidden;
}

.card{
  display:flex;
  flex-direction:column;
  align-items:center;
}

/* floating glow circles */
.card::before,
.card::after{
  content:'';
  position:absolute;
  width:120px;
  height:120px;
  border-radius:50%;
  background:rgba(142,68,173,.15);
  animation: float 6s infinite ease-in-out;
}

.card::before{ top:-40px; left:-40px; }
.card::after{ bottom:-40px; right:-40px; animation-delay:2s; }

@keyframes float{
  0%,100%{ transform:translateY(0); }
  50%{ transform:translateY(20px); }
}

.logo{
  width:120px;
  margin:0 auto 10px auto;
  display:block;
  filter: drop-shadow(0 10px 15px rgba(0,0,0,.1));
}

.code{
  font-size:6rem;
  font-weight:900;
  background:var(--gradient);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  animation: bounce 2s infinite;
}

@keyframes bounce{
  0%,100%{ transform:translateY(0); }
  50%{ transform:translateY(-15px); }
}

.message{
  font-size:1.2rem;
  color:#555;
  margin-top:10px;
}

.btn-home{
  margin-top:25px;
  display:inline-block;
  padding:12px 22px;
  border-radius:12px;
  background:var(--gradient);
  color:#fff;
  text-decoration:none;
  font-weight:500;
  transition:.3s;
  box-shadow:0 10px 25px rgba(142,68,173,.3);
}

.btn-home:hover{
  transform:scale(1.05);
}
</style>

<div class="error-wrapper">

  <div class="card">

    <img src="/brands/logo1.png" class="logo">

    <div class="code">
      {{ $code ?? 404 }}
    </div>

    <div class="message">
      {{ $message ?? "Oops! Page vanished into the void 🚀" }}
    </div>

    <a href="{{ route('home') }}" class="btn-home">
      🚀 Go Back Home
    </a>

  </div>

</div>
@endsection
