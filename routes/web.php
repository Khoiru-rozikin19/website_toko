<?php

use App\Http\Controllers\Admin\OkeconnectController as AdminOkeconnectController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ServerController as AdminServerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\PesananController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

// Autentikasi (Login, Register, Logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Menu Belanja Publik
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');
Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan');
Route::get('/pesanan/{ref}', [PesananController::class, 'show'])->name('pesanan.show');
Route::get('/checkout/{ref}/status', [CheckoutController::class, 'checkStatus'])->name('checkout.status');

// Pembuatan Pesanan / Checkout (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
});

// Menu Panel Admin (Hanya Role Admin)
Route::prefix('admin')->name('admin.')->middleware(['auth', EnsureUserIsAdmin::class])->group(function () {
    // Kelola Produk CRUD
    Route::get('/produk', [AdminProductController::class, 'index'])->name('produk');
    Route::post('/produk', [AdminProductController::class, 'store'])->name('produk.store');
    Route::put('/produk/{product}', [AdminProductController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{product}', [AdminProductController::class, 'destroy'])->name('produk.destroy');
    Route::post('/produk/{product}/toggle', [AdminProductController::class, 'toggleStatus'])->name('produk.toggle');

    // Integrasi OkeConnect & Server VPS
    Route::get('/okeconnect', [AdminOkeconnectController::class, 'index'])->name('okeconnect');
    Route::get('/server', [AdminServerController::class, 'index'])->name('server');
});
