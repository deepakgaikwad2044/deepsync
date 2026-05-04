@extends("layouts.homel")

@section("content")
<header class="header">
  <div class="container nav">
    <div class="logo">
      <img src="/brands/logo.png" alt="Deep Sync Logo" class="brand-logo">
      <span class="brand-name">Deep Sync</span>
    </div>

    <a href="https://github.com/deepakgaikwad2044/deepsync"
       class="btn-outline" target="_blank">
       GitHub
    </a>
  </div>
</header>

<main class="container">

  <h1 class="title">
    Welcome to {{ env('APP_NAME') }}
  </h1>

  <p class="lead">
    Build powerful, secure, and scalable PHP applications with ease and speed.
  </p>

  <a href="https://github.com/deepakgaikwad2044/deepsync"
     class="btn-primary" target="_blank">
     Get Started 🚀
  </a>

 <section class="features">

  <div class="feature">
    <div class="icon">⚡</div>
    <h3>Lightweight & Fast</h3>
    <p>Optimized for performance with minimal overhead.</p>
  </div>

  <div class="feature">
    <div class="icon">🔒</div>
    <h3>Secure by Design</h3>
    <p>Protection from common vulnerabilities out of the box.</p>
  </div>

  <div class="feature">
    <div class="icon">💻</div>
    <h3>Clean Architecture</h3>
    <p>Maintainable, scalable, and modular structure.</p>
  </div>

  <div class="feature">
    <div class="icon">⚙️</div>
    <h3>Developer Friendly</h3>
    <p>Simple APIs and tools to boost productivity.</p>
  </div>

  <!-- 🔥 NEW FEATURES -->

  <div class="feature">
    <div class="icon">🧩</div>
    <h3>Blade Templating Engine</h3>
    <p>Custom Blade-like engine with sections, layouts, and reusable components.</p>
  </div>

  <div class="feature">
    <div class="icon">🔌</div>
    <h3>Modular System</h3>
    <p>Build applications using independent modules for better scalability.</p>
  </div>

  <div class="feature">
    <div class="icon">🗄️</div>
    <h3>Database Ready</h3>
    <p>Seamless integration with MySQL and SQLite for fast data handling.</p>
  </div>

  <div class="feature">
    <div class="icon">🔐</div>
    <h3>Authentication System</h3>
    <p>Built-in login, register, and session handling for secure apps.</p>
  </div>

  <div class="feature">
    <div class="icon">📡</div>
    <h3>Realtime Support</h3>
    <p>WebSocket integration for live updates and dynamic applications.</p>
  </div>

  <div class="feature">
    <div class="icon">🛠️</div>
    <h3>Custom CLI Tools</h3>
    <p>Generate controllers, models, and views using simple commands.</p>
  </div>

</section>
</main>

<footer class="footer">
  <div class="container footer-content">

    <p>
      © {{ date('Y') }} <strong>Deep Sync Framework</strong>
    </p>

    <div class="footer-links">
      <a href="https://github.com/deepakgaikwad2044/deepsync" target="_blank">GitHub</a>
      <a href="{{ route('deep.docs')}}">Docs</a>
      <a href="{{ route('deep.about')}}" class="nav-link">About</a>
    </div>

  </div>
</footer>
@endsection


