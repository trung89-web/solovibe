@props(['title', 'products'])

@if($products->isNotEmpty())
<div class="mb-5">
    <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom border-2 border-dark">
        <h4 class="text-dark fw-bold mb-0 text-uppercase d-flex align-items-center">
            <span class="bg-danger text-white rounded p-1 px-2 me-2 fs-6">
                <i class="bi bi-fire"></i>
            </span>
            {{ $title }}
        </h4>
        <a href="{{ route('frontend.products.index') }}" class="text-danger text-decoration-none small fw-bold">
            Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4">
        @foreach($products as $product)
            <div class="col">
                <x-product-card :product="$product" />
            </div>
        @endforeach
    </div>
</div>
@endif