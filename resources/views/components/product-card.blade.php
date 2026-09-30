@php
    $thumbUrl = $product->thumbnail 
        ? (str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : asset('storage/' . $product->thumbnail))
        : null;
    $hasDiscount = $product->sale_price && $product->sale_price < $product->price;
    $discountPercent = $hasDiscount ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0;
@endphp

<div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden product-card-hover transition-all" style="transition: transform 0.25s ease, box-shadow 0.25s ease;">
    <!-- Badges Container -->
    <div class="position-absolute top-0 start-0 p-2 d-flex flex-column gap-1 z-2">
        @if($hasDiscount)
            <span class="badge bg-danger fw-bold rounded-pill px-2 py-1 shadow-sm">
                -{{ $discountPercent }}%
            </span>
        @endif
        @if($product->is_featured)
            <span class="badge bg-dark text-warning fw-bold rounded-pill px-2 py-1 shadow-sm">
                <i class="bi bi-fire text-danger me-1"></i>Hot
            </span>
        @endif
    </div>

    <!-- Thumbnail Link -->
    <a href="{{ route('frontend.products.show', $product->slug) }}" class="text-decoration-none position-relative overflow-hidden bg-white d-block" style="height: 220px;">
        @if($thumbUrl)
            <img src="{{ $thumbUrl }}" class="card-img-top w-100 h-100 object-fit-cover" alt="{{ $product->name }}" loading="lazy" style="transition: transform 0.3s ease;">
        @else
            <div class="bg-light text-muted d-flex flex-column align-items-center justify-content-center h-100">
                <i class="bi bi-box-seam fs-1 mb-1"></i>
                <span class="small">Chưa có ảnh</span>
            </div>
        @endif
    </a>

    <!-- Content -->
    <div class="card-body p-3 d-flex flex-column">
        <!-- Category & Brand -->
        <div class="d-flex justify-content-between align-items-center mb-1">
            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">
                {{ $product->category->name ?? 'Sneaker' }}
            </small>
            @if($product->origin)
                <span class="badge bg-light text-secondary border rounded-pill px-2" style="font-size: 0.7rem;">
                    {{ Str::limit($product->origin, 18) }}
                </span>
            @endif
        </div>

        <!-- Product Name -->
        <h6 class="card-title mb-2 fw-bold" style="font-size: 0.95rem; line-height: 1.35; min-height: 2.7rem;">
            <a href="{{ route('frontend.products.show', $product->slug) }}" class="text-decoration-none text-dark hover-danger">
                {{ $product->name }}
            </a>
        </h6>

        <!-- Price & Action -->
        <div class="mt-auto pt-2 border-top border-light d-flex justify-content-between align-items-center">
            <div>
                @if($hasDiscount)
                    <div class="text-danger fw-bold fs-6">
                        {{ number_format($product->sale_price, 0, ',', '.') }}đ
                    </div>
                    <small class="text-muted text-decoration-line-through" style="font-size: 0.8rem;">
                        {{ number_format($product->price, 0, ',', '.') }}đ
                    </small>
                @else
                    <div class="text-dark fw-bold fs-6">
                        {{ number_format($product->price, 0, ',', '.') }}đ
                    </div>
                    <small class="text-success" style="font-size: 0.75rem;">
                        <i class="bi bi-check-circle me-1"></i>Sẵn hàng
                    </small>
                @endif
            </div>

            <a href="{{ route('frontend.products.show', $product->slug) }}" class="btn btn-sm btn-outline-dark rounded-circle d-flex align-items-center justify-content-center p-2" title="Xem chi tiết" style="width: 34px; height: 34px;">
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<style>
    .product-card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }
    .product-card-hover:hover img {
        transform: scale(1.05);
    }
    .hover-danger:hover {
        color: #e11d48 !important;
    }
</style>