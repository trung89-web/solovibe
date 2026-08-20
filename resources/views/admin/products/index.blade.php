@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý Sản phẩm</h2>
    <a href="{{ route('products.create') }}" class="btn btn-primary">Thêm Sản Phẩm</a>
</div>

<table class="table table-bordered table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Hình ảnh</th>
            <th>Tên / SKU</th>
            <th>Danh mục</th>
            <th>Giá bán</th>
            <th>Tồn kho</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>
                @if($product->thumbnail)
                    <img src="{{ asset('storage/' . $product->thumbnail) }}" width="60" class="img-thumbnail" alt="img">
                @else
                    <span class="text-muted">Trống</span>
                @endif
            </td>
            <td>
                <strong>{{ $product->name }}</strong><br>
                <small class="text-muted">SKU: {{ $product->sku ?? 'N/A' }}</small>
            </td>
            <td>{{ $product->category->name ?? 'N/A' }}</td>
            <td>
                @if($product->sale_price)
                    <span class="text-danger">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span><br>
                    <del class="text-muted small">{{ number_format($product->price, 0, ',', '.') }}đ</del>
                @else
                    <span class="text-success">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                @endif
            </td>
            <td>{{ $product->stock_quantity }}</td>
            <td>
                <span class="badge {{ $product->status == 'published' ? 'bg-success' : 'bg-secondary' }}">
                    {{ $product->status == 'published' ? 'Hiển thị' : 'Bản nháp' }}
                </span>
            </td>
            <td>
                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">Sửa</a>
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Xác nhận xóa sản phẩm này?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">Chưa có sản phẩm nào.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">
    {{ $products->links('pagination::bootstrap-5') }}
</div>
@endsection