  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nutri App</title>

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  </head>
  <body>

  <header class="d-flex justify-content-between align-items-center px-4 py-3 bg-light shadow-sm">
    <div class="logo fw-bold fs-4 text-success">NUTRI APP</div>

    <nav>
      <ul class="nav">
        <li class="nav-item"><a class="nav-link text-dark" href="{{ route('home') }}">Home</a></li>
        <li class="nav-item"><a class="nav-link text-dark" href="{{ route('buyer.products.index') }}">Shop</a></li>
        <li class="nav-item"><a class="nav-link text-dark" href="#about">About Us</a></li>
        <li class="nav-item"><a class="nav-link text-dark" href="{{ url('/#contact-us') }}">Contact Us</a></li>

      </ul>
    </nav>

    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('buyer.cart.index') }}" class="text-success fs-5">
        <i class="fa-solid fa-cart-shopping"></i>
      </a>

      @auth
        <div class="dropdown">
          <button class="btn btn-outline-success dropdown-toggle" type="button" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
            {{ Auth::user()->name }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownUser">
            @if (Auth::user()->role === 'buyer')
              <li><a class="dropdown-item" href="{{ route('buyer.profile.show') }}">Profile</a></li>
            @elseif (Auth::user()->role === 'farmer')
              <li><a class="dropdown-item" href="{{ route('farmer.dashboard') }}">Dashboard</a></li>
            @endif
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">Logout</button>
              </form>
            </li>
          </ul>
        </div>
      @else
        <a href="{{ route('login') }}">
          <button class="btn btn-success">Login</button>
        </a>
      @endauth
    </div>
  </header>

  <!-- HERO SECTION -->
  <section class="hero">
    <div class="hero-content">
      <h1>GROW YOUR BUSINESS WITH THE BEST CROPS</h1>
      <p>Partner with Nutri App and unlock a world of opportunities in the nutrition and wellness industry, sourcing high-quality crops and empowering local farmers for a healthier future.</p>
      <a href="{{ route('register') }}">
    <button class="btn btn-success px-4 py-2 text-white">Register Now</button>
  </a>
    </div>
    <div class="hero-image">
      <img src="{{ asset('img/hero.png') }}" alt="Hero Farmer">
    </div>
  </section>

  <!-- BEST SELLING -->
  <section class="best-selling py-5 text-center bg-white">
    <h2 class="text-success fw-bold">BEST SELLING</h2>
    <p class="text-muted">Shop smarter, eat fresher! Discover top-selling farm produce on NutriApp today.</p>
    <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
      @forelse ($bestSelling as $product)
        <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none text-dark">
          <div class="card shadow-sm" style="width: 200px;">
            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="{{ $product->name }}">
            <div class="card-body p-2">
              <h6 class="mb-1">{{ ucfirst($product->name) }}</h6>
              <small class="text-success fw-semibold">₱{{ number_format($product->price, 2) }}</small>
            </div>
          </div>
        </a>
      @empty
        <p class="text-muted">No top-selling products found.</p>
      @endforelse
    </div>
  </section>

  <!-- ABOUT US -->
  <section id="about" class="py-5" style="background: #e6ffe6;">
    <div class="container d-flex align-items-center justify-content-center">
      <img src="{{ asset('img/about.png') }}" alt="About" style="max-width: 300px; margin-right: 40px;">
      <div>
        <h2>ABOUT US</h2>
        <p>NutriApp connects farmers to markets, boosting sales, efficiency, and sustainability through direct sales, analytics, and secure transactions.</p>
        <a href="{{ route('register') }}">
    <button class="btn btn-success px-4 py-2 text-white">Register Now</button>
  </a>
      </div>
    </div>
  </section>

<!-- CONTACT US -->
<section id="contact-us" class="py-5" style="background: #ccf0cc;">
  <h2 class="text-center mb-4">CONTACT US</h2>
  <div class="container text-center">
    <p>If you have questions or need support, email us at: <strong>nutrisupport@nutriapp.ph</strong></p>
    <p>For privacy-related inquiries: <strong>nutriprivacy@nutriapp.ph</strong></p>
    <p>Call us at: <strong>0916 309 0162</strong></p>
    <p>Message us on Facebook: <a href="https://facebook.com/nutriapp" target="_blank" class="text-success">facebook.com/nutriapp</a></p>

    <button onclick="document.getElementById('contactForm').classList.remove('d-none')" class="btn btn-success mt-3">
      Send us a message
    </button>

    <!-- Hidden Contact Form -->
    <div id="contactForm" class="mt-4 d-none d-flex justify-content-center">
      <form action="{{ route('contact.submit') }}" method="POST" class="bg-white p-4 rounded shadow-sm" style="max-width: 600px; width: 100%;">
        @csrf
        <div class="mb-3 text-start">
          <label for="name" class="form-label">Your Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3 text-start">
          <label for="email" class="form-label">Your Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3 text-start">
          <label for="message" class="form-label">Message</label>
          <textarea name="message" rows="4" class="form-control" required></textarea>
        </div>
        <div class="text-end">
          <button type="submit" class="btn btn-success px-4">Send</button>
        </div>
      </form>
    </div>
  </div>
</section>



  <footer style="background: #004d26; color: white; padding: 40px 20px;">
<footer style="background: #004d26; color: white; padding: 40px 20px;">
  <div class="d-flex justify-content-between flex-wrap">
    <div>
      <h4>NUTRI APP</h4>
      <p>SOCIAL MEDIA</p>
      <p class="d-flex gap-3">
        <a href="https://facebook.com/nutriapp" target="_blank" class="text-white fs-5">
          <i class="fab fa-facebook"></i>
        </a>
        <a href="https://instagram.com/nutriapp" target="_blank" class="text-white fs-5">
          <i class="fab fa-instagram"></i>
        </a>
      </p>
    </div>
    <div>
      <h4>Shop</h4>
      <p><a href="{{ route('buyer.products.index') }}" class="text-white">Products</a></p>
    </div>
    <div>
      <h4>Company</h4>
      <p><a href="#about" class="text-white">About us</a></p>
      <p><a href="#contact" class="text-white">Support</a></p> <!-- ✅ Now links to Contact Us -->
    </div>
    <div>
      <h4>STAY UP TO DATE</h4>
      <form>
        <input type="email" placeholder="Enter your email" class="form-control mb-2">
        <button type="submit" class="btn btn-light btn-sm">SUBMIT</button>
      </form>
    </div>
  </div>

  <div class="text-center mt-4">
    TERMS &nbsp;&nbsp; PRIVACY &nbsp;&nbsp; COOKIES
  </div>
  <div class="text-center text-white-50 mt-2 small">
    © 2025–2026 NutriTech. All rights reserved.
  </div>
</footer>


  <!-- Scripts -->
  <script src="{{ asset('js/script.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  </body>
  </html>
