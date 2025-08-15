<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductImageController;


/*
|--------------------------------------------------------------------------
| Authentication Routes (Your Custom)
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

    // User Frontend Pages
    Route::get('dashboard', function () {
        return view('frontend.pages.home');
    })->name('dashboard');

    Route::get('detail', function () {
        return view('frontend.pages.detail');
    })->name('detail');

    // Add more user frontend pages here
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
| Protected Resource Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Basic CRUD routes
    Route::resource('site/products', ProductController::class);
    Route::resource('site/stores', StoreController::class);
    Route::resource('site/categories', CategoryController::class);

    // Nested resource routes
    Route::resource('site/products.variants', ProductVariantController::class);
    Route::resource('site/products.images', ProductImageController::class);

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
| Breeze Routes (Keep for password reset, email verification, etc.)
|--------------------------------------------------------------------------
*/

// require __DIR__.'/auth.php';
