<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Fruit Tree E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    @stack('scripts') <!-- Dành cho CKEditor -->
</head>
<body class="bg-light">

    <div class="d-flex" style="min-height: 100vh;">
        
        <!-- SIDEBAR -->
        <div class="bg-dark text-white p-3 shadow" style="width: 260px;">
            <a href="{{ route('home') }}" class="text-decoration-none">
                <h4 class="text-center text-success fw-bold mb-4 mt-2">🌱 Admin Panel</h4>
            </a>            
            <!-- Thông tin User -->
            <div class="mb-4 pb-3 border-bottom border-secondary text-center">
                <div class="fw-bold fs-5">{{ Auth::user()->name }}</div>
                <span class="badge bg-danger mt-1">{{ Auth::user()->roles->pluck('name')->implode(', ') }}</span>
            </div>

            <!-- Menu Điều Hướng -->
            <ul class="nav flex-column mb-auto">
                <li class="nav-item mb-2">
                    <a href="{{ route('categories.index') }}" class="nav-link text-white {{ request()->routeIs('categories.*') ? 'bg-success rounded' : '' }}">
                        <i class="bi bi-folder2-open me-2"></i> Quản lý Danh mục
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('products.index') }}" class="nav-link text-white {{ request()->routeIs('products.*') ? 'bg-success rounded' : '' }}">
                        <i class="bi bi-box-seam me-2"></i> Quản lý Sản phẩm
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('orders.index') }}" class="nav-link text-white {{ request()->routeIs('orders.*') ? 'bg-success rounded' : '' }}">
                        <i class="bi bi-cart-check me-2"></i> Quản lý Đơn hàng
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('attributes.index') }}" class="nav-link text-white {{ request()->routeIs('attributes.*') ? 'bg-success rounded' : '' }}">
                        <i class="bi bi-tags me-2"></i> Quản lý Thuộc tính
                    </a>
                </li>

                <!-- Nút Đăng Xuất -->
                <li class="nav-item mt-2">
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf

                        <input type="hidden" name="from_admin" value="1">
                        
                        <button type="submit" class="btn btn-outline-light w-100 text-start">
                            <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                        </button>
                    </form>
                </li>
            </ul>
        </div>

        <!-- NỘI DUNG CHÍNH (MAIN CONTENT) -->
        <div class="flex-grow-1 p-4 overflow-auto" style="height: 100vh;">
            
            <!-- Khu vực hiển thị thông báo flash -->
            @if(session('success'))
                <div class="alert alert-success shadow-sm alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger shadow-sm alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Nội dung thay đổi từng trang -->
            <div class="bg-white p-4 rounded shadow-sm">
                @yield('content')
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>