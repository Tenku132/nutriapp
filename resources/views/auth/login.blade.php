<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nutri App - Login</title>
    <!-- Google Font: Inter (Variable Font) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0..1,14..32,100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .main {
      display: flex;
      height: 84vh;
    }

    .left-side {
      flex: 1;
      background-color: #1f4321;
      color: white;
      height: 84vh;
    }

    .carousel-item img {
      object-fit: cover;
      width: 100%;
      height: 100%;
    }

    .right-side {
      flex: 1;
      padding: 5% 10%;
    }

    .form-label {
      margin-bottom: 0.5rem;
    }
  </style>
</head>
<!-- HEADER -->
<header>
  <div class="logo">NUTRI APP</div>
  <nav>
    <ul>
      <li><a href="{{ route('home') }}">Home</a></li>
      <li><a href="{{ route('buyer.products.index') }}">Shop</a></li>
      <li><a href="#about">About Us</a></li>
    </ul>
  </nav>
  <a href="{{ route('buyer.cart.index') }}" style="color: green;">
    <i class="fa-solid fa-cart-shopping"></i>
  </a>
  <a href="{{ route('login') }}">
    <button class="login-btn">Login</button>
  </a>
</header>

<body>
  <div class="main">
    <div class="left-side d-flex align-items-center justify-content-center">
      <h1>Welcome Back to Nutri App!</h1>
    </div>
    <div class="right-side">
      <h2 class="mb-4">Login to Your Account</h2>
      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" id="email" name="email" class="form-control" required autofocus>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3 form-check">
          <input type="checkbox" name="remember" class="form-check-input" id="remember">
          <label class="form-check-label" for="remember">Remember Me</label>
        </div>

        <button type="submit" class="btn btn-success">Login</button>
      </form>

      <p class="mt-3">Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
    </div>
  </div>
</body>
</html>
