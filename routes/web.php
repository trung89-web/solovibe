<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Frontend\HomeController;

use App\Http\Controllers\Admin\AttributeController;
// ==========================================
// 1. PHẦN FRONTEND (KHÁCH HÀNG)
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/san-pham', [\App\Http\Controllers\Frontend\ProductController::class, 'index'])->name('frontend.products.index');

Route::get('/san-pham', [\App\Http\Controllers\Frontend\ProductController::class, 'index'])->name('frontend.products.index');
Route::get('/san-pham/{product:slug}', [\App\Http\Controllers\Frontend\ProductController::class, 'show'])->name('frontend.products.show');


// ==========================================
// 2. PHẦN ADMIN (YÊU CẦU ĐĂNG NHẬP)
// ==========================================
Route::prefix('admin')->middleware(['auth', 'role:Super Admin|Content Staff'])->group(function () {
    
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->except(['show']);
    Route::delete('products/images/{productImage}', [\App\Http\Controllers\Admin\ProductController::class, 'destroyImage'])->name('products.images.destroy');
});


// ==========================================
// 3. FILE AUTH CỦA BREEZE (Tự động sinh ra)
// ==========================================
require __DIR__.'/auth.php';

// ==========================================
// THƯƠNG MẠI ĐIỆN TỬ - GIỎ HÀNG
// ==========================================
use App\Http\Controllers\Frontend\CartController;

Route::prefix('gio-hang')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{productId}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{productId}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::post('/remove-multiple', [CartController::class, 'removeMultiple'])->name('removeMultiple');
});

Route::resource('attributes', AttributeController::class)->except(['create', 'show', 'edit', 'update']);