@extends('frontend.layouts.app')

@section('title', $product->name . ' - SoleVibe')

@section('content')
<!-- Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('frontend.products.index') }}" class="text-decoration-none text-muted">Bộ sưu tập Giày</a></li>
        <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $product->name }}</li>
    </ol>
</nav>

<div class="row mb-5 gy-4">
    <!-- Cột Hình Ảnh -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-3 bg-white">
            @php
                $mainImgUrl = $product->thumbnail 
                    ? (str_starts_with($product->thumbnail, 'http') ? $product->thumbnail : asset('storage/' . $product->thumbnail))
                    : null;
            @endphp
            @if($mainImgUrl)
                <img id="mainProductImage" src="{{ $mainImgUrl }}" class="card-img-top w-100 object-fit-cover" alt="{{ $product->name }}" style="max-height: 480px; min-height: 380px;">
            @else
                <div class="bg-light text-muted d-flex flex-column align-items-center justify-content-center" style="height: 400px;">
                    <i class="bi bi-image fs-1 mb-2"></i>
                    <span>Chưa có ảnh hiển thị</span>
                </div>
            @endif
        </div>
        
        <!-- Gallery ảnh phụ -->
        @if($mainImgUrl || ($product->images && $product->images->count() > 0))
            <div class="d-flex overflow-auto gap-2 pb-2">
                @if($mainImgUrl)
                    <img src="{{ $mainImgUrl }}" class="img-thumbnail rounded-3 shadow-sm border-2 border-danger" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" alt="Thumbnail" onclick="changeImage(this.src)" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1">
                @endif
                @if($product->images && $product->images->count() > 0)
                    @foreach($product->images as $img)
                        @php
                            $galleryImgUrl = str_starts_with($img->image_path, 'http') ? $img->image_path : asset('storage/' . $img->image_path);
                        @endphp
                        <img src="{{ $galleryImgUrl }}" class="img-thumbnail rounded-3 shadow-sm" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" alt="Gallery" onclick="changeImage(this.src)" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1">
                    @endforeach
                @endif
            </div>
        @endif
    </div>

    <!-- Cột Thông Tin Sản Phẩm -->
    <div class="col-lg-6">
        <div class="ps-lg-3">
            <!-- Brand & Badges -->
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <span class="badge bg-danger rounded-pill px-3 py-1">
                    <i class="bi bi-shield-check me-1"></i>100% Chính Hãng
                </span>
                <span class="badge bg-secondary rounded-pill px-3 py-1">
                    SKU: {{ $product->sku }}
                </span>
                @if($product->is_featured)
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold">
                        <i class="bi bi-fire text-danger me-1"></i>Bán chạy
                    </span>
                @endif
                <span class="badge bg-light text-dark border rounded-pill px-3 py-1">
                    {{ $product->gender_style }}
                </span>
            </div>

            <!-- Product Title -->
            <h1 class="fw-bold mb-3 text-dark" style="font-size: 1.85rem; line-height: 1.3;">
                {{ $product->name }}
            </h1>

            <!-- Price Block -->
            <div class="mb-4 p-3 bg-white rounded-4 shadow-sm border border-light d-flex align-items-baseline gap-3">
                @if($product->has_variations)
                    <div id="price_container">
                        <span class="text-danger fw-extrabold display-6" id="display_price">
                            {{ number_format($product->variations->min('price'), 0, ',', '.') }}đ - {{ number_format($product->variations->max('price'), 0, ',', '.') }}đ
                        </span>
                        <span class="text-muted text-decoration-line-through fs-5 ms-2 d-none" id="display_sale_price"></span>
                    </div>
                @else
                    <div>
                        @if($product->sale_price && $product->sale_price < $product->price)
                            <span class="text-danger fw-extrabold display-6">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                            <span class="text-muted text-decoration-line-through fs-5 ms-2">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                            <span class="badge bg-danger ms-2">Tiết kiệm {{ number_format($product->price - $product->sale_price, 0, ',', '.') }}đ</span>
                        @else
                            <span class="text-dark fw-extrabold display-6">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Short Description -->
            <p class="text-secondary mb-4 fs-6 leading-relaxed">
                {{ $product->short_description }}
            </p>

            <!-- Shoe Specs Table -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-light py-2 fw-bold text-dark small text-uppercase">
                    <i class="bi bi-info-circle me-1 text-danger"></i> Thông Số Kỹ Thuật
                </div>
                <ul class="list-group list-group-flush small">
                    @if($product->origin)
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Thương hiệu & Xuất xứ:</span>
                            <strong class="text-dark">{{ $product->origin }}</strong>
                        </li>
                    @endif
                    @if($product->material)
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Chất liệu thân giày (Upper):</span>
                            <strong class="text-dark">{{ $product->material }}</strong>
                        </li>
                    @endif
                    @if($product->sole_height_cm)
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Chiều cao đế (Sole Height):</span>
                            <strong class="text-dark">{{ $product->sole_height_cm }} cm (Đệm êm)</strong>
                        </li>
                    @endif
                    @if($product->warranty)
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Chế độ bảo hành:</span>
                            <strong class="text-danger">{{ $product->warranty }}</strong>
                        </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Tình trạng:</span>
                        <span class="badge bg-success-subtle text-success border border-success">
                            {{ $product->status == 'published' ? 'Còn hàng trong kho' : 'Hết hàng' }}
                        </span>
                    </li>
                </ul>
            </div>

            <!-- Form Đặt Hàng & Chọn Size -->
            <form action="{{ route('cart.add', $product) }}" method="POST" id="add-to-cart-form">
                @csrf
                <input type="hidden" name="variation_id" id="selected_variation_id" value="{{ !$product->has_variations ? $product->variations->first()->id ?? '' : '' }}">
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                @if($product->has_variations && $product->variations->count() > 0)
                    <div class="mb-4 p-3 bg-white rounded-4 shadow-sm border border-light">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="fw-bold text-dark mb-0">
                                <i class="bi bi-ruler me-1 text-danger"></i> Chọn Size giày:
                            </label>
                            <!-- Size Guide Modal Trigger -->
                            <button type="button" class="btn btn-link text-danger p-0 small fw-semibold text-decoration-none" data-bs-toggle="modal" data-bs-target="#sizeGuideModal">
                                <i class="bi bi-question-circle me-1"></i> Bảng đo size chuẩn
                            </button>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            @foreach($product->variations as $var)
                                @php
                                    if($var->sku == $product->sku . '-DEFAULT') continue;
                                    $label = $var->attributeValues->pluck('value')->implode(' - ');
                                    if(empty($label)) continue;
                                    $isOutOfStock = $var->stock_quantity <= 0;
                                @endphp

                                <input type="radio" class="btn-check variation-radio" 
                                    name="variation_id_radio" 
                                    id="var_{{ $var->id }}" 
                                    value="{{ $var->id }}" 
                                    {{ $isOutOfStock ? 'disabled' : '' }}
                                    autocomplete="off">
                                <label class="btn {{ $isOutOfStock ? 'btn-outline-secondary opacity-50' : 'btn-outline-dark' }} px-3 py-2 fw-bold rounded-3" for="var_{{ $var->id }}">
                                    {{ $label }}
                                    @if($isOutOfStock) <small class="d-block text-danger" style="font-size: 0.68rem;">(Hết)</small> @endif
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-2">
                            <span id="variation-warning" class="text-danger small fw-bold {{ $product->has_variations ? '' : 'd-none' }}">
                                * Vui lòng nhấp chọn Size giày ở trên để mua hàng.
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Quantity -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <label class="fw-bold text-dark mb-0">Số lượng:</label>
                    <div class="input-group" style="width: 140px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(-1)">-</button>
                        <input type="number" name="quantity" id="quantity_input" class="form-control text-center fw-bold" value="1" min="1" max="{{ $product->has_variations ? 1 : $product->stock_quantity }}" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(1)">+</button>
                    </div>
                    <span class="text-muted small">
                        (Còn lại: <span id="display_stock" class="fw-bold text-dark">{{ $product->has_variations ? '---' : $product->stock_quantity }}</span> đôi)
                    </span>
                </div>

                <!-- Action Buttons -->
                <div class="row g-2 mb-4">
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-outline-dark btn-lg w-100 rounded-pill shadow-sm fw-bold py-3" id="btn-add-cart" {{ $product->has_variations ? 'disabled' : '' }}>
                            <i class="bi bi-bag-plus me-2"></i> THÊM VÀO GIỎ
                        </button>
                    </div>
                    <div class="col-sm-6">
                        <button type="submit" name="buy_now" value="1" class="btn btn-brand-primary btn-lg w-100 rounded-pill shadow fw-bold py-3" id="btn-buy-now" {{ $product->has_variations ? 'disabled' : '' }}>
                            <i class="bi bi-lightning-charge-fill me-1"></i> MUA NGAY
                        </button>
                    </div>
                </div>
            </form>

            <!-- Value Propositions Badges -->
            <div class="p-3 bg-light rounded-4 border d-flex flex-column gap-2 small">
                <div class="d-flex align-items-center text-secondary">
                    <i class="bi bi-truck text-danger fs-5 me-2"></i>
                    <span><strong>Giao hàng toàn quốc:</strong> Kiểm tra hàng thử size trước khi thanh toán tiền mặt (COD).</span>
                </div>
                <div class="d-flex align-items-center text-secondary">
                    <i class="bi bi-arrow-repeat text-danger fs-5 me-2"></i>
                    <span><strong>Đổi size miễn phí trong 7 ngày:</strong> Đổi tận nơi dễ dàng nếu không vừa chân.</span>
                </div>
                <div class="d-flex align-items-center text-secondary">
                    <i class="bi bi-box2-heart text-danger fs-5 me-2"></i>
                    <span><strong>Đóng hộp Double-Box:</strong> Bảo vệ hộp giày nguyên vẹn đến tay khách hàng.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs: Mô tả, Bảo quản & Bảo hành -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom pt-3 pb-0">
                <ul class="nav nav-tabs border-bottom-0" id="productTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold text-danger border-danger border-bottom-0" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button">
                            <i class="bi bi-card-text me-1"></i> Mô tả chi tiết & Công nghệ
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold text-dark border-0" id="care-tab" data-bs-toggle="tab" data-bs-target="#care" type="button">
                            <i class="bi bi-droplet-half me-1"></i> Hướng dẫn vệ sinh & Bảo quản
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold text-dark border-0" id="policy-tab" data-bs-toggle="tab" data-bs-target="#policy" type="button">
                            <i class="bi bi-shield-check me-1"></i> Chính sách đổi size & Bảo hành
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="productTabsContent">
                    <div class="tab-pane fade show active leading-relaxed" id="desc">
                        {!! $product->description ?: '<p class="text-muted">Thông tin mô tả đang được cập nhật...</p>' !!}
                    </div>
                    <div class="tab-pane fade leading-relaxed" id="care">
                        <h6 class="fw-bold text-dark mb-3">Mẹo giữ gìn và chăm sóc giày luôn như mới:</h6>
                        {!! $product->care_instructions ?: '<p class="text-muted">Hướng dẫn bảo quản đang được cập nhật...</p>' !!}
                    </div>
                    <div class="tab-pane fade leading-relaxed" id="policy">
                        <h6 class="fw-bold text-dark mb-2">Chính sách bảo hành & Đổi size tại SoleVibe:</h6>
                        <ul>
                            <li><strong>Đổi size miễn phí trong 7 ngày:</strong> Sản phẩm còn nguyên tem mác, chưa qua sử dụng ngoài trời và hộp giày còn nguyên vẹn.</li>
                            <li><strong>Bảo hành keo chỉ 12 tháng:</strong> Miễn phí dán keo, may chỉ và bảo dưỡng đế giày trong suốt thời gian bảo hành.</li>
                            <li><strong>Cam kết 100% chính hãng:</strong> Phát hiện hàng giả, hàng nhái hoàn tiền gấp 2 lần giá trị đơn hàng.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sản phẩm tương tự -->
@if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="row mb-5">
        <div class="col-12 mb-3">
            <h4 class="fw-bold text-dark text-uppercase border-bottom pb-2">
                <i class="bi bi-stars text-danger me-2"></i>SẢN PHẨM CÙNG BỘ SƯU TẬP
            </h4>
        </div>
        @foreach($relatedProducts as $related)
            <div class="col-lg-3 col-md-4 col-6 mb-3">
                <x-product-card :product="$related" />
            </div>
        @endforeach
    </div>
@endif

@if($product->reviews->count() > 0)
    <div class="row mb-5">
        <div class="col-12 mb-3">
            <h4 class="fw-bold text-dark border-bottom pb-2">
                <i class="bi bi-star-fill text-warning me-2"></i>ĐÁNH GIÁ KHÁCH HÀNG
            </h4>
        </div>
        <div class="col-12">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="fs-3 fw-bold text-warning">{{ number_format($product->average_rating, 1) }}</div>
                <div>
                    <div class="text-warning">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= round($product->average_rating) ? '-fill' : '' }}"></i>
                        @endfor
                    </div>
                    <small class="text-muted">Dựa trên {{ $product->review_count }} đánh giá</small>
                </div>
            </div>

            @foreach($product->reviews->take(5) as $review)
                <div class="border rounded-4 p-3 mb-3 bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-semibold text-dark">{{ $review->user->name ?? 'Khách hàng' }}</div>
                        <div class="text-warning small">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                            @endfor
                        </div>
                    </div>
                    @if($review->comment)
                        <p class="mb-0 text-secondary">{{ $review->comment }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Size Guide Modal -->
<div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-labelledby="sizeGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="sizeGuideModalLabel">
                    <i class="bi bi-ruler me-2 text-danger"></i> Bảng Quy Đổi Kích Cỡ Giày Chuẩn
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">
                    Hãy đo chiều dài bàn chân từ gót chân đến đầu ngón chân dài nhất (cm) vào buổi chiều để có kết quả chính xác nhất.
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Size VN / EU</th>
                                <th>Chiều dài chân (cm)</th>
                                <th>Size US (Nam)</th>
                                <th>Size US (Nữ)</th>
                                <th>Size UK</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td><strong>36</strong></td><td>22.5 cm</td><td>4.5</td><td>6.0</td><td>3.5</td></tr>
                            <tr><td><strong>37</strong></td><td>23.0 cm</td><td>5.0</td><td>6.5</td><td>4.0</td></tr>
                            <tr><td><strong>38</strong></td><td>23.5 - 24.0 cm</td><td>6.0</td><td>7.5</td><td>5.0</td></tr>
                            <tr><td><strong>39</strong></td><td>24.5 cm</td><td>6.5</td><td>8.0</td><td>6.0</td></tr>
                            <tr><td><strong>40</strong></td><td>25.0 cm</td><td>7.0</td><td>8.5</td><td>6.5</td></tr>
                            <tr><td><strong>41</strong></td><td>25.5 - 26.0 cm</td><td>8.0</td><td>9.5</td><td>7.5</td></tr>
                            <tr><td><strong>42</strong></td><td>26.5 cm</td><td>8.5</td><td>10.0</td><td>8.0</td></tr>
                            <tr><td><strong>43</strong></td><td>27.0 - 27.5 cm</td><td>9.5</td><td>11.0</td><td>9.0</td></tr>
                            <tr><td><strong>44</strong></td><td>28.0 cm</td><td>10.0</td><td>11.5</td><td>9.5</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-warning mb-0 small">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i>
                    <strong>Mẹo chọn size:</strong> Nếu chân bè hoặc mu bàn chân dày, khuyến khích bạn chọn tăng thêm 0.5 - 1 size so với bảng kích cỡ để có cảm giác thoải mái nhất khi đi tất.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function changeImage(newSrc) {
        document.getElementById('mainProductImage').src = newSrc;
    }

    function adjustQty(amount) {
        const input = document.getElementById('quantity_input');
        let current = parseInt(input.value) || 1;
        let max = parseInt(input.max) || 99;
        let nextVal = current + amount;
        if (nextVal >= 1 && nextVal <= max) {
            input.value = nextVal;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const hasVariations = {{ $product->has_variations ? 'true' : 'false' }};
        const variations = @json($variationsJson ?? []);
        
        if (!hasVariations) return;

        const radios = document.querySelectorAll('.variation-radio');
        const priceEl = document.getElementById('display_price');
        const salePriceEl = document.getElementById('display_sale_price');
        const stockEl = document.getElementById('display_stock');
        const varIdInput = document.getElementById('selected_variation_id');
        const btnAddCart = document.getElementById('btn-add-cart');
        const btnBuyNow = document.getElementById('btn-buy-now');
        const warningText = document.getElementById('variation-warning');
        const qtyInput = document.getElementById('quantity_input');

        const formatMoney = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + 'đ';

        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                const selectedId = parseInt(this.value);
                const matchedVar = variations.find(v => v.id === selectedId);

                if (matchedVar) {
                    if (matchedVar.sale_price && matchedVar.sale_price < matchedVar.price) {
                        priceEl.innerText = formatMoney(matchedVar.sale_price);
                        salePriceEl.innerText = formatMoney(matchedVar.price);
                        salePriceEl.classList.remove('d-none');
                    } else {
                        priceEl.innerText = formatMoney(matchedVar.price);
                        salePriceEl.classList.add('d-none');
                    }

                    stockEl.innerText = matchedVar.stock_quantity;
                    varIdInput.value = matchedVar.id;
                    qtyInput.max = matchedVar.stock_quantity;
                    qtyInput.value = 1;

                    btnAddCart.disabled = false;
                    if (btnBuyNow) {
                        btnBuyNow.disabled = false;
                    }
                    if (warningText) {
                        warningText.classList.add('d-none');
                    }
                }
            });
        });
    });
</script>
@endsection