@extends('admin.layouts.app')

@section('content')
<div class="mb-4">
    <h2>Cập Nhật Danh Mục: {{ $category->name }}</h2>
    <a href="{{ route('categories.index') }}" class="text-decoration-none">← Quay lại danh sách</a>
</div>

<form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label class="form-label">Tên Danh Mục <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Đường dẫn (Slug)</label>
                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $category->slug) }}">
                @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $category->description) }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Danh mục cha</label>
                <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                    <option value="">-- Không có --</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Thứ tự hiển thị</label>
                <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $category->sort_order) }}" min="0">
                @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Hình ảnh hiện tại</label>
                <div>
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="img" width="100" class="img-thumbnail mb-2">
                    @else
                        <span class="text-muted">Không có ảnh</span>
                    @endif
                </div>
                <label class="form-label">Tải ảnh mới lên (sẽ ghi đè ảnh cũ)</label>
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="isActive">Hiển thị (Active)</label>
            </div>
        </div>
    </div>
    
    <button type="submit" class="btn btn-warning">Cập Nhật</button>
</form>
@endsection