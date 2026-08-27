@extends('frontend.layouts.app')

@section('content')
<div class="container py-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 border-top border-success border-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock text-success" style="font-size: 3rem;"></i>
                        <h4 class="fw-bold mt-2">Nhập mã xác thực</h4>
                        <p class="text-muted small">
                            Chúng tôi đã gửi mã OTP 6 số đến email <strong>{{ session('register_data')['email'] ?? '' }}</strong>.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('register.verify') }}">
                        @csrf
                        <div class="mb-4">
                            <input type="text" name="otp" class="form-control form-control-lg text-center fw-bold text-success @error('otp') is-invalid @enderror" 
                                   placeholder="--- ---" maxlength="6" style="letter-spacing: 5px; font-size: 1.5rem;" required autofocus>
                            @error('otp') 
                                <div class="invalid-feedback text-center fw-bold">{{ $message }}</div> 
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm">
                            XÁC NHẬN VÀ TẠO TÀI KHOẢN
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('register') }}" class="text-muted text-decoration-none small">
                            <i class="bi bi-arrow-left"></i> Quay lại trang đăng ký
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection