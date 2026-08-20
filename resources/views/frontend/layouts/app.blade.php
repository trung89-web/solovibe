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
                    <li class="nav-item me-3">
                        <a class="nav-link" href="#">Giỏ hàng <i class="bi bi-cart"></i></a>
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
                        <!-- Nút Vào Quản Trị (Chỉ hiện nếu là Admin) -->
                        @if(auth()->user()->hasAnyRole(['Super Admin', 'Content Staff']))
                            <li class="nav-item me-2">
                                <a class="btn btn-warning btn-sm fw-bold" href="{{ route('products.index') }}">
                                    <i class="bi bi-speedometer2"></i> Vào Quản trị
                                </a>
                            </li>
                        @endif

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-bold text-success" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">Đăng xuất</button>
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
    <div class="container">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p class="mb-0">© 2026 Fruit Tree E-commerce. Demo Version.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>