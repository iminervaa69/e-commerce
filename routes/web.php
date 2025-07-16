<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductVariantController;


Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard');
})->name('dashboard');

Route::resource('products', ProductController::class);
Route::resource('stores', StoreController::class);
Route::resource('categories', CategoryController::class);
Route::resource('products.variants', ProductVariantController::class);
