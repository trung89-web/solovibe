@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h2 class="mt-4 fw-bold">Quản lý Nhóm Thuộc Tính</h2>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Quản trị</a></li>
       <li class="breadcrumb-item active">Nhóm thuộc tính</li>
    </ol>

    <!-- Hiển thị thông báo -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Cột trái: Form thêm mới -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 border-top border-danger border-4">
                <div class="card-header bg-white border-bottom-0 pt-3">
                    <h5 class="fw-bold mb-0">Thêm Nhóm mới</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('attributes.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên Nhóm Thuộc Tính</label>
                            <input type="text" name="name" class="form-control" placeholder="VD: Kích cỡ (Size), Màu sắc..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Các giá trị (Ngăn cách bằng dấu phẩy)</label>
                            <textarea name="values" class="form-control" rows="3" placeholder="VD: 38, 39, 40, 41, 42, 43 hoặc Trắng, Đen, Đỏ, Xám" required></textarea>
                            <div class="form-text">Hệ thống sẽ tự động tách các giá trị này ra.</div>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 shadow-sm fw-bold">
                            <i class="bi bi-plus-circle me-1"></i> THÊM MỚI
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Cột phải: Danh sách -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white pt-3 pb-2">
                    <h5 class="fw-bold mb-0">Danh sách Thuộc Tính</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Tên nhóm</th>
                                    <th>Các giá trị</th>
                                    <th class="text-center" style="width: 100px;">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attributes as $attribute)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $attribute->name }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($attribute->values as $val)
                                                <span class="badge bg-secondary">{{ $val->value }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('attributes.destroy', $attribute->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Xóa nhóm thuộc tính này sẽ ảnh hưởng đến các sản phẩm đang dùng nó. Chắc chắn xóa?');">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Chưa có nhóm thuộc tính nào.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection