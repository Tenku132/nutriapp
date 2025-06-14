<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// 🔧 Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProductDisplayController;
use App\Http\Controllers\PaymentController as PaymongoPaymentController;
use App\Http\Controllers\Buyer\PaymentController;
use App\Http\Controllers\Buyer\BuyerProductController;
use App\Http\Controllers\Buyer\CartController;
use App\Http\Controllers\Buyer\OrderController;
use App\Http\Controllers\Buyer\FollowController;
use App\Http\Controllers\Buyer\BuyerNotificationController;
use App\Http\Controllers\Buyer\BuyerAddressController;
use App\Http\Controllers\Buyer\BuyerFarmerController;
use App\Http\Controllers\Buyer\PaymentTestController;
use App\Http\Controllers\Farmer\ProductController as FarmerProductController;
use App\Http\Controllers\Farmer\TransactionController;
use App\Http\Controllers\Farmer\FarmerDashboardController;
use App\Http\Controllers\Farmer\FarmerProfileController;
use App\Http\Controllers\Farmer\FarmerSettingsController;
use App\Http\Controllers\Farmer\FarmerNotificationController;

// 🌐 Public Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/language/{lang}', [LanguageController::class, 'switch'])->name('language.switch');

// ✉️ Contact Form
Route::post('/contact', function (Request $request) {
    \Log::info('Contact Form Submitted', $request->all());
    return redirect()->back()->with('success', 'Your message has been sent!');
})->name('contact.submit');

// 🧑 Guest Auth Pages
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::get('/register', fn () => view('auth.register'))->name('register');
});

// 🔒 Authenticated Role Redirect
Route::get('/dashboard', function () {
    $user = Auth::user();
    if (!$user) return redirect()->route('login');
    if (!$user->hasVerifiedEmail()) return redirect()->route('verification.notice');

    return match ($user->role) {
        'farmer' => redirect()->route('farmer.dashboard'),
        'buyer' => redirect()->route('buyer.dashboard'),
        default => abort(403),
    };
})->name('dashboard');

