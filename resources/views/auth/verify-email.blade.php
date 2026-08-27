@extends('frontend.layouts.app')

@section('content')
<div class="container py-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 border-top border-success border-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-envelope-check text-success" style="font-size: 3rem;"></i>
                        <h4 class="fw-bold mt-2">Xác thực địa chỉ Email</h4>
                    </div>

                    <p class="text-muted text-center mb-4">
                        Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, vui lòng xác thực địa chỉ email bằng cách bấm vào đường link chúng tôi vừa gửi qua email của bạn. 
                        <br>Nếu bạn chưa nhận được, hãy bấm nút bên dưới để chúng tôi gửi lại.
                    </p>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success text-center fw-bold shadow-sm" role="alert">
                            <i class="bi bi-check-circle me-1"></i> Một đường link xác thực mới đã được gửi đến email của bạn!
                        </div>
                    @endif

                    <div class="d-flex flex-column gap-3 mt-4">
                        <!-- Form gửi lại email -->
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm">
                                <i class="bi bi-send me-1"></i> Gửi lại thư xác thực
                            </button>
                        </form>

                        <!-- Form Đăng xuất -->
                        <form method="POST" action="{{ route('logout') }}" class="text-center">
                            @csrf
                            <button type="submit" class="btn btn-link text-muted text-decoration-none small">
                                Đăng xuất tài khoản
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection