<?php

use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PodcastController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\PromoCustomerController;
use App\Http\Controllers\PromoFlashController;
use App\Http\Controllers\PromoVideoController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SurveyLayananController;
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

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/load', [UserController::class, 'loadUser']);
    Route::get('/users/roles', [UserController::class, 'roles']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users/{roleId}', [UserController::class, 'store']);
    Route::patch('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::get('/stores', [StoreController::class, 'index'])->name('store.index');
    Route::post('/stores', [StoreController::class, 'store'])->name('store.store');
    Route::patch('/stores/{id}', [StoreController::class, 'update']);
    Route::get('/stores/{id}', [StoreController::class, 'show']);
    Route::delete('/stores/{id}', [StoreController::class, 'destroy']);

    Route::get('/brand-produk', [BrandController::class, 'index'])->name('brand.index');
    Route::post('/brand-produk', [BrandController::class, 'store'])->name('brand.store');
    Route::patch('/brand-produk/{id}', [BrandController::class, 'update']);
    Route::get('/brand-produk/{id}', [BrandController::class, 'show']);
    Route::delete('/brand-produk/{id}', [BrandController::class, 'destroy']);

    Route::get('/kategori-produk', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori-produk', [KategoriController::class, 'store'])->name('kategori.store');
    Route::patch('/kategori-produk/{id}', [KategoriController::class, 'update']);
    Route::get('/kategori-produk/{id}', [KategoriController::class, 'show']);
    Route::delete('/kategori-produk/{id}', [KategoriController::class, 'destroy']);

    Route::get('/products', [ProdukController::class, 'index'])->name('products.index');
    Route::post('/products', [ProdukController::class, 'store'])->name('products.store');
    Route::get('/products/stores', [ProdukController::class, 'getStore'])->name('products.getStore');
    Route::patch('/products/{id}', [ProdukController::class, 'update']);
    Route::get('/products/{id}', [ProdukController::class, 'show']);
    Route::delete('/products/{id}', [ProdukController::class, 'destroy']);

    Route::get('/promo-reguler', [PromoController::class, 'index'])->name('promo.index');
    Route::post('/promo-reguler', [PromoController::class, 'store'])->name('promo.store');
    Route::patch('/promo-reguler/{id}', [PromoController::class, 'update']);
    Route::get('/promo-reguler/{id}', [PromoController::class, 'show']);
    Route::delete('/promo-reguler/{id}', [PromoController::class, 'destroy']);

    Route::get('/promo-customer', [PromoCustomerController::class, 'index'])->name('promo.customer.index');
    Route::post('/promo-customer', [PromoCustomerController::class, 'store'])->name('promo.customer.store');
    Route::patch('/promo-customer/{id}', [PromoCustomerController::class, 'update']);
    Route::get('/promo-customer/{id}', [PromoCustomerController::class, 'show']);
    Route::delete('/promo-customer/{id}', [PromoCustomerController::class, 'destroy']);

    Route::get('/promo-flash', [PromoFlashController::class, 'index'])->name('promo.flash.index');
    Route::post('/promo-flash', [PromoFlashController::class, 'store'])->name('promo.flash.store');
    Route::patch('/promo-flash/{id}', [PromoFlashController::class, 'update']);
    Route::get('/promo-flash/{id}', [PromoFlashController::class, 'show']);
    Route::delete('/promo-flash/{id}', [PromoFlashController::class, 'destroy']);

    Route::get('/promo-video', [PromoVideoController::class, 'index'])->name('promo.video.index');
    Route::post('/promo-video', [PromoVideoController::class, 'store'])->name('promo.video.store');
    Route::patch('/promo-video/{id}', [PromoVideoController::class, 'update']);
    Route::get('/promo-video/{id}', [PromoVideoController::class, 'show']);
    Route::delete('/promo-video/{id}', [PromoVideoController::class, 'destroy']);

    // PARENTING
    Route::get('/podcast', [PodcastController::class, 'index'])->name('podcast.index');
    Route::post('/podcast', [PodcastController::class, 'store'])->name('podcast.store');
    Route::patch('/podcast/{id}', [PodcastController::class, 'update']);
    Route::get('/podcast/{id}', [PodcastController::class, 'show']);
    Route::delete('/podcast/{id}', [PodcastController::class, 'destroy']);

    Route::get('/artikel-parenting', [ArtikelController::class, 'index'])->name('artikel.index');
    Route::post('/artikel-parenting', [ArtikelController::class, 'store'])->name('artikel.store');
    Route::patch('/artikel-parenting/{id}', [ArtikelController::class, 'update']);
    Route::get('/artikel-parenting/{id}', [ArtikelController::class, 'show']);
    Route::delete('/artikel-parenting/{id}', [ArtikelController::class, 'destroy']);

    Route::get('/survey-layanan', [SurveyLayananController::class, 'index'])->name('survey.index');
    Route::post('/survey-layanan', [SurveyLayananController::class, 'store'])->name('survey.store');
    Route::patch('/survey-layanan/{id}', [SurveyLayananController::class, 'update']);
    Route::get('/survey-layanan/{id}', [SurveyLayananController::class, 'show']);
    Route::delete('/survey-layanan/{id}', [SurveyLayananController::class, 'destroy']);

    Route::get('/profile', [UserController::class, 'profile']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/artikel-parenting/{id}/preview', [ArtikelController::class, 'preview']);

Route::middleware('api.auth')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginCheck'])->name('login.process');
});
