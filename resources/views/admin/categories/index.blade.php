@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý Danh mục</h2>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">Thêm Mới</a>
</div>

<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Hình ảnh</th>
            <th>Tên Danh Mục</th>
            <th>Đường dẫn (Slug)</th>
            <th>Danh mục cha</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $category)
        <tr>
            <td>{{ $category->id }}</td>
            <td>
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="img" width="50" class="img-thumbnail">
                @endif
            </td>
            <td>{{ $category->name }}</td>
            <td>{{ $category->slug }}</td>
            <td>{{ $category->parent ? $category->parent->name : '-- Trống --' }}</td>
            <td>
                <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $category->is_active ? 'Hiển thị' : 'Ẩn' }}
                </span>
            </td>
            <td>
                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning">Sửa</a>
                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-3">
    {{ $categories->links('pagination::bootstrap-5') }}
</div>
@endsection