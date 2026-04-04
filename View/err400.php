<?php includes("layouts.header"); ?>
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Arial', sans-serif;
      text-align: center;
      color: #343a40;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }
    .error-container {
      max-width: 600px;
      animation: fadeIn 1s ease-in-out;
    }
    .error-code {
      font-size: 6rem;
      font-weight: bold;
      color: #6c757d;
      animation: bounce 2s infinite;
    }
    .error-message {
      font-size: 1.5rem;
      margin-top: 10px;
    }
    .btn-home {
      margin-top: 20px;
      animation: fadeInUp 1.5s ease-in-out;
    }
    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
      }
      40% {
        transform: translateY(-20px);
      }
      60% {
        transform: translateY(-10px);
      }
    }
    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }
    @keyframes fadeInUp {
      from {
        transform: translateY(20px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }
  </style>
  <div class="error-container">
    
    <img src="/public/brands/logo1.png" class="img-fluid w-50"  />
    <div class="error-code">404</div>
    <p class="error-message">Oops! The page you're looking for doesn't exist.</p>
    <a href="/" class="btn btn-primary btn-home">Go Back to Home</a>
  </div>
  
<?php includes("layouts.footer"); ?>
