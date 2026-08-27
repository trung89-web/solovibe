<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Thêm dòng này
use App\Services\CartService; // Thêm dòng này
use Illuminate\Auth\Notifications\VerifyEmail; // Thêm
use Illuminate\Notifications\Messages\MailMessage; // Thêm

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

        // Việt hóa Email xác thực
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('🌱 Xác thực địa chỉ Email - Fruit Tree')
                ->greeting('Xin chào ' . $notifiable->name . ',')
                ->line('Cảm ơn bạn đã đăng ký tài khoản tại Fruit Tree - Vườn ươm cây giống chất lượng.')
                ->line('Vui lòng bấm vào nút bên dưới để xác thực địa chỉ email của bạn.')
                ->action('Xác thực Email', $url)
                ->line('Liên kết xác thực này sẽ hết hạn sau 60 phút.')
                ->line('Nếu bạn không tạo tài khoản này, vui lòng bỏ qua thư này.')
                ->salutation('Trân trọng, Đội ngũ Fruit Tree');
        });
    }
}
