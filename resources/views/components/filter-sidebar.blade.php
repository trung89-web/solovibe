<form action="{{ route('frontend.products.index') }}" method="GET" class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-3"><i class="bi bi-funnel"></i> Bộ lọc</h5>

        <!-- Search Keyword -->
        <div class="mb-3">
            <input type="text" name="keyword" class="form-control" placeholder="Tìm tên cây, mô tả..." value="{{ request('keyword') }}">
        </div>

        <!-- Sort -->
        <div class="mb-3">
            <label class="form-label fw-bold">Sắp xếp</label>
            <select name="sort" class="form-select" onchange="this.form.submit()">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                <option value="best_selling" {{ request('sort') == 'best_selling' ? 'selected' : '' }}>Bán chạy</option>
            </select>
        </div>

        <hr>

        <!-- Category -->
        <div class="mb-3">
            <label class="form-label fw-bold">Danh mục</label>
            @foreach($categories as $category)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="category_id[]" value="{{ $category->id }}" id="cat_{{ $category->id }}"
                        {{ in_array($category->id, request('category_id', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="cat_{{ $category->id }}">
                        {{ $category->name }}
                    </label>
                </div>
                <!-- Hiển thị danh mục con nếu có -->
                @if($category->children->count() > 0)
                    <div class="ms-3 mt-1">
                        @foreach($category->children as $child)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="category_id[]" value="{{ $child->id }}" id="cat_{{ $child->id }}"
                                    {{ in_array($child->id, request('category_id', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat_{{ $child->id }}">
                                    {{ $child->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>

        <hr>

        <!-- Season -->
        <div class="mb-3">
            <label class="form-label fw-bold">Mùa vụ</label>
            @php $seasons = ['all_year' => 'Quanh năm', 'spring' => 'Mùa Xuân', 'summer' => 'Mùa Hè', 'autumn' => 'Mùa Thu', 'winter' => 'Mùa Đông']; @endphp
            <div class="form-check">
                <input class="form-check-input" type="radio" name="season" value="" id="season_all" {{ request('season') == '' ? 'checked' : '' }}>
                <label class="form-check-label" for="season_all">Tất cả</label>
            </div>
            @foreach($seasons as $key => $label)
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="season" value="{{ $key }}" id="season_{{ $key }}"
                        {{ request('season') == $key ? 'checked' : '' }}>
                    <label class="form-check-label" for="season_{{ $key }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>

        <hr>

        <!-- Price Range -->
        <div class="mb-3">
            <label class="form-label fw-bold">Khoảng giá (VNĐ)</label>
            <div class="row g-2">
                <div class="col-6">
                    <input type="number" name="min_price" class="form-control" placeholder="Từ" value="{{ request('min_price') }}">
                </div>
                <div class="col-6">
                    <input type="number" name="max_price" class="form-control" placeholder="Đến" value="{{ request('max_price') }}">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100">Áp dụng lọc</button>
        <a href="{{ route('frontend.products.index') }}" class="btn btn-outline-secondary w-100 mt-2">Xóa bộ lọc</a>
    </div>
</form>