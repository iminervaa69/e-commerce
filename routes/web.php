<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\User\HomeController; 
use App\Http\Controllers\User\CartController as UserCartController;
use App\Http\Controllers\User\ProductController as UserProductController;
use GlennRaya\Xendivel\Xendivel;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentController;



// New Breeze-style Auth Controllers
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

/*
|--------------------------------------------------------------------------
| Public Routes (No authentication required)
|--------------------------------------------------------------------------
*/

// Landing page - accessible to everyone
Route::get('/', [HomeController::class, 'index'])->name('home');

// Public product routes (no auth required)
Route::get('/product/{slug}', [UserProductController::class, 'show'])->name('product.show');
Route::get('/product/{slug}/reviews', [UserProductController::class, 'reviews'])->name('product.reviews');

// Category routes (public)
Route::get('category/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');

// Store routes (public)
Route::get('stores', [StoreController::class, 'index'])->name('stores.index');
Route::get('store/{slug}', [StoreController::class, 'show'])->name('store.show');

Route::prefix('cart')->name('cart.')->group(function () {
    // Cart page
    Route::get('/', [UserCartController::class, 'index'])->name('index');
    
    // AJAX endpoints - REMOVED the redundant /cart/ prefix
    Route::post('/add', [UserCartController::class, 'addItem'])->name('add');
    Route::put('/update/{itemId}', [UserCartController::class, 'updateQuantity'])->name('update');
    Route::delete('/remove/{itemId}', [UserCartController::class, 'removeItem'])->name('remove');
    Route::delete('/clear', [UserCartController::class, 'clearCart'])->name('clear');
    
    // API endpoints for dynamic updates
    Route::get('/data', [UserCartController::class, 'getCartData'])->name('data');
    Route::get('/count', [UserCartController::class, 'getCartCount'])->name('count');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Updated to Breeze Style)
|--------------------------------------------------------------------------
*/

// Routes for guests only (not authenticated)
Route::middleware('guest')->group(function () {
    // Registration Routes
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login Routes  
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Routes for authenticated users only
Route::middleware('auth')->group(function () {
    // Logout Route
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // User Dashboard
    Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');
    
    // User-specific product actions (require auth)
    Route::post('/product/{slug}/review', [UserProductController::class, 'storeReview'])->name('product.store-review');
});

/*
|--------------------------------------------------------------------------
| Protected Resource Routes (Admin/Management)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('site')->name('site.')->group(function () {
    // Basic CRUD routes
    Route::resource('products', ProductController::class);
    Route::resource('stores', StoreController::class);
    Route::resource('categories', CategoryController::class);

    // Nested resource routes
    Route::resource('products.variants', ProductVariantController::class);
    Route::resource('products.images', ProductImageController::class);

    // API route for variants
    Route::get('/api/product/{id}/variants', [ProductVariantController::class, 'getVariants']);
});

/*
|--------------------------------------------------------------------------
| Store Role-Based Routes
|--------------------------------------------------------------------------
*/

// Store admin routes
Route::middleware(['auth', 'store.role:admin'])->group(function () {
    Route::get('site/stores/{store}/edit', [StoreController::class, 'edit'])->name('stores.edit');
    Route::put('site/stores/{store}', [StoreController::class, 'update'])->name('stores.update');
});

// Store owner routes
Route::middleware(['auth', 'store.owner'])->group(function () {
    Route::delete('site/stores/{store}', [StoreController::class, 'destroy'])->name('stores.destroy');
    Route::get('site/stores/{store}/settings', [StoreController::class, 'settings'])->name('stores.settings');
});

/*
|--------------------------------------------------------------------------
| API Routes (Optional - for AJAX requests)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->middleware('auth')->name('api.')->group(function () {
    Route::get('products/featured', [HomeController::class, 'getFeaturedProductsApi']);
    Route::get('categories/popular', [HomeController::class, 'getPopularCategoriesApi']);
    Route::get('search/products', [ProductController::class, 'search']);
});

/*
|--------------------------------------------------------------------------
| Additional Auth Routes (Password Reset, Email Verification, etc.)
|--------------------------------------------------------------------------
*/

// Uncomment this line if you want to add password reset, email verification, etc.
// require __DIR__.'/auth.php';

Route::post('/process-payment', function (Request $request) {
    try {
        $payment = Xendivel::payWithCard($request)
            ->getResponse();
        
        return response()->json([
            'success' => true,
            'data' => $payment
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');

// Payment processing routes
Route::post('/payment/card', [PaymentController::class, 'processCardPayment'])->name('payment.card');
Route::post('/payment/ewallet', [PaymentController::class, 'processEwalletPayment'])->name('payment.ewallet');

// Success and failure pages
Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/failed', [PaymentController::class, 'paymentFailed'])->name('payment.failed');

// E-wallet redirect URLs (required by Xendit)
Route::get('/ewallet/success', [PaymentController::class, 'ewalletSuccess'])->name('ewallet.success');
Route::get('/ewallet/failed', [PaymentController::class, 'ewalletFailed'])->name('ewallet.failed');

// Xendit webhook (no auth needed)
Route::post('/xendit/webhook', [PaymentController::class, 'handleWebhook'])
    ->name('xendit.webhook')
    ->withoutMiddleware(['auth', 'verified']);