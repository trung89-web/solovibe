<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\MomoController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ReportController;
// Sửa dòng use ở đầu file routes/web.php:
use App\Http\Controllers\User\ChatController as UserChatController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
// ==========================================
// 1. PHẦN FRONTEND (KHÁCH HÀNG)
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/san-pham', [ProductController::class, 'index'])->name('frontend.products.index');
Route::get('/san-pham/{product:slug}', [ProductController::class, 'show'])->name('frontend.products.show');

// ==========================================
// 2. PHẦN ADMIN (YÊU CẦU ĐĂNG NHẬP)
// ==========================================
Route::prefix('admin')->middleware(['auth', 'role:Super Admin|Content Staff'])->group(function () {
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->except(['show']);
    Route::delete('products/images/{productImage}', [\App\Http\Controllers\Admin\ProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update']);
    Route::get('users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::patch('users/{user}/toggle-lock', [AdminUserController::class, 'toggleLock'])->name('admin.users.toggleLock');
    Route::get('reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('reports/transactions', [ReportController::class, 'transactions'])->name('admin.reports.transactions');
    Route::put('reports/orders/{order}/cod-payment', [ReportController::class, 'updateCodPayment'])->name('admin.reports.cod.update');
});

// ==========================================
// 3. FILE AUTH CỦA BREEZE
// ==========================================
require __DIR__.'/auth.php';

// ==========================================
// 4. GIỎ HÀNG
// ==========================================
Route::prefix('gio-hang')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::post('/buy-now/{product}', [CartController::class, 'buyNow'])->name('buyNow');
    Route::patch('/update/{productId}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{productId}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::post('/remove-multiple', [CartController::class, 'removeMultiple'])->name('removeMultiple');
});

// ==========================================
// 5. CHECKOUT ROUTES
// ==========================================
Route::middleware(['auth'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::match(['get', 'post'], '/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{orderCode?}', [CheckoutController::class, 'success'])->name('success');
    Route::post('/calculate-shipping', [CheckoutController::class, 'calculateShippingFee'])->name('calculateShipping');
});

Route::resource('attributes', AttributeController::class)->except(['create', 'show', 'edit', 'update']);

// ==========================================
// 6. PROFILE ROUTES
// ==========================================
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::post('/address', [ProfileController::class, 'storeAddress'])->name('address.store');
    Route::put('/address/{id}', [ProfileController::class, 'updateAddress'])->name('address.update');
    Route::delete('/address/{id}', [ProfileController::class, 'destroyAddress'])->name('address.destroy');
    Route::put('/address/{id}/default', [ProfileController::class, 'setDefaultAddress'])->name('address.default');
    Route::put('/orders/{order}/cancel', [ProfileController::class, 'cancelOrder'])->name('orders.cancel');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// ==========================================
// 7. THANH TOÁN MOMO
// ==========================================
// IPN nhận phản hồi ngầm từ server MoMo (ĐẶT NGOÀI middleware auth)
Route::post('/payment/momo/ipn', [MomoController::class, 'ipn'])->name('payment.momo.ipn');

Route::middleware(['auth'])->group(function () {
    // Route tạo link thanh toán
    Route::get('/payment/momo/{order_id}', [MomoController::class, 'startPayment'])->name('payment.momo');
    
    // Route nhận phản hồi khi khách hàng hoàn tất thanh toán trên trình duyệt
    Route::get('/payment/momo/callback', [MomoController::class, 'callback'])->name('payment.momo.callback');
});

// Đặt NGOÀI middleware auth để tránh bị chặn khi MoMo redirect về
Route::get('/payment/momo/callback', [MomoController::class, 'callback'])->name('payment.momo.callback');
Route::post('/payment/momo/ipn', [MomoController::class, 'ipn'])->name('payment.momo.ipn');

Route::middleware(['auth'])->group(function () {
    Route::get('/payment/momo/{order_id}', [MomoController::class, 'startPayment'])->name('payment.momo');
});
// ROUTE CHO USER
Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
    Route::post('/chat/send', [UserChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/messages', [UserChatController::class, 'getMessages'])->name('chat.messages');
});

// ROUTE CHO ADMIN
Route::middleware(['auth', 'role:Super Admin|Content Staff'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/chat/users', [AdminChatController::class, 'getUsers'])->name('chat.users');
    Route::get('/chat/messages/{userId}', [AdminChatController::class, 'getMessages'])->name('chat.messages');
    Route::get('/chat/history/{user}', [AdminChatController::class, 'showHistory'])->name('chat.history');
    Route::post('/chat/send', [AdminChatController::class, 'send'])->name('chat.send');
    Route::get('/reports/export', [ReportController::class, 'exportExcel'])->name('reports.export');
});