@extends('frontend.layouts.app')

@section('title', 'Bộ Sưu Tập Giày Sneaker & Thể Thao - SoleVibe')

@section('content')
<div class="row">
    <!-- Sidebar -->
    <div class="col-lg-3 mb-4">
        @include('components.filter-sidebar')
    </div>

    <!-- Product Grid -->
    <div class="col-lg-9">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h4 class="mb-1 fw-bold text-dark text-uppercase">
                    <i class="bi bi-collection-fill text-danger me-2"></i>BỘ SƯU TẬP GIÀY
                </h4>
                <p class="text-muted small mb-0">Tìm thấy <strong>{{ $products->total() }}</strong> sản phẩm phù hợp</p>
            </div>
            
            @if(request('keyword') || request('category_id') || request('size') || request('min_price'))
                <div>
                    <a href="{{ route('frontend.products.index') }}" class="btn btn-sm btn-outline-danger rounded-pill">
                        <i class="bi bi-x-circle me-1"></i>Xóa tất cả bộ lọc
                    </a>
                </div>
            @endif
        </div>

        @if($products->isEmpty())
            <div class="text-center py-5 bg-white shadow-sm rounded-4">
                <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 fw-bold text-dark">Không tìm thấy sản phẩm nào!</h5>
                <p class="text-muted">Vui lòng thử lại với từ khóa khác hoặc xóa bớt các tiêu chí lọc.</p>
                <a href="{{ route('frontend.products.index') }}" class="btn btn-danger rounded-pill px-4">
                    Xem tất cả sản phẩm
                </a>
            </div>
        @else
            <div class="row row-cols-2 row-cols-md-3 g-3 g-md-4 mb-4">
                @foreach($products as $product)
                    <div class="col">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>

            <!-- Phân trang -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection