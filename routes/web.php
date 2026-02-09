<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\DashboardController;
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

    Route::get('/profile', [UserController::class, 'profile']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginCheck'])->name('login.process');
