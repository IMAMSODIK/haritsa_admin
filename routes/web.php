<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('api.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); 

    Route::get('/banner', [BannerController::class, 'index'])->name('banner'); 
    Route::post('/banner/store', [BannerController::class, 'store'])->name('banner.store');
    Route::delete('/banner/{id}', [BannerController::class, 'destroy'])->name('banner.delete');

    Route::get('/stores', [StoreController::class, 'index'])->name('store.index');
    Route::post('/stores', [StoreController::class, 'store'])->name('store.store');
    Route::patch('/stores/{id}', [StoreController::class, 'update']);
    Route::get('/stores/{id}', [StoreController::class, 'show']);
    Route::delete('/stores/{id}', [StoreController::class, 'destroy']);

    Route::get('/products', [ProdukController::class, 'index'])->name('products.index');
    Route::post('/products', [ProdukController::class, 'store'])->name('products.store');
    Route::get('/products/stores', [ProdukController::class, 'getStore'])->name('products.getStore');
    Route::patch('/products/{id}', [ProdukController::class, 'update']);
    Route::get('/products/{id}', [ProdukController::class, 'show']);
    Route::delete('/products/{id}', [ProdukController::class, 'destroy']);

    Route::get('/promo', [PromoController::class, 'index'])->name('promo.index');
    Route::post('/promo', [PromoController::class, 'store'])->name('promo.store');
    Route::patch('/promo/{id}', [PromoController::class, 'update']);
    Route::get('/promo/{id}', [PromoController::class, 'show']);
    Route::delete('/promo/{id}', [PromoController::class, 'destroy']);

    Route::get('/profile', [UserController::class, 'profile']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware('api.auth')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginCheck'])->name('login.process');
});
