<div class="card h-100 shadow-sm">
    <!-- Thumbnail -->
    @if($product->thumbnail)
        <img src="{{ asset('storage/' . $product->thumbnail) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
    @else
        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
            <span>No Image</span>
        </div>
    @endif

    <!-- Content -->
    <div class="card-body d-flex flex-column">
        <div class="mb-2">
            <span class="badge bg-success">{{ __('mùa vụ: ' . $product->planting_season) }}</span>
            @if($product->is_featured)
                <span class="badge bg-warning text-dark">Nổi bật</span>
            @endif
        </div>
        
        <h5 class="card-title">
            <a href="{{ route('frontend.products.show', $product->slug) }}" class="text-decoration-none text-dark">
                {{ $product->name }}
            </a>
        </h5>
        
        <div class="mt-auto">
            @if($product->sale_price && $product->sale_price < $product->price)
                <span class="text-danger fw-bold fs-5">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                <span class="text-muted text-decoration-line-through ms-2">{{ number_format($product->price, 0, ',', '.') }}đ</span>
            @else
                <span class="text-success fw-bold fs-5">{{ number_format($product->price, 0, ',', '.') }}đ</span>
            @endif
        </div>
    </div>
</div>