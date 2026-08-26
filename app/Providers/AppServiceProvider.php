<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Thêm dòng này
use App\Services\CartService; // Thêm dòng này

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Chia sẻ biến $cartTotalQuantity cho layout frontend
        View::composer('frontend.layouts.app', function ($view) {
            $cartService = app(CartService::class);
            $view->with('cartTotalQuantity', $cartService->getTotalQuantity());
        });
    }
}
