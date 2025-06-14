@extends('layouts.app')

@section('content')
<style>
  .profile-wrapper {
    display: flex;
    align-items: flex-start;
    gap: 30px;
    padding: 30px;
  }

  .business-logo {
    width: 160px;
    height: 160px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 0 5px #999;
  }

  .business-info {
    flex-grow: 1;
  }

  .business-info h3 {
    font-size: 1.75rem;
    font-weight: bold;
    color: #2e7d32;
    margin-bottom: 5px;
  }

  .meta-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 15px;
  }

  .meta-item {
    font-size: 0.95rem;
    color: #444;
  }

  .meta-item i {
    margin-right: 8px;
    color: #2e7d32;
  }

  .profile-actions {
    margin-top: 20px;
  }

  .profile-actions button,
  .profile-actions a.btn {
    font-size: 0.9rem;
    padding: 6px 14px;
    margin-right: 10px;
  }

  .description {
    padding: 0 30px 30px 30px;
  }

  .description h4 {
    font-weight: 600;
    margin-bottom: 8px;
  }

  .product-section {
    background-color: #2e7d32;
    margin: 30px;
    padding: 20px;
    border-radius: 10px;
    border: 4px solid #2e7d32;
    box-shadow: 0 0 5px #999;
  }

  .product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 20px;
  }

  .product-card {
    background-color: white;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(46, 125, 50, 0.15);
  }

  .product-card img {
    width: 100%;
    height: 150px;
    border-radius: 10px;
    object-fit: cover;
    margin-bottom: 10px;
  }

  .product-card .star {
    color: gold;
  }

  .star-rating {
    display: inline-block;
    position: relative;
    font-size: 1.25rem;
    line-height: 1;
    unicode-bidi: bidi-override;
    direction: ltr;
  }

  .back-stars,
  .front-stars {
    display: flex;
    position: absolute;
    top: 0;
    left: 0;
    overflow: hidden;
    white-space: nowrap;
    height: 1.25rem;
  }

  .back-stars {
    color: #ccc;
    z-index: 0;
  }

  .front-stars {
    color: #FFA500;
    z-index: 1;
    pointer-events: none;
  }

.progress {
  height: 14px;
  background-color: #f1f1f1;
  border-radius: 10px;
  overflow: hidden;
}

.progress-bar {
  transition: width 0.4s ease;
  border-radius: 10px; /* Makes both ends rounded */
  font-size: 0.75rem;
  line-height: 14px;
  padding-left: 6px;
  padding-right: 6px;
}
</style>


<div class="container">
  <div class="profile-wrapper">
    <img class="business-logo" src="{{ $farmer->business_photo ? asset('storage/business_photos/' . $farmer->business_photo) : asset('img/default-business.jpg') }}" alt="{{ $farmer->business_name }}">

    <div class="business-info">
      <h3>{{ strtoupper($farmer->business_name ?? 'N/A') }}</h3>

      <div class="meta-grid">
        <div class="meta-item"><i class="fa-solid fa-calendar-check"></i> Joined: {{ $farmer->created_at->diffForHumans() }}</div>
        <div class="meta-item"><i class="fa-solid fa-box"></i> Products: {{ $farmer->products->count() }}</div>
        <div class="meta-item"><i class="fa-solid fa-users"></i> Followers: {{ $farmer->followers->count() }}</div>

        @php
          $allRatings = $farmer->products->flatMap->reviews->pluck('rating');
          $avgRating = $allRatings->count() > 0 ? $allRatings->avg() : 0;
        @endphp

        <div class="meta-item d-flex align-items-center gap-2">
          <span>Average Rating:</span>
          @php
            $allRatings = $farmer->products->flatMap->reviews->pluck('rating');
            $avgRating = $allRatings->count() > 0 ? round($allRatings->avg(), 1) : 0;
            $fullStars = floor($avgRating);
            $halfStar = ($avgRating - $fullStars) >= 0.5;
            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
          @endphp

          <span class="text-success d-inline-flex align-items-center">
            @for ($i = 0; $i < $fullStars; $i++)
              <i class="fas fa-star"></i>
            @endfor
            @if ($halfStar)
              <i class="fas fa-star-half-alt"></i>
            @endif
            @for ($i = 0; $i < $emptyStars; $i++)
              <i class="far fa-star"></i>
            @endfor
          </span>

          <strong class="ms-1">{{ number_format($avgRating, 1) }}</strong>
        </div>

      </div>

      <div class="profile-actions mt-3">
        @if ($isFollowing)
          <form action="{{ route('buyer.unfollow', $farmer->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger"><i class="fa-solid fa-minus"></i> Unfollow</button>
          </form>
        @else
          <form action="{{ route('buyer.follow', $farmer->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-plus"></i> Follow</button>
          </form>
        @endif

        <a href="{{ route('buyer.messages.create', ['receiver_id' => $farmer->id]) }}" class="btn btn-outline-success">
          <i class="fa-regular fa-comment-dots"></i> Message
        </a>
      </div>
    </div>
  </div>

  <div class="description">
    <h4>About Seller</h4>
    <p>{{ $farmer->bio ?? 'No bio provided for this farmer.' }}</p>

    <p class="mt-2">
      <strong>Farm Address:</strong>
      {{ implode(', ', array_filter([
        $farmer->street ?? '',
        $farmer->barangay ?? '',
        $farmer->city ?? '',
        $farmer->province ?? '',
        $farmer->zip ?? '',
      ])) ?: 'Not specified by the seller.' }}
    </p>
  </div>

    @php
      $allRatings = $farmer->products->flatMap->reviews;
      $totalRatings = $allRatings->count();
      $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

      foreach ($allRatings as $review) {
          $rounded = round($review->rating);
          if (isset($ratingCounts[$rounded])) {
              $ratingCounts[$rounded]++;
          }
      }
    @endphp

    @if ($totalRatings > 0)
      <div class="container mb-4 px-4">
        <h5 class="fw-bold mb-3">Ratings Breakdown</h5>
        @foreach ([5, 4, 3, 2, 1] as $star)
          @php
            $percent = ($ratingCounts[$star] / $totalRatings) * 100;
          @endphp
          <div class="d-flex align-items-center mb-2 gap-2">
            <div class="text-nowrap" style="width: 50px;">
              {{ $star }} <i class="fa fa-star text-success"></i>
            </div>
            <div class="progress flex-grow-1" style="height: 14px;">
              <div class="progress-bar bg-warning" role="progressbar"
                  style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}"
                  aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div style="width: 50px;" class="text-end small text-muted">
              {{ number_format($percent, 0) }}%
            </div>
          </div>
        @endforeach
        <p class="small text-muted mt-1">Total Ratings: {{ $totalRatings }}</p>
      </div>
    @endif
    
