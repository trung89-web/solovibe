<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Ép cứng redirect cho Admin (Bỏ intended)
        if ($request->user()->hasAnyRole(['Super Admin', 'Content Staff'])) {
            return redirect()->route('products.index'); 
        }

        // Khách hàng thì dùng intended để trả về trang họ đang định vào
        return redirect()->intended(route('frontend.products.index'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Đẩy thẳng về trang chủ khách hàng sau khi đăng xuất
        return redirect()->route('frontend.products.index');
    }
}