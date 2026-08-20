@extends('frontend.layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 mt-5">
            <div class="card-body p-4">
                <h4 class="text-center text-success mb-3 fw-bold">QUÊN MẬT KHẨU</h4>
                <p class="text-muted text-center mb-4">
                    Nhập email của bạn và chúng tôi sẽ gửi cho bạn một liên kết để đặt lại mật khẩu.
                </p>

                <!-- Báo lỗi hoặc thành công -->
                <x-auth-session-status class="alert alert-success" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-bold">Gửi liên kết</button>
                </form>
                
                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="text-success text-decoration-none">Quay lại đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection