<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterOtpMail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    // 1. Hàm store cũ: Thay vì tạo User luôn, ta lưu tạm vào Session và Gửi OTP
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        // Tạo mã OTP 6 số
        $otp = rand(100000, 999999);

        // Lưu thông tin đăng ký và OTP vào Session (Tồn tại 10 phút)
        Session::put('register_data', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10)
        ]);

        // Gửi Mail chứa mã OTP
        Mail::to($request->email)->send(new RegisterOtpMail($otp));

        return redirect()->route('register.otp');
    }

    // 2. Hàm mới: Hiển thị form nhập OTP
    public function showOtpForm()
    {
        if (!Session::has('register_data')) {
            return redirect()->route('register');
        }
        return view('auth.verify-otp-register');
    }

    // 3. Hàm mới: Kiểm tra OTP và Tạo tài khoản
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6'
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP',
            'otp.digits' => 'Mã OTP phải bao gồm 6 chữ số'
        ]);

        $data = Session::get('register_data');

        // Check hết hạn
        if (!$data || now()->greaterThan($data['expires_at'])) {
            Session::forget('register_data');
            return redirect()->route('register')->withErrors(['email' => 'Mã OTP đã hết hạn, vui lòng đăng ký lại.']);
        }

        // Check OTP sai
        if ($request->otp != $data['otp']) {
            return back()->withErrors(['otp' => 'Mã OTP không chính xác. Vui lòng thử lại.']);
        }

        // OTP đúng -> TẠO TÀI KHOẢN VÀ XÁC THỰC NGAY LẬP TỨC
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'email_verified_at' => now(), // Xác thực ngay lập tức
        ]);

        // Xóa Session rác
        Session::forget('register_data');

        // Tự động đăng nhập
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Đăng ký và xác thực tài khoản thành công!');
    }
}
