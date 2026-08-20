@extends('frontend.layouts.app')

@section('content')
    <!-- 1. HERO BANNER (Hardcode ảnh tạm thời) -->
    <div id="heroCarousel" class="carousel slide mb-5 shadow-sm rounded overflow-hidden" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('images/banners/banner-1.jpg') }}" class="d-block w-100" alt="Banner 1" style="object-fit: cover; height: 400px;">
            </div>
            
            <div class="carousel-item">
                <img src="{{ asset('images/banners/banner-2.jpg') }}" class="d-block w-100" alt="Banner 2" style="object-fit: cover; height: 400px;">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>

    <!-- 2. USP / CAM KẾT DỊCH VỤ -->
    <div class="row text-center mb-5 g-3">
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white shadow-sm rounded h-100 border-top border-success border-4">
                <i class="bi bi-truck fs-2 text-success mb-2"></i>
                <h6 class="fw-bold mb-1">Giao hàng toàn quốc</h6>
                <p class="text-muted small mb-0">Đóng gói cẩn thận, an toàn</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white shadow-sm rounded h-100 border-top border-success border-4">
                <i class="bi bi-shield-check fs-2 text-success mb-2"></i>
                <h6 class="fw-bold mb-1">Cam kết chuẩn giống</h6>
                <p class="text-muted small mb-0">Bảo hành đúng giống 100%</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white shadow-sm rounded h-100 border-top border-success border-4">
                <i class="bi bi-headset fs-2 text-success mb-2"></i>
                <h6 class="fw-bold mb-1">Hỗ trợ kỹ thuật</h6>
                <p class="text-muted small mb-0">Tư vấn chăm sóc 24/7</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white shadow-sm rounded h-100 border-top border-success border-4">
                <i class="bi bi-arrow-repeat fs-2 text-success mb-2"></i>
                <h6 class="fw-bold mb-1">Đổi trả dễ dàng</h6>
                <p class="text-muted small mb-0">Nếu cây hỏng do vận chuyển</p>
            </div>
        </div>
    </div>

    <!-- 3. DANH MỤC NỔI BẬT -->
    @if($featuredCategories->isNotEmpty())
    <div class="mb-5">
        <h3 class="mb-4 text-success fw-bold text-center">DANH MỤC NỔI BẬT</h3>
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3 justify-content-center">
            @foreach($featuredCategories as $category)
            <div class="col">
                <a href="{{ route('frontend.products.index', ['category_id[]' => $category->id]) }}" class="text-decoration-none">
                    <div class="card h-100 text-center border-0 shadow-sm bg-light">
                        <div class="card-body p-3">
                            <!-- Đang dùng icon tạm, nếu có ảnh category thì thay thẻ img vào đây -->
                            <div class="bg-white rounded-circle d-inline-block p-3 mb-2 shadow-sm">
                                <i class="bi bi-tree text-success fs-3"></i>
                            </div>
                            <h6 class="card-title text-dark fw-bold mb-0 small">{{ $category->name }}</h6>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 4, 5, 6. CÁC SECTION SẢN PHẨM (Sử dụng Component) -->
    <x-home.product-section title="SẢN PHẨM NỔI BẬT" :products="$featuredProducts" />
    
    <x-home.product-section title="HÀNG MỚI VỀ" :products="$latestProducts" />
    
    <x-home.product-section title="ĐANG GIẢM GIÁ" :products="$discountProducts" />

@endsection