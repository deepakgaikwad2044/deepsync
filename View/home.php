<?php
define("BRAND_COLOR", "#8e44ad"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Welcome to Deep Sync Framework</title>
  <style>
    :root {
      --brand-color: <?= BRAND_COLOR ?>;
      --brand-dark: #6c3483;
      --background: #f1f1fe;
      --text-color: #333;
      --text-muted: #666;
      --button-hover: #5a2f7c;
      --brand-gradient: linear-gradient(135deg, #6f42c1, #8e44ad);
      
  
    }
    * {
      box-sizing: border-box;
    }
    
      .brand_logo {
    width: 1rem;
    height: 1rem;
    object-fit: cover;
    
  }
  
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: var(--background);
      color: var(--text-color);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    header {
      background: var(--brand-gradient);
      color: #fff;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .logo {
      font-weight: 900;
      font-size: 1.5rem;
      letter-spacing: 2px;
      user-select: none;
      cursor: default;
    }

    main {
      flex-grow: 1;
      padding: 3rem 1.5rem;
      max-width: 960px;
      margin: 0 auto;
      text-align: center;
    }
    main h1 {
      font-size: 3rem;
      margin-bottom: 0.25rem;
      background:var(--brand-gradient);
      -webkit-background-clip:text;
      -webkit-text-fill-color:transparent;
      
    }
    main p.lead {
      font-size: 1.3rem;
      margin-bottom: 2.5rem;
      color: var(--text-muted);
    }
    .btn-primary {
      background: var(--brand-gradient);
      color: white;
      padding: 0.85rem 3rem;
      font-size: 1.1rem;
      font-weight: 700;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }
    .btn-primary:hover {
      background-color: var(--button-hover);
    }

    .features {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 2rem;
      margin-top: 4rem;
    }
    .feature {
      background: white;
      box-shadow: 0 3px 8px rgb(0 0 0 / 0.1);
      border-radius: 10px;
      padding: 2rem;
      flex: 1 1 280px;
      max-width: 320px;
      text-align: left;
      transition: transform 0.3s ease;
    }
    .feature:hover {
      transform: translateY(-5px);
    }
    .feature h3 {
      color: var(--brand-color);
     background:var(--brand-gradient);
      -webkit-background-clip:text;
      -webkit-text-fill-color:transparent;
      margin-bottom: 1rem;
      font-size: 1.4rem;
    }
    .feature p {
      color: var(--text-muted);
      line-height: 1.5;
      font-size: 1rem;
    }
    .feature-icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
      color: var(--brand-color);
    }

    footer {
      background: #222;
      color: #bbb;
      text-align: center;
      padding: 1rem;
      font-size: 0.9rem;
      margin-top: 3rem;
    }
    footer a {
      color: var(--brand-color);
           background:var(--brand-gradient);
      -webkit-background-clip:text;
      -webkit-text-fill-color:transparent;
      text-decoration: none;
      margin: 0 0.5rem;
      font-weight: 600;
      transition: color 0.3s ease;
    }
    footer a:hover {
      color: var(--button-hover);
    }

    /* Responsive */
    @media (max-width: 700px) {
      .features {
        flex-direction: column;
        align-items: center;
      }
      main h1 {
        font-size: 2.2rem;
      }
      main p.lead {
        font-size: 1.1rem;
      }
    }
    
    
    
    .logo {
  gap: 8px;
  font-weight: 700;
  white-space: nowrap;
}

/* Logo image */
.brand-logo {
  height: 38px;
  transition: all 0.3s ease;
}

/* Brand text */
.brand-name {
  font-size: 1.1rem;
  color: #8e44ad; /* Deep Sync brand */
  color: #fff; /* Deep Sync brand */
}

/* 📱 Mobile */
@media (max-width: 576px) {
  .brand-logo {
    height: 50px;
    border-radius: 50%;
  }

  .brand-name {
    font-size: 2.45rem;
    letter-spacing: -.1rem;
  }
}

/* 💻 Laptop */
@media (min-width: 768px) {
  .brand-logo {
    height: 42px;
  
  }

  .brand-name {
    font-size: 1.15rem;
  }
}

/* 🖥️ Large screens */
@media (min-width: 1200px) {
  .brand-logo {
    height: 50px;
  }

  .brand-name {
    font-size: 1.35rem;
  }
}


.logo:hover {
  opacity: 0.9;
}


  </style>
  <!-- FontAwesome for icons -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

<header>
<div class="logo d-flex align-items-center">
  <img src="/brands/logo.png" alt="Deep Sync Logo" class="brand-logo img-fluid">
  <span class="brand-name">Deep Sync</span>
</div>

</header>

<main>
  <h1>Welcome to <?= env('APP_NAME') ?></h1>
  <p class="lead">Build powerful, secure, and scalable PHP applications with ease and speed.</p>
  <a href="https://github.com/deepakgaikwad2044/deepsync" class="btn-primary" target="_blank" rel="noopener">Get Started</a>

  <section class="features" aria-label="Core Features">
    <article class="feature">
      <div class="feature-icon"><i class="fas fa-bolt"></i></div>
      <h3>Lightweight & Fast</h3>
      <p>Optimized for speed, Deep Sync ensures your apps run smoothly with minimal overhead.</p>
    </article>

    <article class="feature">
      <div class="feature-icon"><i class="fas fa-lock"></i></div>
      <h3>Secure by Design</h3>
      <p>Built-in security features protect your app from common vulnerabilities out of the box.</p>
    </article>

    <article class="feature">
      <div class="feature-icon"><i class="fas fa-code"></i></div>
      <h3>Clean Architecture</h3>
      <p>Modular and easy to extend, keep your codebase organized and maintainable.</p>
    </article>

    <article class="feature">
      <div class="feature-icon"><i class="fas fa-cogs"></i></div>
      <h3>Developer Friendly</h3>
      <p>Comes with helpful tools, clear documentation, and simple APIs to boost your productivity.</p>
    </article>
  </section>
</main>

<footer>
  &copy; <?= date("Y") ?> Deep Sync Framework —

</footer>

</body>
</html>
