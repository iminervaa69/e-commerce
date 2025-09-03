<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\CartController as UserCartController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\AddressController;
use App\Http\Controllers\User\WebhookController;

// Admin Controllers (separate namespace)
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ProductImageController;

// Auth Controllers
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Guest + User accessible) address
|--------------------------------------------------------------------------
*/

// Homepage - accessible to everyone
Route::get('/', [HomeController::class, 'index'])->name('home');

// Product browsing - accessible to everyone
Route::get('/product/{slug}', [UserProductController::class, 'show'])->name('product.show');
Route::get('/product/{slug}/reviews', [UserProductController::class, 'reviews'])->name('product.reviews');

// Category and store browsing - accessible to everyone
Route::get('/category/{slug}', [AdminCategoryController::class, 'show'])->name('category.show');
Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
Route::get('/stores', [AdminStoreController::class, 'index'])->name('stores.index');
Route::get('/store/{slug}', [AdminStoreController::class, 'show'])->name('store.show');

// Cart routes - accessible to everyone (guest cart uses session)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [UserCartController::class, 'index'])->name('index');
    Route::post('/add', [UserCartController::class, 'addItem'])->name('add');
    Route::put('/update/{itemId}', [UserCartController::class, 'updateQuantity'])->name('update');
    Route::delete('/remove/{itemId}', [UserCartController::class, 'removeItem'])->name('remove');
    Route::delete('/clear', [UserCartController::class, 'clearCart'])->name('clear');
    Route::get('/data', [UserCartController::class, 'getCartData'])->name('data');
    Route::get('/count', [UserCartController::class, 'getCartCount'])->name('count');

    // Add this line:
    Route::post('/proceed-to-checkout', [UserCartController::class, 'proceedToCheckout'])->name('proceed-to-checkout');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Registration
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Password Reset
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

/*
|--------------------------------------------------------------------------
| USER ROUTES (Authenticated users only)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Email Verification
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Password Confirmation
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    // User Dashboard
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // User-specific product actions
    Route::post('/product/{slug}/review', [UserProductController::class, 'storeReview'])->name('product.store-review');

    // CHECKOUT - Only authenticated users can checkout
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::get('/success', [CheckoutController::class, 'success'])->name('success');
        Route::get('/failed', [CheckoutController::class, 'failed'])->name('failed');
    });

    // PAYMENT - Only authenticated users can make payments
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::post('/card', [PaymentController::class, 'processCardPayment'])->name('card');
        Route::post('/ewallet', [PaymentController::class, 'processEwalletPayment'])->name('ewallet');
        Route::get('/success', [PaymentController::class, 'paymentSuccess'])->name('success');
        Route::get('/failed', [PaymentController::class, 'paymentFailed'])->name('failed');
    });

    // E-wallet redirect URLs (for authenticated users)
    Route::prefix('ewallet')->name('ewallet.')->group(function () {
        Route::get('/success', [PaymentController::class, 'ewalletSuccess'])->name('success');
        Route::get('/failed', [PaymentController::class, 'ewalletFailed'])->name('failed');
    });

    // ADDRESS MANAGEMENT - Only for authenticated users
    Route::prefix('addresses')->name('addresses.')->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('index');
        Route::get('/api', [AddressController::class, 'getAddresses'])->name('get');
        Route::post('/api', [AddressController::class, 'store'])->name('store');
        Route::get('/api/{id}', [AddressController::class, 'show'])->name('show');
        Route::put('/api/{id}', [AddressController::class, 'update'])->name('update');
        Route::delete('/api/{id}', [AddressController::class, 'destroy'])->name('destroy');
        Route::patch('/api/{id}/default', [AddressController::class, 'setDefault'])->name('setDefault');
        Route::get('/api/provinces', [AddressController::class, 'getProvinces'])->name('provinces');
    });

    // USER PROFILE & ORDERS
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [UserController::class, 'profile'])->name('index');
        Route::put('/', [UserController::class, 'updateProfile'])->name('update');
        Route::get('/orders', [UserController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [UserController::class, 'orderDetail'])->name('order.detail');
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Separate login system - to be implemented later)
|--------------------------------------------------------------------------
*/

// For now, using basic auth middleware - you'll replace this with admin auth later
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Product Management
    Route::resource('products', AdminProductController::class);
    Route::resource('products.variants', ProductVariantController::class);
    Route::resource('products.images', ProductImageController::class);
    Route::get('/api/product/{id}/variants', [ProductVariantController::class, 'getVariants'])
        ->name('products.variants.api');

    // Store Management
    Route::resource('stores', AdminStoreController::class);

    // Category Management
    Route::resource('categories', AdminCategoryController::class);

    // Order Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
        Route::put('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
    });

    // User Management
    Route::resource('users', AdminUserController::class)->only(['index', 'show', 'edit', 'update']);

    // Reports & Analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [AdminReportController::class, 'sales'])->name('sales');
        Route::get('/products', [AdminReportController::class, 'products'])->name('products');
        Route::get('/users', [AdminReportController::class, 'users'])->name('users');
    });
});

/*
|--------------------------------------------------------------------------
| WEBHOOK ROUTES (No authentication required)
|--------------------------------------------------------------------------
*/

// Xendit webhook - must be accessible without authentication
Route::post('/webhook/xendit', [WebhookController::class, 'xenditWebhook'])
    ->name('webhook.xendit')
    ->withoutMiddleware(['auth', 'verified'])
    ->middleware('throttle:1000,1');

/*
|--------------------------------------------------------------------------
| API ROUTES (For AJAX calls)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->name('api.')->group(function () {
    // Public API (no auth required)
    Route::get('/products/featured', [HomeController::class, 'getFeaturedProductsApi']);
    Route::get('/categories/popular', [HomeController::class, 'getPopularCategoriesApi']);
    Route::get('/search/products', [UserProductController::class, 'search']);

    // Protected API (auth required)
    Route::middleware('auth')->group(function () {
        Route::get('/user/orders', [UserController::class, 'getOrdersApi']);
        Route::get('/user/profile', [UserController::class, 'getProfileApi']);
    });
});

/*
|--------------------------------------------------------------------------
| FALLBACK ROUTES
|--------------------------------------------------------------------------
*/

// Redirect old admin routes to new structure (temporary)
Route::redirect('/site/{any}', '/admin')->where('any', '.*');

// 404 handler for undefined routes
Route::fallback(function () {
    return view('youtube.com');
});
