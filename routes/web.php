<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\checkForPrice;
use App\Http\Middleware\CheckForAuth;
// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/about', [App\Http\Controllers\HomeController::class, 'about'])->name('about');
Route::get('/contact', [App\Http\Controllers\HomeController::class, 'contact'])->name('contact');

Route::aliasMiddleware('checkForPrice', checkForPrice::class);
Route::aliasMiddleware('CheckForAuth', CheckForAuth::class);

Route::group(['prefix'=>'products'],function(){
    //products
    Route::get('/category/{id}', [App\Http\Controllers\Products\ProductsController::class, 'singleCategory'])->name('single.category');
    Route::get('/single-product/{id}', [App\Http\Controllers\Products\ProductsController::class, 'singleProduct'])->name('single.product');

    Route::get('/shop', [App\Http\Controllers\Products\ProductsController::class, 'shop'])->name('products.shop');
    //cart
    Route::post('/add-cart', [App\Http\Controllers\Products\ProductsController::class, 'addToCart'])->name('products.add.cart');
    Route::get('/cart', [App\Http\Controllers\Products\ProductsController::class, 'cart'])->name('products.cart')->middleware('auth:web');
    Route::get('/delete-cart/{id}', [App\Http\Controllers\Products\ProductsController::class, 'deleteFromCart'])->name('products.cart.delete');

    //checkout
    Route::post('/prepare-checkout', [App\Http\Controllers\Products\ProductsController::class, 'prepareCheckout'])->name('products.prepare.checkout')->middleware('checkForPrice');
    Route::get('/checkout', [App\Http\Controllers\Products\ProductsController::class, 'checkout'])->name('products.checkout')->middleware('checkForPrice');
    Route::post('/checkout', [App\Http\Controllers\Products\ProductsController::class, 'proccessCheckout'])->name('products.proccess.checkout');


    //Checkout and paying
    Route::get('/pay', [App\Http\Controllers\Products\ProductsController::class, 'payWithPaypal'])->name('products.pay')->middleware('checkForPrice');
    Route::get('/success', [App\Http\Controllers\Products\ProductsController::class, 'success'])->name('products.success')->middleware('checkForPrice');;

});
Route::group(['prefix'=>'users'],function(){
//users pages
    Route::get('/my-orders', [App\Http\Controllers\Users\UsersController::class, 'myOrders'])->name('users.orders')->middleware('auth:web');
    Route::get('/settings', [App\Http\Controllers\Users\UsersController::class, 'settings'])->name('users.settings')->middleware('auth:web');
    Route::post('/settings/{id}', [App\Http\Controllers\Users\UsersController::class, 'updateUserSettings'])->name('users.settings.update')->middleware('auth:web');
});

//admin
Route::get('admin/login', [App\Http\Controllers\Admins\AdminsController::class, 'viewLogin'])->name('view.login')->middleware('CheckForAuth');
Route::post('admin/login', [App\Http\Controllers\Admins\AdminsController::class, 'checkLogin'])->name('check.login');

Route::post('admin/logout', [App\Http\Controllers\Admins\AdminsController::class, 'logout'])->name('admin.logout')->middleware('auth:admin');


Route::group(['prefix'=>'admin','middleware'=>'auth:admin'],function(){
    Route::get('/index', [App\Http\Controllers\Admins\AdminsController::class, 'index'])->name('admins.dashboard');
});
