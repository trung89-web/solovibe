@extends('frontend.layouts.app')

@section('title', 'SoleVibe - Thế Giới Giày Sneaker & Thời Trang Thể Thao Chính Hãng')

@section('content')
    <!-- 1. HERO BANNER SLIDER -->
    <div id="heroCarousel" class="carousel slide mb-5 shadow rounded-4 overflow-hidden" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        </div>
        <div class="carousel-inner">
            <!-- Slide 1: Sneaker Streetwear -->
            <div class="carousel-item active position-relative" style="background: linear-gradient(120deg, #0f172a 0%, #1e293b 50%, #334155 100%); min-height: 420px;">
                <div class="container h-100 py-5">
                    <div class="row align-items-center h-100 gy-4">
                        <div class="col-lg-6 text-white ps-lg-4">
                            <span class="badge bg-danger text-uppercase fw-bold px-3 py-2 rounded-pill mb-3">
                                <i class="bi bi-fire me-1"></i> New Season 2026
                            </span>
                            <h1 class="display-4 fw-extrabold mb-3" style="letter-spacing: -1px;">
                                BỨT PHÁ <span class="text-danger">PHONG CÁCH</span> MỖI BƯỚC CHÂN
                            </h1>
                            <p class="lead text-secondary text-light opacity-75 mb-4">
                                Khám phá bộ sưu tập Sneaker & Giày thể thao chính hãng mới nhất. Đệm êm ái, thiết kế thời thượng tôn trọn cá tính của bạn.
                            </p>
                            <div class="d-flex flex-wrap gap-3">
                                <a href="{{ route('frontend.products.index') }}" class="btn btn-brand-primary btn-lg px-4 py-3 rounded-pill fw-bold shadow">
                                    <i class="bi bi-bag-check me-2"></i> MUA NGAY HÔM NAY
                                </a>
                                <a href="{{ route('frontend.products.index') }}" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill fw-bold">
                                    XEM BỘ SƯU TẬP
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center">
                            <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=900&auto=format&fit=crop&q=80" 
                                 class="img-fluid rounded-4 shadow-lg" 
                                 alt="Nike Air Jordan Hero" 
                                 style="max-height: 360px; object-fit: cover; transform: rotate(-5deg); transition: transform 0.4s ease;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2: Running Performance -->
            <div class="carousel-item position-relative" style="background: linear-gradient(120deg, #18181b 0%, #27272a 50%, #3f3f46 100%); min-height: 420px;">
                <div class="container h-100 py-5">
                    <div class="row align-items-center h-100 gy-4">
                        <div class="col-lg-6 text-white ps-lg-4">
                            <span class="badge bg-warning text-dark text-uppercase fw-bold px-3 py-2 rounded-pill mb-3">
                                <i class="bi bi-lightning-fill me-1"></i> Running Innovation
                            </span>
                            <h1 class="display-4 fw-extrabold mb-3" style="letter-spacing: -1px;">
                                CHẠY BỘ KHÔNG GIỚI HẠN CÙNG <span class="text-warning">LIGHT BOOST</span>
                            </h1>
                            <p class="lead text-light opacity-75 mb-4">
                                Trọng lượng siêu nhẹ, hoàn trả năng lượng tối đa trên từng dặm chạy. Giảm chấn thương, tăng hiệu suất vận động.
                            </p>
                            <div class="d-flex flex-wrap gap-3">
                                <a href="{{ route('frontend.products.index') }}" class="btn btn-warning btn-lg px-4 py-3 rounded-pill fw-bold text-dark shadow">
                                    <i class="bi bi-arrow-right-circle me-2"></i> KHÁM PHÁ DÒNG CHẠY
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center">
                            <img src="https://images.unsplash.com/photo-1575537302964-96cd47c06b1b?w=900&auto=format&fit=crop&q=80" 
                                 class="img-fluid rounded-4 shadow-lg" 
                                 alt="Running Shoe Hero" 
                                 style="max-height: 360px; object-fit: cover; transform: rotate(-4deg);">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon p-3 bg-dark bg-opacity-50 rounded-circle" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon p-3 bg-dark bg-opacity-50 rounded-circle" aria-hidden="true"></span>
        </button>
    </div>

    <!-- 2. USP / CAM KẾT DỊCH VỤ NGÀNH GIÀY -->
    <div class="row text-center mb-5 g-3">
        <div class="col-6 col-md-3">
            <div class="p-3 py-4 bg-white shadow-sm rounded-4 h-100 border-bottom border-danger border-4 transition-all">
                <div class="text-danger mb-2">
                    <i class="bi bi-shield-fill-check fs-1"></i>
                </div>
                <h6 class="fw-bold mb-1 text-dark">100% Chính Hãng</h6>
                <p class="text-muted small mb-0">Hoàn tiền 200% nếu phát hiện fake</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 py-4 bg-white shadow-sm rounded-4 h-100 border-bottom border-danger border-4 transition-all">
                <div class="text-danger mb-2">
                    <i class="bi bi-arrow-repeat fs-1"></i>
                </div>
                <h6 class="fw-bold mb-1 text-dark">Đổi Size 7 Ngày</h6>
                <p class="text-muted small mb-0">Hỗ trợ đổi size tận nhà nhanh chóng</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 py-4 bg-white shadow-sm rounded-4 h-100 border-bottom border-danger border-4 transition-all">
                <div class="text-danger mb-2">
                    <i class="bi bi-box-seam-fill fs-1"></i>
                </div>
                <h6 class="fw-bold mb-1 text-dark">Hộp Double-Box</h6>
                <p class="text-muted small mb-0">Bảo vệ hộp giày nguyên vẹn hoàn hảo</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 py-4 bg-white shadow-sm rounded-4 h-100 border-bottom border-danger border-4 transition-all">
                <div class="text-danger mb-2">
                    <i class="bi bi-wrench-adjustable-circle-fill fs-1"></i>
                </div>
                <h6 class="fw-bold mb-1 text-dark">Bảo Hành 12 Tháng</h6>
                <p class="text-muted small mb-0">Bảo hành keo chỉ & Spa giày định kỳ</p>
            </div>
        </div>
    </div>

    <!-- 3. DANH MỤC NỔI BẬT -->
    @if($featuredCategories->isNotEmpty())
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom border-2 border-dark">
            <h4 class="fw-bold mb-0 text-dark text-uppercase">
                <i class="bi bi-grid-fill text-danger me-2"></i>DANH MỤC BỘ SƯU TẬP
            </h4>
            <a href="{{ route('frontend.products.index') }}" class="text-danger text-decoration-none small fw-bold">
                Tất cả sản phẩm <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3 justify-content-center">
            @foreach($featuredCategories as $category)
            @php
                $catIcons = [
                    'Giày Sneaker & Thể Thao' => 'bi-lightning-charge-fill',
                    'Giày Chạy Bộ (Running)' => 'bi-speedometer',
                    'Giày Tây & Oxford Công Sở' => 'bi-briefcase-fill',
                    'Giày Lười (Loafer & Slip-on)' => 'bi-stars',
                    'Giày Cổ Cao & Boots' => 'bi-shield-shaded',
                    'Dép & Sandal Thời Trang' => 'bi-sun-fill',
                ];
                $iconClass = $catIcons[$category->name] ?? 'bi-bag-check-fill';
            @endphp
            <div class="col">
                <a href="{{ route('frontend.products.index', ['category_id[]' => $category->id]) }}" class="text-decoration-none group-card">
                    <div class="card h-100 text-center border-0 shadow-sm rounded-4 bg-white p-3 transition-all">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-2 mx-auto" style="width: 65px; height: 65px;">
                            <i class="bi {{ $iconClass }} text-danger fs-3"></i>
                        </div>
                        <h6 class="card-title text-dark fw-bold mb-1 small" style="line-height: 1.3;">{{ $category->name }}</h6>
                        <small class="text-muted" style="font-size: 0.72rem;">Xem ngay <i class="bi bi-chevron-right"></i></small>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 4. CÁC SECTION SẢN PHẨM -->
    <x-home.product-section title="SẢN PHẨM NỔI BẬT (TRENDING)" :products="$featuredProducts" />
    
    <!-- Promotion Banner Break -->
    <div class="my-5 p-4 p-md-5 rounded-4 text-white position-relative overflow-hidden shadow" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-danger mb-2 px-3 py-1">SPECIAL OFFER</span>
                <h3 class="fw-extrabold mb-2">GIẢM ĐẾN 30% CHO TẤT CẢ DÒNG RUNNING & SNEAKER</h3>
                <p class="text-light opacity-75 mb-0">Áp dụng cho thành viên mới và hóa đơn đặt trước trong tháng này. Tặng kèm bộ vệ sinh giày cao cấp!</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('frontend.products.index') }}" class="btn btn-danger btn-lg px-4 py-2 rounded-pill fw-bold shadow">
                    SĂN DEAL NGAY <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <x-home.product-section title="HÀNG MỚI VỀ (NEW ARRIVALS)" :products="$latestProducts" />
    
    <x-home.product-section title="ĐANG GIẢM GIÁ (HOT DEALS)" :products="$discountProducts" />

@endsection