{{-- 🔍 Sticky Top Filter + Sort Bar --}}
<div class="sticky-top bg-white py-2 border-bottom z-10" style="top: 60px;">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center px-4 mb-2 gap-2">
    {{-- Sort Pills --}}
    <div class="d-flex flex-wrap gap-2 align-items-center">
      <span class="fw-semibold me-2">Sort by:</span>

      @php
        $sortOptions = [
          '' => 'Default',
          'price_asc' => 'Price ↑',
          'price_desc' => 'Price ↓',
          'rating_desc' => 'Top Rated',
          'newest' => 'Newest'
        ];
      @endphp

      @foreach ($sortOptions as $value => $label)
        <a href="{{ request()->fullUrlWithQuery(['sort' => $value]) }}"
           class="btn btn-sm {{ request('sort') === $value ? 'btn-success' : 'btn-outline-secondary' }}">
          @if(request('sort') === $value)<i class="fas fa-check-circle me-1"></i>@endif
          {{ $label }}
        </a>
      @endforeach
    </div>

    {{-- Filter Form --}}
    <form method="GET" class="d-flex gap-2 flex-wrap mt-2 mt-md-0">
      {{-- Search --}}
      <input type="text" name="search" class="form-control form-control-sm" placeholder="Search products..."
             value="{{ request('search') }}" style="min-width: 200px;">

      {{-- Category Filter --}}
@php
  $categories = ['Fruits', 'Vegetable', 'Spices', 'Grains', 'Beverages'];
@endphp

<select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
  <option value="">All Categories</option>
  @foreach($categories as $cat)
    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
  @endforeach
</select>



      <button type="submit" class="btn btn-sm btn-success">
        <i class="fa fa-search"></i>
      </button>
    </form>
  </div>

  {{-- Reset Link (shown only if filters active) --}}
  @if(request()->has('sort') || request()->has('search') || request()->has('category'))
    <div class="px-4 small text-muted">
      <i class="fas fa-filter"></i> Filters active —
      <a href="{{ route('buyer.farmer-profile', $farmer->id) }}" class="text-danger">Reset all</a>
    </div>
  @endif
</div>




  <div class="product-section">
    <div class="product-grid">
      @foreach ($products as $product)
        <div class="product-card">
          <a href="{{ route('product.show', $product->id) }}">
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">

            <h4 class="text-uppercase mb-2">{{ $product->name }}</h4>

            {{-- Metadata Line --}}
            <p class="small text-muted mb-1">
              {{ ucfirst($product->ripeness) ?? 'Ripeness N/A' }} • 
              Harvested {{ \Carbon\Carbon::parse($product->harvested_at)->diffForHumans() }} •
              {{ $product->unit === 'piece' ? 'Per Piece' : 'Per Kg' }}
            </p>

            {{-- Badges --}}
            <div class="mb-2">
              @if ($product->stock > 0)
                <span class="badge bg-success">In Stock</span>
              @else
                <span class="badge bg-secondary">Out of Stock</span>
              @endif

              @if (now()->diffInDays($product->harvested_at) <= 2)
                <span class="badge bg-info text-dark">Fresh</span>
              @endif

              @if ($product->sales_count >= 10)
                <span class="badge bg-warning text-dark">Best Seller</span>
              @endif
            </div>

            {{-- Price + Rating --}}
            @php $avg = $product->reviews->avg('rating') ?? 0; @endphp
            <p class="mb-1">
              ₱{{ number_format($product->price, 2) }} |
              <span class="star text-success" title="Based on {{ $product->reviews->count() }} reviews">
                {{ number_format($avg, 1) }} ★
              </span>
            </p>
          </a>

          {{-- Add to Cart --}}
          <form action="{{ route('buyer.cart.add') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <button type="submit" class="btn btn-sm btn-outline-success w-100 mt-2" 
              @if ($product->stock <= 0) disabled @endif>
              <i class="fas fa-cart-plus"></i> Add to Cart
            </button>
          </form>
        </div>

      @endforeach
    </div>
  </div>
    <div class="mt-4 text-center">
    {{ $products->links() }}
    </div>

</div>
@endsection
