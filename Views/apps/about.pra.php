@extends("layouts.homel")

@section('content')

<section class="about">

  <h1 class="about-title">About Deep Sync 🚀</h1>
  
<a href="{{ route('home') }}" class="back-btn">Back to Home</a>

  <p class="about-lead">
    Deep Sync is a lightweight and powerful PHP framework designed to help developers
    build secure, scalable, and modern web applications with ease.
  </p>

  <div class="about-grid">

    <div class="about-card">
      <h3>⚡ Our Mission</h3>
      <p>
        To simplify web development by providing a clean and efficient architecture
        that developers love to use.
      </p>
    </div>

    <div class="about-card">
      <h3>🧠 Smart Architecture</h3>
      <p>
        Built with modular design and custom Blade engine for better performance
        and maintainability.
      </p>
    </div>

    <div class="about-card">
      <h3>🔒 Security First</h3>
      <p>
        Deep Sync comes with built-in protection against common vulnerabilities
        like XSS and CSRF.
      </p>
    </div>

    <div class="about-card">
      <h3>🚀 Performance</h3>
      <p>
        Optimized core ensures fast rendering and minimal memory usage.
      </p>
    </div>

    <div class="about-card">
      <h3>👨‍💻 Developer Friendly</h3>
      <p>
        Easy routing, templating, and database handling make development smooth.
      </p>
    </div>

    <div class="about-card">
      <h3>🔮 Future Ready</h3>
      <p>
        Designed to support real-time features, APIs, and modern web apps.
      </p>
    </div>

  </div>

  <div class="about-footer">
    <p>Built with ❤️ using pure PHP by passionate developers.</p>
  </div>

</section>

@endsection