<form action="{{ route('frontend.products.index') }}" method="GET" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-dark text-white py-3">
        <h6 class="card-title mb-0 fw-bold d-flex align-items-center">
            <i class="bi bi-funnel-fill text-danger me-2"></i> BỘ LỌC TÌM KIẾM
        </h6>
    </div>

    <div class="card-body p-3 p-lg-4">
        <!-- Search Keyword -->
        <div class="mb-4">
            <label class="form-label fw-bold small text-uppercase text-secondary">Từ khóa</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="keyword" class="form-control border-start-0" placeholder="Tên giày, thương hiệu..." value="{{ request('keyword') }}">
            </div>
        </div>

        <!-- Sort -->
        <div class="mb-4">
            <label class="form-label fw-bold small text-uppercase text-secondary">Sắp xếp theo</label>
            <select name="sort" class="form-select" onchange="this.form.submit()">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                <option value="best_selling" {{ request('sort') == 'best_selling' ? 'selected' : '' }}>Bán chạy nhất</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
            </select>
        </div>

        <hr class="my-3 opacity-25">

        <!-- Category Filter -->
        <div class="mb-4">
            <label class="form-label fw-bold small text-uppercase text-secondary mb-2">Danh mục Giày</label>
            <div class="d-flex flex-column gap-2" style="max-height: 220px; overflow-y: auto;">
                @foreach($categories as $category)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="category_id[]" value="{{ $category->id }}" id="cat_{{ $category->id }}"
                            {{ in_array($category->id, (array)request('category_id', [])) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="cat_{{ $category->id }}">
                            {{ $category->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <hr class="my-3 opacity-25">

        <!-- Size Filter -->
        <div class="mb-4">
            <label class="form-label fw-bold small text-uppercase text-secondary mb-2">Kích cỡ (Size)</label>
            @php
                $sizes = ['36', '37', '38', '39', '40', '41', '42', '43', '44'];
            @endphp
            <div class="d-flex flex-wrap gap-2">
                @foreach($sizes as $sz)
                    <input type="radio" class="btn-check" name="size" id="size_{{ $sz }}" value="{{ $sz }}" 
                           {{ request('size') == $sz ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="btn btn-sm {{ request('size') == $sz ? 'btn-danger' : 'btn-outline-secondary' }} px-3 py-1 fw-bold" for="size_{{ $sz }}">
                        {{ $sz }}
                    </label>
                @endforeach
            </div>
            @if(request('size'))
                <div class="mt-2">
                    <a href="{{ request()->fullUrlWithQuery(['size' => null]) }}" class="small text-danger text-decoration-none">
                        <i class="bi bi-x-circle me-1"></i>Bỏ chọn size ({{ request('size') }})
                    </a>
                </div>
            @endif
        </div>

        <hr class="my-3 opacity-25">

        <!-- Price Range -->
        <div class="mb-4">
            <label class="form-label fw-bold small text-uppercase text-secondary">Khoảng giá (VNĐ)</label>
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Từ" value="{{ request('min_price') }}">
                </div>
                <div class="col-6">
                    <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Đến" value="{{ request('max_price') }}">
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <button type="submit" class="btn btn-danger w-100 py-2 fw-bold shadow-sm mb-2">
            <i class="bi bi-check2-circle me-1"></i> ÁP DỤNG LỌC
        </button>
        <a href="{{ route('frontend.products.index') }}" class="btn btn-outline-secondary w-100 btn-sm">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Xóa bộ lọc
        </a>
    </div>
</form>