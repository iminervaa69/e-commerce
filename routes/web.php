<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\User\HomeController; 
use App\Http\Controllers\User\ProductController as UserProductController;

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

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Routes for guests only (not authenticated)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

// Routes for authenticated users only
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // User Dashboard
    Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');
    
    // User-specific product actions (require auth)
    Route::post('/product/{slug}/review', [UserProductController::class, 'storeReview'])->name('product.store-review');
    
    // User pages
    Route::get('detail', function () {
        return view('frontend.pages.detail');
    })->name('detail');

    Route::get('profile', function () {
        return view('frontend.pages.profile');
    })->name('profile');

    Route::get('orders', function () {
        return view('frontend.pages.orders');
    })->name('orders');

    Route::get('cart', function () {
        return view('frontend.pages.cart');
    })->name('cart');
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
| Breeze Routes (Keep for password reset, email verification, etc.)
|--------------------------------------------------------------------------
*/

// require __DIR__.'/auth.php';