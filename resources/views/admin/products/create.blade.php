@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4 mb-5">
    <h3 class="mt-4 fw-bold">Thêm Sản Phẩm Mới</h3>
    
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <!-- ==========================================
                 CỘT TRÁI (THÔNG TIN CHI TIẾT)
            =========================================== -->
            <div class="col-lg-8 mb-4">
                
                <!-- 1. Thông tin cơ bản -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white pt-3 pb-2"><h5 class="fw-bold">Thông tin cơ bản</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="VD: Nike Air Jordan 1 Retro High OG" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Mã sản phẩm (SKU) <span class="text-danger">*</span></label>
                                <input type="text" name="sku" class="form-control" placeholder="VD: BUOI-PT-01" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Thông số kỹ thuật Giày & Thời trang -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white pt-3 pb-2"><h5 class="fw-bold">Thông số kỹ thuật & Chi tiết Giày</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Thương hiệu & Xuất xứ</label>
                                <input type="text" name="origin" class="form-control" placeholder="VD: Nike - Chính Hãng">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Chất liệu thân giày (Upper)</label>
                                <input type="text" name="tree_age" class="form-control" placeholder="VD: Da bò thật 100%, Flyknit, Da lộn...">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Độ cao đế (cm)</label>
                                <input type="number" name="tree_height_cm" class="form-control" placeholder="VD: 3 hoặc 4">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Chế độ bảo hành & Kiểu dáng</label>
                                <input type="text" name="fruit_harvest_time" class="form-control" placeholder="VD: Bảo hành 12 tháng, Low-top">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Phong cách / Phù hợp</label>
                                <select name="planting_season" class="form-select">
                                    <option value="all_year">Bốn mùa / Unisex năng động</option>
                                    <option value="spring">Xuân - Hè / Thoáng khí</option>
                                    <option value="summer">Mùa Hè / Thể thao năng động</option>
                                    <option value="autumn">Thu - Đông / Ấm áp</option>
                                    <option value="winter">Mùa Đông / Kháng nước</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Nội dung mô tả -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white pt-3 pb-2"><h5 class="fw-bold">Nội dung chi tiết</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả ngắn</label>
                            <textarea name="short_description" class="form-control" rows="3" placeholder="Đoạn văn ngắn giới thiệu điểm nổi bật của mẫu giày..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả chi tiết & Công nghệ</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Chi tiết chất liệu, công nghệ đệm, độ bám đế..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hướng dẫn vệ sinh & Bảo quản giày</label>
                            <textarea name="care_instructions" class="form-control" rows="4" placeholder="VD: Dùng bọt vệ sinh chuyên dụng, không giặt máy giặt, phơi nơi râm mát..."></textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ==========================================
                 CỘT PHẢI (TRẠNG THÁI, ẢNH & GIÁ MẶC ĐỊNH)
            =========================================== -->
            <div class="col-lg-4 mb-4">
                
                <!-- Publishing -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white pt-3 pb-2"><h5 class="fw-bold">Trạng thái hiển thị</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="published">Hiển thị (Published)</option>
                                <option value="draft">Bản nháp (Draft)</option>
                                <option value="out_of_stock">Hết hàng (Out of stock)</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1">
                            <label class="form-check-label fw-bold" for="is_featured">Đánh dấu Sản phẩm Nổi bật</label>
                        </div>
                    </div>
                </div>

                <!-- Media -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white pt-3 pb-2"><h5 class="fw-bold">Hình ảnh</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ảnh đại diện (Thumbnail)</label>
                            <input type="file" name="thumbnail" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Thư viện ảnh (Gallery)</label>
                            <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                            <small class="text-muted">Có thể chọn nhiều ảnh cùng lúc.</small>
                        </div>
                    </div>
                </div>

                <!-- Giá & Tồn kho mặc định -->
                <div class="card shadow-sm border-0 mb-4" id="basic_price_info">
                    <div class="card-header bg-white pt-3 pb-2"><h5 class="fw-bold">Giá & Tồn kho mặc định</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Giá khuyến mãi (VNĐ)</label>
                            <input type="number" name="sale_price" class="form-control" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tồn kho <span class="text-danger">*</span></label>
                            <input type="number" name="stock_quantity" class="form-control" min="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             KHỐI DƯỚI CÙNG (THIẾT LẬP BIẾN THỂ)
        =========================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body bg-light rounded">
                        <div class="form-check form-switch fs-5 mb-0">
                            <input class="form-check-input" type="checkbox" id="has_variations" name="has_variations" value="1">
                            <label class="form-check-label fw-bold text-success" for="has_variations">
                                Kích hoạt Phân loại hàng hóa (Sản phẩm có nhiều Kích thước, Đóng gói...)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Bảng thiết lập biến thể (Duy trì y hệt tính năng Shopee vừa làm) -->
                <div id="variations_container" class="card shadow-sm border-success mb-4 d-none">
                    <div class="card-header bg-success text-white pt-3 pb-2">
                        <h5 class="fw-bold mb-0">Thiết lập chi tiết Phân loại</h5>
                    </div>
                    <div class="card-body">
                        
                        <div class="row mb-4">
                            @foreach($attributes as $attribute)
                                <div class="col-md-4 attribute-group" data-attr-id="{{ $attribute->id }}">
                                    <label class="fw-bold fs-6 mb-2 border-bottom pb-1 text-dark d-block">{{ $attribute->name }}</label>
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($attribute->values as $val)
                                            <div class="form-check">
                                                <input class="form-check-input variation-checkbox border-secondary" type="checkbox"
                                                    name="attribute_values[{{ $attribute->id }}][]"
                                                    value="{{ $val->id }}"
                                                    data-val-name="{{ $val->value }}"
                                                    id="val_{{ $val->id }}">
                                                <label class="form-check-label" for="val_{{ $val->id }}">{{ $val->value }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr class="text-success border-2 opacity-50">

                        <h6 class="fw-bold mb-3 text-dark">Danh sách các phiên bản sẽ được tạo:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle" id="variations_table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 200px;">Tên phân loại</th>
                                        <th style="min-width: 150px;">Giá bán (VNĐ) <span class="text-danger">*</span></th>
                                        <th style="min-width: 150px;">Giá KM (VNĐ)</th>
                                        <th style="min-width: 120px;">Tồn kho <span class="text-danger">*</span></th>
                                        <th style="min-width: 150px;">Mã SKU riêng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="no_variation_row"><td colspan="5" class="text-center text-muted py-4">Vui lòng tick chọn ít nhất 1 thuộc tính ở trên để sinh tổ hợp.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mb-5">
            <button type="submit" class="btn btn-success btn-lg px-5 shadow fw-bold">
                <i class="bi bi-save me-2"></i> LƯU SẢN PHẨM
            </button>
        </div>
    </form>
</div>

<!-- ==========================================
     JAVASCRIPT XỬ LÝ 
=========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleVariations = document.getElementById('has_variations');
    const varContainer = document.getElementById('variations_container');
    const basicPriceContainer = document.getElementById('basic_price_info'); 
    const basicInputs = basicPriceContainer.querySelectorAll('input[type="number"]'); // Chỉ chọn ô number để gỡ required

    // Logic Ẩn/Hiện form
    toggleVariations.addEventListener('change', function() {
        if (this.checked) {
            varContainer.classList.remove('d-none');
            basicPriceContainer.classList.add('d-none');
            basicInputs.forEach(input => input.removeAttribute('required'));
        } else {
            varContainer.classList.add('d-none');
            basicPriceContainer.classList.remove('d-none');
            basicInputs.forEach(input => {
                if(input.name !== 'sale_price') input.setAttribute('required', 'true');
            });
        }
    });

    let variationCache = {}; 

    function generateVariations() {
        document.querySelectorAll('#variations_table tbody tr.var-row').forEach(row => {
            let key = row.getAttribute('data-key');
            variationCache[key] = {
                price: row.querySelector('.var-price').value,
                sale: row.querySelector('.var-sale').value,
                stock: row.querySelector('.var-stock').value,
                sku: row.querySelector('.var-sku').value,
            };
        });

        let groups = [];
        document.querySelectorAll('.attribute-group').forEach(group => {
            let checkedBoxes = Array.from(group.querySelectorAll('.variation-checkbox:checked'));
            if (checkedBoxes.length > 0) {
                groups.push(checkedBoxes.map(cb => ({ id: cb.value, name: cb.getAttribute('data-val-name') })));
            }
        });

        let tbody = document.querySelector('#variations_table tbody');
        if (groups.length === 0) {
            tbody.innerHTML = '<tr id="no_variation_row"><td colspan="5" class="text-center text-muted py-4">Vui lòng tick chọn ít nhất 1 thuộc tính ở trên để sinh tổ hợp.</td></tr>';
            return;
        }

        const cartesian = (...a) => a.reduce((a, b) => a.flatMap(d => b.map(e => [d, e].flat())));
        let combos = cartesian(...groups);

        tbody.innerHTML = '';
        combos.forEach((combo, index) => {
            let items = Array.isArray(combo) ? combo : [combo];
            let key = items.map(i => i.id).join('_');
            let name = items.map(i => i.name).join(' - ');
            let attrValIds = items.map(i => i.id).join(',');

            let cached = variationCache[key] || { price: '', sale: '', stock: '0', sku: '' };

            let tr = document.createElement('tr');
            tr.className = 'var-row';
            tr.setAttribute('data-key', key);
            tr.innerHTML = `
                <td class="align-middle fw-bold text-success">
                    ${name}
                    <input type="hidden" name="variations[${index}][attribute_value_ids]" value="${attrValIds}">
                </td>
                <td><input type="number" name="variations[${index}][price]" class="form-control var-price" value="${cached.price}" required min="0"></td>
                <td><input type="number" name="variations[${index}][sale_price]" class="form-control var-sale" value="${cached.sale}" min="0"></td>
                <td><input type="number" name="variations[${index}][stock_quantity]" class="form-control var-stock" value="${cached.stock}" required min="0"></td>
                <td><input type="text" name="variations[${index}][sku]" class="form-control var-sku" value="${cached.sku}"></td>
            `;
            tbody.appendChild(tr);
        });
    }

    document.querySelectorAll('.variation-checkbox').forEach(cb => {
        cb.addEventListener('change', generateVariations);
    });
});
</script>
@endsection