// 🌾 Farmer Section
Route::prefix('farmer')->name('farmer.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [FarmerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [FarmerProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [FarmerProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [FarmerProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/payout', [FarmerProfileController::class, 'payout'])->name('profile.payout');
    Route::get('/profile/address', [FarmerProfileController::class, 'address'])->name('profile.address');
    Route::post('/profile/payout', [FarmerProfileController::class, 'updatePayout'])->name('profile.updatePayout');
    Route::post('/profile/address', [FarmerProfileController::class, 'updateAddress'])->name('profile.updateAddress');

    Route::get('/settings', [FarmerSettingsController::class, 'index'])->name('settings');
    Route::patch('/settings/password', [FarmerSettingsController::class, 'updatePassword'])->name('settings.updatePassword');
    Route::post('/settings/documents', [FarmerSettingsController::class, 'uploadDocuments'])->name('settings.uploadDocuments');

    Route::get('/products/templates/{category}', [FarmerProductController::class, 'getTemplates'])->name('products.templates');
    Route::resource('products', FarmerProductController::class);
    Route::delete('/products/bulk-delete', [FarmerProductController::class, 'bulkDelete'])->name('products.bulkDelete');

    Route::get('/orders', [TransactionController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}/update-status', [TransactionController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('/orders/{order}', [TransactionController::class, 'show'])->name('orders.show');

    Route::get('/notifications', [FarmerNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all', [FarmerNotificationController::class, 'markAll'])
    ->name('notifications.markAll');

    // 💬 Messaging
    Route::get('/messages/inbox', [MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages/store', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->whereNumber('user')->name('messages.show');
    Route::post('/messages/{user}/reply', [MessageController::class, 'reply'])->name('messages.reply');
});

// 🛍 Buyer Section
Route::prefix('buyer')->name('buyer.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [BuyerProductController::class, 'index'])->name('dashboard');
    Route::get('/products', [BuyerProductController::class, 'index'])->name('products.index');
    Route::get('/products/search', [BuyerProductController::class, 'search'])->name('products.search');
    Route::get('/products/{category}', [BuyerProductController::class, 'categoryPage'])->name('products.byCategory');
    Route::get('/products/{id}', [ProductDisplayController::class, 'show'])->name('products.show');
    Route::post('/products/{id}/buy-now', [BuyerProductController::class, 'buyNow'])->name('products.buyNow');
    Route::post('/products/{product}/add', [BuyerProductController::class, 'addToCart'])->name('products.add');

    Route::prefix('cart')->name('cart.')->group(function () {
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/checkout', [CartController::class, 'checkoutSelected'])->name('checkout');
        Route::post('/update-quantity', [CartController::class, 'updateQuantity'])->name('updateQuantity');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::delete('/{id}', [CartController::class, 'removeFromCart'])->name('remove');
    });

    // 💳 Payment Flow
    Route::get('/payment/form', fn () => view('buyer.payment.form'))->name('payment.form');
    Route::post('/payment/process', [PaymentController::class, 'processForm'])->name('payment.process');
    Route::post('/payment/mock-process', [PaymentController::class, 'mockSuccess'])->name('payment.mockSuccess');
    Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/failure', [PaymentController::class, 'paymentFailure'])->name('payment.failure');
    Route::get('/payment/review', [PaymentController::class, 'review'])->name('payment.review');
    Route::get('/payment/test-card', [PaymentTestController::class, 'showTestForm'])->name('payment.testCard');
    Route::post('/payment/test-card/process', [PaymentTestController::class, 'createIntent'])->name('payment.testCard.process');

    Route::get('/checkout/preview', [PaymentController::class, 'checkoutPreview'])->name('checkout.preview');
    Route::post('/checkout/create-session', [PaymentController::class, 'createCheckoutSession'])->name('checkout.createSession');
    Route::get('/checkout/thank-you', [PaymentController::class, 'thankYou'])->name('checkout.thankYou');

    // 📦 Orders
    Route::get('/orders/history', [OrderController::class, 'history'])->name('orders.history');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::patch('/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
    Route::patch('/orders/{order}/return', [OrderController::class, 'requestReturn'])->name('orders.requestReturn');
    Route::post('/orders/{order}/buy-again', [OrderController::class, 'buyAgain'])->name('orders.buyAgain');

    // ⭐ Rating System
    Route::get('/orders/{order}/rate', [OrderController::class, 'rate'])->name('orders.rate.create');
    Route::post('/orders/{order}/rate', [OrderController::class, 'submitRating'])->name('orders.rate.store');

    // 👤 Profile
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/address', [BuyerAddressController::class, 'edit'])->name('profile.address');
    Route::post('/profile/address', [BuyerAddressController::class, 'update'])->name('profile.updateAddress');

    // 👥 Following
    Route::post('/follow/{farmerId}', [FollowController::class, 'follow'])->name('follow');
    Route::delete('/unfollow/{id}', [FollowController::class, 'unfollow'])->name('unfollow');
    Route::get('/farmer-profile/{id}', [BuyerFarmerController::class, 'show'])->name('farmer-profile');

    // 💬 Messaging
    Route::get('/messages/inbox', [MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages/store', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->whereNumber('user')->name('messages.show');
    Route::post('/messages/{user}/reply', [MessageController::class, 'reply'])->name('messages.reply');

    // 🔔 Notifications
    Route::get('/notifications', [BuyerNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all', fn () => tap(Auth::user()->unreadNotifications->markAsRead(), fn () => back()->with('success', 'All notifications marked.')))->name('notifications.markAll');
    Route::post('/notifications/mark-ajax', fn () => tap(Auth::user()->unreadNotifications->markAsRead(), fn () => response()->json(['status' => 'ok'])))->name('notifications.markAsReadAjax');
});

// 🛒 Public Product Add-to-Cart & View
Route::get('/product/{id}', [BuyerProductController::class, 'show'])->name('product.show');

// 🌾 Public Farmer Profile (Legacy Route)
Route::get('/farmer/profile/{id}', [FarmerProfileController::class, 'show'])->name('farmer.profile');

// 💳 Paymongo Log (Admin View)
Route::get('/payments', [PaymongoPaymentController::class, 'index'])->name('payments.index');

// 🔄 Paymongo Webhook
Route::post('/webhooks/paymongo', [App\Http\Controllers\WebhookController::class, 'handle']);

// 🛡 Laravel Auth Routes
require __DIR__.'/auth.php';
