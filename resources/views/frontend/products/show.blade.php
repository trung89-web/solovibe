@extends('frontend.layouts.app')

@section('content')
<!-- Nút quay lại -->
<div class="mb-4">
    <a href="{{route('frontend.products.index') }}" class="text-decoration-none text-success">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<div class="row mb-5">
    <!-- Cột Hình Ảnh -->
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm border-0 mb-3">
            @if($product->thumbnail)
                @php
                    // Kiểm tra nếu là link http (online) thì giữ nguyên, ngược lại thì gọi từ storage
                    $mainImgUrl = str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : asset('storage/' . $product->thumbnail);
                @endphp
                <!-- Thêm id="mainProductImage" vào ảnh lớn -->
                <img id="mainProductImage" src="{{ $mainImgUrl }}" class="card-img-top rounded" alt="{{ $product->name }}" style="max-height: 400px; object-fit: cover;">
            @else
                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                    <span>Không có ảnh đại diện</span>
                </div>
            @endif
        </div>
        
        <!-- Gallery ảnh phụ -->
        @if($product->thumbnail || ($product->images && $product->images->count() > 0))
            <div class="d-flex overflow-auto gap-2 pb-2">
                
                <!-- Hiển thị lại ảnh đại diện làm thumbnail đầu tiên để click quay lại -->
                @if($product->thumbnail)
                    <img src="{{ $mainImgUrl }}" class="img-thumbnail shadow-sm border-success" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" alt="Thumbnail" onclick="changeImage(this.src)" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1">
                @endif

                <!-- Các ảnh phụ trong Gallery -->
                @if($product->images && $product->images->count() > 0)
                    @foreach($product->images as $img)
                        @php
                            $galleryImgUrl = str_starts_with($img->image_path, 'http') ? $img->image_path : asset('storage/' . $img->image_path);
                        @endphp
                        <!-- Thêm sự kiện onclick và cursor pointer -->
                        <img src="{{ $galleryImgUrl }}" class="img-thumbnail shadow-sm" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" alt="Gallery" onclick="changeImage(this.src)" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1">
                    @endforeach
                @endif
            </div>
        @endif
    </div>

    <!-- Cột Thông Tin Sản Phẩm -->
    <div class="col-md-7">
        <h2 class="fw-bold mb-3">{{ $product->name }}</h2>
        
        <div class="mb-3 d-flex gap-2">
            <span class="badge bg-success">Mùa vụ trồng: {{ $product->planting_season }}</span>
            <span class="badge bg-secondary">SKU: {{ $product->sku }}</span>
            @if($product->is_featured)
                <span class="badge bg-warning text-dark">Nổi bật</span>
            @endif
        </div>

        <!-- Giá bán -->
        <div class="mb-4 p-3 bg-white rounded shadow-sm border">
            @if($product->sale_price && $product->sale_price < $product->price)
                <span class="text-danger fw-bold display-6">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                <span class="text-muted text-decoration-line-through fs-5 ms-2">{{ number_format($product->price, 0, ',', '.') }}đ</span>
            @else
                <span class="text-success fw-bold display-6">{{ number_format($product->price, 0, ',', '.') }}đ</span>
            @endif
        </div>

        <p class="text-muted fs-5">{{ $product->short_description }}</p>

        <!-- Thuộc tính đặc thù -->
        <div class="card border-0 shadow-sm mb-4">
            <ul class="list-group list-group-flush">
                @if($product->origin) <li class="list-group-item"><strong>Xuất xứ:</strong> {{ $product->origin }}</li> @endif
                @if($product->tree_age) <li class="list-group-item"><strong>Độ tuổi cây:</strong> {{ $product->tree_age }}</li> @endif
                @if($product->tree_height_cm) <li class="list-group-item"><strong>Chiều cao:</strong> {{ $product->tree_height_cm }} cm</li> @endif
                @if($product->fruit_harvest_time) <li class="list-group-item"><strong>Thời gian thu hoạch:</strong> {{ $product->fruit_harvest_time }}</li> @endif
                <li class="list-group-item"><strong>Tình trạng:</strong> {{ $product->status == 'published' ? 'Còn hàng' : 'Hết hàng' }}</li>
            </ul>
        </div>
        <!-- Khung thêm vào giỏ hàng -->
            <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-4">
                @csrf
                <div class="d-flex align-items-center gap-3 mb-3">
                    <label class="fw-bold mb-0">Số lượng:</label>
                    <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="{{ $product->stock_quantity }}" style="width: 100px;" required>
                    <span class="text-muted small">(Còn {{ $product->stock_quantity }} sản phẩm)</span>
                </div>
                
                <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm fw-bold">
                    <i class="bi bi-cart-plus me-2"></i> THÊM VÀO GIỎ HÀNG
                </button>
            </form>
        </div>
</div>

<!-- Tabs Mô tả & Chăm sóc -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-success" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">Mô tả chi tiết</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-success" id="care-tab" data-bs-toggle="tab" data-bs-target="#care" type="button" role="tab">Hướng dẫn chăm sóc</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="productTabsContent">
                    <div class="tab-pane fade show active" id="desc" role="tabpanel">
                        {!! nl2br(e($product->description)) ?: 'Đang cập nhật...' !!}
                    </div>
                    <div class="tab-pane fade" id="care" role="tabpanel">
                        {!! nl2br(e($product->care_instructions)) ?: 'Đang cập nhật...' !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sản phẩm liên quan -->
@if($relatedProducts->count() > 0)
    <div class="row">
        <div class="col-12 mb-3">
            <h4 class="fw-bold border-bottom pb-2">Sản phẩm liên quan</h4>
        </div>
        @foreach($relatedProducts as $related)
            <div class="col-md-3 col-sm-6 mb-4">
                @include('components.product-card', ['product' => $related])
            </div>
        @endforeach
    </div>
@endif

<!-- JS Xử lý đổi ảnh -->
<script>
    function changeImage(newSrc) {
        document.getElementById('mainProductImage').src = newSrc;
    }
</script>
@endsection