<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fruit Tree E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">🌱 Cây Giống Tốt</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('frontend.products.index') }}">Sản phẩm</a>
                    </li>
                    <li class="nav-item me-3 mt-1">
                        <a class="nav-link position-relative text-success fw-bold" href="{{ route('cart.index') }}">
                            <i class="bi bi-cart3 fs-5"></i> Giỏ hàng
                            <!-- Nếu có sản phẩm thì hiện chấm đỏ -->
                            @if(isset($cartTotalQuantity) && $cartTotalQuantity > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light border-2 mt-2">
                                    {{ $cartTotalQuantity }}
                                </span>
                            @endif
                        </a>
                    </li>
                    
                    <!-- Nếu CHƯA đăng nhập -->
                    @guest
                        <li class="nav-item">
                            <a class="btn btn-success btn-sm px-4 fw-bold text-white shadow-sm" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                            </a>
                        </li>
                    @endguest

                    <!-- Nếu ĐÃ đăng nhập -->
                    @auth
                        <li class="nav-item dropdown">
                            <!-- Sửa text-success thành text-white cho dễ nhìn -->
                            <a class="nav-link dropdown-toggle fw-bold text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                
                                <!-- Nút Vào Quản Trị (Chỉ hiện nếu là Admin) -->
                                @if(auth()->user()->hasAnyRole(['Super Admin', 'Content Staff']))
                                    <li>
                                        <a class="dropdown-item text-warning fw-bold" href="{{ route('products.index') }}">
                                            <i class="bi bi-speedometer2"></i> Vào Quản trị
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif

                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mt-3">
        <!-- Khu vực hiển thị thông báo thành công -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p class="mb-0">© 2026 Fruit Tree E-commerce. Demo Version.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>