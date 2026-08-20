@props(['title', 'products'])

<!-- Task 6: Kiểm tra Edge case - Ẩn cả section nếu không có sản phẩm nào -->
@if($products->isNotEmpty())
<div class="mb-5">
    <div class="d-flex justify-content-between align-items-end border-bottom border-success border-opacity-50 pb-2 mb-4">
        <h3 class="text-success fw-bold mb-0">
            <i class="bi bi-star-fill text-warning me-2"></i>{{ $title }}
        </h3>
        <a href="{{ route('frontend.products.index') }}" class="text-success text-decoration-none small fw-bold">Xem tất cả <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
        @foreach($products as $product)
            <div class="col">
                <!-- Task 3: Tái sử dụng lại component thẻ sản phẩm đã làm ở trang danh sách -->
                <x-product-card :product="$product" />
            </div>
        @endforeach
    </div>
</div>
@endif