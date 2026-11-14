<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

// Halaman publik
Route::get('/', [ShopController::class, 'home'])->name('home');
Route::get('/produk', [ShopController::class, 'index'])->name('shop.index');
Route::get('/produk/{product:slug}', [ShopController::class, 'show'])->name('shop.show');

// Webhook Midtrans (dipanggil server Midtrans, tanpa login)
Route::post('/midtrans/notification', [MidtransWebhookController::class, 'handle'])
    ->name('midtrans.notification');

// Setelah login: admin ke panel admin, pelanggan ke beranda toko
Route::get('/dashboard', function () {
    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('home');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Keranjang belanja
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/keranjang/item/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/item/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Riwayat pesanan
    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Pembayaran
    Route::post('/pesanan/{order}/bayar', [PaymentController::class, 'token'])->name('payment.token');
    Route::post('/pesanan/{order}/cek-status', [PaymentController::class, 'check'])->name('payment.check');
});

// Area Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('products', ProductController::class)->except('show');
});

require __DIR__.'/auth.php';