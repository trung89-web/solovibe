@extends('admin.layouts.app')

@section('content')
<div class="mb-4">
    <h2>Cập Nhật Sản Phẩm: {{ $product->name }}</h2>
    <a href="{{ route('products.index') }}" class="text-decoration-none">← Quay lại danh sách</a>
</div>

<form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- Cột thông tin chính -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Tên Sản Phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Mã sản phẩm (SKU)</label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}">
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả ngắn</label>
                        <textarea name="short_description" class="form-control" rows="2">{{ old('short_description', $product->short_description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả chi tiết</label>
                        <textarea name="description" id="editor" class="form-control">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột thông số & Ảnh -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Giá gốc (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', round($product->price)) }}" min="0">
                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giá khuyến mãi (VNĐ)</label>
                        <input type="number" name="sale_price" class="form-control @error('sale_price') is-invalid @enderror" value="{{ old('sale_price', round($product->sale_price)) }}" min="0">
                        @error('sale_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tồn kho <span class="text-danger">*</span></label>
                        <input type="number" name="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0">
                        @error('stock_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select name="status" class="form-select">
                            <option value="published" {{ old('status', $product->status) == 'published' ? 'selected' : '' }}>Hiển thị (Published)</option>
                            <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Bản nháp (Draft)</option>
                        </select>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="isFeatured" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                        <label class="form-check-label text-warning fw-bold" for="isFeatured">Đánh dấu Nổi bật</label>
                    </div>
                </div>
            </div>

            <!-- Block Ảnh Đại Diện -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <label class="form-label fw-bold">Ảnh đại diện (Thumbnail)</label>
                    <div class="mb-2">
                        @if($product->thumbnail)
                            <img src="{{ asset('storage/' . $product->thumbnail) }}" class="img-thumbnail" width="120" alt="Thumbnail">
                        @else
                            <span class="text-muted">Chưa có ảnh đại diện</span>
                        @endif
                    </div>
                    <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
                    <small class="text-muted">Tải lên ảnh mới sẽ ghi đè ảnh cũ.</small>
                    @error('thumbnail') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Block Thư Viện Ảnh (Gallery) -->
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <label class="form-label fw-bold">Thư viện ảnh hiện tại</label>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @forelse($product->images as $image)
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                <!-- Nút xoá gọi form ẩn ở cuối trang -->
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 py-0 px-1" 
                                        onclick="if(confirm('Bạn có chắc muốn xoá ảnh này?')) document.getElementById('delete-img-{{ $image->id }}').submit();">
                                    &times;
                                </button>
                            </div>
                        @empty
                            <span class="text-muted small">Chưa có ảnh phụ.</span>
                        @endforelse
                    </div>

                    <label class="form-label fw-bold">Thêm ảnh vào thư viện</label>
                    <input type="file" name="images[]" class="form-control @error('images.*') is-invalid @enderror" accept="image/*" multiple>
                    <small class="text-muted">Có thể chọn nhiều ảnh cùng lúc để thêm vào.</small>
                    @error('images.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-warning w-100 btn-lg fw-bold">Cập Nhật Sản Phẩm</button>
        </div>
    </div>
</form>

<!-- Các Form ẩn dùng để xoá từng ảnh trong Gallery -->
@foreach($product->images as $image)
    <form id="delete-img-{{ $image->id }}" action="{{ route('products.images.destroy', $image->id) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@endforeach

<!-- Tích hợp CKEditor 5 -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => {
            console.error(error);
        });
</script>
@endsection