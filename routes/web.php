<?php

use Illuminate\Support\Facades\Route;

// ==========================================
// 1. PHẦN FRONTEND (KHÁCH HÀNG)
// ==========================================
Route::get('/', function () {
    return redirect()->route('frontend.products.index');
});

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