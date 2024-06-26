<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('products/category/{id}', [App\Http\Controllers\Products\ProductsController::class, 'singleCategory'])->name('single.category');
Route::get('products/single-product/{id}', [App\Http\Controllers\Products\ProductsController::class, 'singleProduct'])->name('single.product');

Route::get('products/shop', [App\Http\Controllers\Products\ProductsController::class, 'shop'])->name('products.shop');
//cart
Route::post('products/add-cart', [App\Http\Controllers\Products\ProductsController::class, 'addToCart'])->name('products.add.cart');

Route::get('products/cart', [App\Http\Controllers\Products\ProductsController::class, 'cart'])->name('products.cart');

Route::get('products/delete-cart/{id}', [App\Http\Controllers\Products\ProductsController::class, 'deleteFromCart'])->name('products.cart.delete');

//checkout
Route::post('products/prepare-checkout', [App\Http\Controllers\Products\ProductsController::class, 'prepareCheckout'])->name('products.prepare.checkout');

Route::get('products/checkout', [App\Http\Controllers\Products\ProductsController::class, 'checkout'])->name('products.checkout');
