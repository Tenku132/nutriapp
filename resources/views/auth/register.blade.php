<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - Nutri App</title>
    <!-- Google Font: Inter (Variable Font) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0..1,14..32,100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
    }
    header {
      background-color: #c6f2d2;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
    }
    header .logo {
      font-weight: bold;
      font-size: 1.5rem;
      color: #14532d;
    }
    header nav ul {
      display: flex;
      list-style: none;
      gap: 1.5rem;
      margin: 0;
    }
    header nav ul li a {
      text-decoration: none;
      color: #000;
    }
    .main {
      display: flex;
      height: calc(100vh - 80px);
    }
    .left-side {
      flex: 1;
      background-color: #1f4321;
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }
    .left-side h1 {
      font-size: 2rem;
    }
    .right-side {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #ffffff;
      padding: 3rem;
    }
    .form-box {
      width: 100%;
      max-width: 420px;
    }
    .form-box h2 {
      margin-bottom: 1.5rem;
      font-weight: bold;
    }
    .form-box .btn-primary {
      background-color: #14532d;
      border: none;
    }
    .form-box .btn-primary:hover {
      background-color: #0f3e21;
    }
    .login-btn {
      border: 1px solid #000;
      padding: 0.4rem 1rem;
      background: white;
      border-radius: 4px;
    }
    .login-btn:hover {
      background-color: #f1f1f1;
    }
  </style>
</head>
<body>

  <!-- ✅ HEADER -->
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
      <i class="fa-solid fa-cart-shopping me-3"></i>
    </a>
    <a href="{{ route('login') }}">
      <button class="login-btn">Login</button>
    </a>
  </header>

  <!-- ✅ REGISTER BODY -->
  <div class="main">
    <!-- Left -->
    <div class="left-side">
      <h1>Welcome to Nutri App!<br>Create Your Account</h1>
    </div>

    <!-- Right -->
    <div class="right-side">
      <div class="form-box">
        <h2>Create an Account</h2>
        <form method="POST" action="{{ route('register') }}">
          @csrf

          <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
            @error('name')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="role" class="form-label">Register as</label>
            <select name="role" id="role" class="form-select" required>
              <option value="">Select Role</option>
              <option value="buyer" {{ old('role') == 'buyer' ? 'selected' : '' }}>Buyer</option>
              <option value="farmer" {{ old('role') == 'farmer' ? 'selected' : '' }}>Farmer</option>
            </select>
            @error('role')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>


          <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
            @error('email')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
            @error('password')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('login') }}">Already registered?</a>
            <button type="submit" class="btn btn-primary">Register</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>
</html>
