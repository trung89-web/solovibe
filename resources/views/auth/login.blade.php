@extends('frontend.layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 mt-5">
            <div class="card-body p-4">
                <h3 class="text-center text-success mb-4 fw-bold">ĐĂNG NHẬP</h3>
                
                <!-- Hiển thị thông báo trạng thái -->
                <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-3 form-check">
                        <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                        <label for="remember_me" class="form-check-label">Ghi nhớ đăng nhập</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-0">
                        @if (Route::has('password.request'))
                            <a class="text-decoration-none text-muted" href="{{ route('password.request') }}">
                                Quên mật khẩu?
                            </a>
                        @endif

                        <button type="submit" class="btn btn-success fw-bold px-4">
                            Đăng nhập
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted mb-0">Chưa có tài khoản? <a href="{{ route('register') }}" class="text-success text-decoration-none fw-bold">Đăng ký ngay</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection