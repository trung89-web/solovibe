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

        // Admin
        if ($request->user()->hasAnyRole(['Super Admin', 'Content Staff'])) {
            return redirect()->route('products.index')->with('success', 'Đăng nhập quyền Admin thành công!'); 
        }

        // Khách hàng
        return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Kiểm tra xem request có gửi từ Admin Panel không
        $redirectUrl = $request->has('from_admin') ? route('login') : route('frontend.products.index');

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Chuyển hướng theo URL đã xác định
        return redirect()->route('login');
    }
}