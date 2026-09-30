<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SoleVibe - Thế Giới Giày Sneaker & Thời Trang Thể Thao Cao Cấp')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sv-primary: #f43f5e;
            --sv-primary-hover: #e11d48;
            --sv-dark: #0f172a;
            --sv-surface: #1e293b;
            --sv-accent: #fb923c;
            --sv-bg: #f8fafc;
            --sv-card-bg: #ffffff;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--sv-bg);
            color: #334155;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            letter-spacing: -0.01em;
        }

        .main-wrapper {
            flex: 1;
        }

        /* Topbar Announcement */
        .top-announcement-bar {
            background: linear-gradient(90deg, #090d16 0%, #1e293b 50%, #090d16 100%);
            font-size: 0.8rem;
            font-weight: 500;
            color: #94a3b8;
            padding: 7px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* Glassmorphism Navbar */
        .navbar-solevibe {
            background-color: rgba(15, 23, 42, 0.92) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
            padding: 0.85rem 0;
            transition: all 0.3s ease;
        }

        .navbar-brand-logo {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.8px;
            color: #ffffff !important;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .navbar-brand-logo .brand-badge {
            background: linear-gradient(135deg, #f43f5e 0%, #fb923c 100%);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            font-size: 1.15rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(244, 63, 94, 0.4);
            transition: transform 0.3s ease;
        }

        .navbar-brand-logo:hover .brand-badge {
            transform: rotate(12deg) scale(1.05);
        }

        /* Nav Items */
        .nav-link-custom {
            color: #cbd5e1 !important;
            font-weight: 600;
            font-size: 0.92rem;
            padding: 8px 18px !important;
            border-radius: 50rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .nav-link-custom:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.08);
        }

        .nav-link-custom.active-link {
            color: #ffffff !important;
            background: linear-gradient(135deg, rgba(244, 63, 94, 0.2), rgba(244, 63, 94, 0.05));
            border: 1px solid rgba(244, 63, 94, 0.3);
        }

        /* Cart Badge */
        .cart-badge {
            background: linear-gradient(135deg, #f43f5e, #e11d48);
            font-size: 0.7rem;
            font-weight: 800;
            box-shadow: 0 2px 8px rgba(244, 63, 94, 0.5);
            padding: 4px 7px;
        }

        /* Buttons */
        .btn-brand-primary {
            background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%);
            color: #ffffff;
            font-weight: 600;
            border: none;
            border-radius: 50rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(244, 63, 94, 0.3);
        }

        .btn-brand-primary:hover {
            background: linear-gradient(135deg, #e11d48 0%, #9f1239 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(244, 63, 94, 0.45);
        }

        /* User Dropdown Menu */
        .user-dropdown-btn {
            background-color: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50rem;
            padding: 6px 16px;
            transition: all 0.25s ease;
        }

        .user-dropdown-btn:hover {
            background-color: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .dropdown-menu-custom {
            background-color: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            border-radius: 16px;
            overflow: hidden;
        }

        .dropdown-menu-custom .dropdown-item {
            color: #cbd5e1;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .dropdown-menu-custom .dropdown-item:hover {
            background-color: rgba(244, 63, 94, 0.1);
            color: #f43f5e;
            transform: translateX(4px);
        }

        /* Alert Banners */
        .custom-alert {
            border: none;
            border-radius: 16px;
            backdrop-filter: blur(8px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        /* Footer */
        .footer-solevibe {
            background-color: #090d16;
            color: #64748b;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            position: relative;
        }

        .footer-title {
            color: #f8fafc;
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 1.25rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.25s ease;
            display: inline-block;
            margin-bottom: 0.65rem;
            font-size: 0.9rem;
        }

        .footer-link:hover {
            color: #f43f5e;
            transform: translateX(4px);
        }

        .footer-badge {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 8px 14px;
            color: #cbd5e1;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Top Announcement Bar -->
    <div class="top-announcement-bar d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger rounded-pill px-2 py-1 fs-12 fw-bold">🔥 HOT DEAL</span>
                <span class="text-slate-300">Miễn phí vận chuyển toàn quốc cho đơn hàng từ 1.000.000đ</span>
            </div>
            <div class="d-flex gap-4">
                <span><i class="bi bi-shield-check text-emerald-400 text-success me-1"></i>100% Chính Hãng</span>
                <span><i class="bi bi-arrow-repeat text-amber-400 text-warning me-1"></i>Đổi Size 7 Ngày</span>
                <span><i class="bi bi-telephone-fill me-1 text-danger"></i>Hotline: 1900 8899</span>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-solevibe sticky-top">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand navbar-brand-logo" href="{{ route('home') }}">
                <span class="brand-badge"><i class="bi bi-lightning-charge-fill"></i></span>
                <span>Sole<span class="text-danger">Vibe</span></span>
            </a>

            <!-- Mobile Toggler Button -->
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Nav Links & Actions -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2 mt-3 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('home') ? 'active-link text-danger' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1 d-lg-none"></i> Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('frontend.products.*') ? 'active-link text-danger' : '' }}" href="{{ route('frontend.products.index') }}">
                            <i class="bi bi-grid me-1 d-lg-none"></i> Bộ sưu tập Giày
                        </a>
                    </li>
                    <li class="nav-item me-lg-2">
                        <a class="nav-link nav-link-custom position-relative" href="{{ route('cart.index') }}">
                            <i class="bi bi-bag-check-fill fs-5 me-1 align-middle"></i>
                            <span>Giỏ hàng</span>
                            @if(isset($cartTotalQuantity) && $cartTotalQuantity > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill cart-badge border border-dark">
                                    {{ $cartTotalQuantity }}
                                </span>
                            @endif
                        </a>
                    </li>

                    <!-- Chưa Đăng Nhập -->
                    @guest
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-brand-primary px-4 py-2" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập
                            </a>
                        </li>
                    @endguest

                    <!-- Đã Đăng Nhập -->
                    @auth
                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link dropdown-toggle text-white user-dropdown-btn d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.85rem;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <span class="fw-semibold">{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom shadow-lg p-2 mt-2" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item py-2 px-3 rounded-3" href="{{ route('profile.index') }}">
                                        <i class="bi bi-person-bounding-box me-2 text-primary"></i> Tài khoản & Đơn hàng
                                    </a>
                                </li>
                                
                                @if(auth()->user()->hasAnyRole(['Super Admin', 'Content Staff']))
                                    <li><hr class="dropdown-divider my-1 border-secondary border-opacity-25"></li>
                                    <li>
                                        <a class="dropdown-item py-2 px-3 rounded-3 text-danger fw-bold" href="{{ route('products.index') }}">
                                            <i class="bi bi-speedometer2 me-2"></i> Trang Quản trị Admin
                                        </a>
                                    </li>
                                @endif

                                <li><hr class="dropdown-divider my-1 border-secondary border-opacity-25"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 px-3 rounded-3 text-muted w-100 text-start border-0 bg-transparent">
                                            <i class="bi bi-box-arrow-right me-2 text-warning"></i> Đăng xuất
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

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <div class="container mt-4">
            <!-- Alert Thông Báo Thành Công -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show custom-alert bg-emerald-50 text-emerald-800 border-0 p-3 mb-4 d-flex align-items-center" role="alert" style="background-color: #ecfdf5; color: #065f46;">
                    <i class="bi bi-check-circle-fill fs-5 me-3 text-success"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Alert Thông Báo Lỗi -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show custom-alert p-3 mb-4 d-flex align-items-center" role="alert" style="background-color: #fef2f2; color: #991b1b;">
                    <i class="bi bi-exclamation-octagon-fill fs-5 me-3 text-danger"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Nội dung thay đổi từng Trang -->
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-solevibe pt-5 pb-4 mt-5">
        <div class="container">
            <div class="row g-4 pb-5 border-bottom border-secondary border-opacity-10">
                <!-- Cột 1: Thông tin Thương hiệu -->
                <div class="col-lg-4 col-md-6">
                    <a class="navbar-brand-logo mb-3" href="{{ route('home') }}">
                        <span class="brand-badge"><i class="bi bi-lightning-charge-fill"></i></span>
                        <span>Sole<span class="text-danger">Vibe</span></span>
                    </a>
                    <p class="small text-slate-400 mb-4 pe-lg-4 lh-lg">
                        SoleVibe - Điểm đến hàng đầu cho các tín đồ Sneaker & Giày thể thao chính hãng. Cam kết chất lượng, bảo hành keo chỉ 12 tháng và hỗ trợ đổi size linh hoạt toàn quốc.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="footer-badge small">
                            <i class="bi bi-shield-check text-success me-1"></i> 100% Authentic
                        </span>
                        <span class="footer-badge small">
                            <i class="bi bi-box-seam text-warning me-1"></i> Double Box
                        </span>
                    </div>
                </div>

                <!-- Cột 2: Danh Mục -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="footer-title">Danh Mục</h6>
                    <ul class="list-unstyled mb-0">
                        <li><a href="{{ route('frontend.products.index') }}" class="footer-link">Giày Sneaker</a></li>
                        <li><a href="{{ route('frontend.products.index') }}" class="footer-link">Giày Chạy Bộ</a></li>
                        <li><a href="{{ route('frontend.products.index') }}" class="footer-link">Giày Tây & Oxford</a></li>
                        <li><a href="{{ route('frontend.products.index') }}" class="footer-link">Giày Lười Loafer</a></li>
                        <li><a href="{{ route('frontend.products.index') }}" class="footer-link">Boots & Cổ Cao</a></li>
                    </ul>
                </div>

                <!-- Cột 3: Hỗ Trợ Khách Hàng -->
                <div class="col-lg-3 col-md-6 col-6">
                    <h6 class="footer-title">Chính Sách & Hỗ Trợ</h6>
                    <ul class="list-unstyled mb-0">
                        <li><a href="{{ route('frontend.products.index') }}" class="footer-link">Hướng dẫn đo size chân</a></li>
                        <li><a href="{{ route('frontend.products.index') }}" class="footer-link">Chính sách đổi size 7 ngày</a></li>
                        <li><a href="{{ route('frontend.products.index') }}" class="footer-link">Bảo hành & Spa giày</a></li>
                        <li><a href="{{ route('frontend.products.index') }}" class="footer-link">Phương thức thanh toán</a></li>
                        <li><a href="{{ route('frontend.products.index') }}" class="footer-link">Chính sách giao hàng</a></li>
                    </ul>
                </div>

                <!-- Cột 4: Liên Hệ & Cửa Hàng -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-title">Cửa Hàng & Hotline</h6>
                    <p class="small text-slate-400 mb-2">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>Chi nhánh 1: 128 Nguyễn Trãi, Q.1, TP.HCM
                    </p>
                    <p class="small text-slate-400 mb-2">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>Chi nhánh 2: 75 Phố Huế, Hoàn Kiếm, Hà Nội
                    </p>
                    <p class="small text-slate-400 mb-2">
                        <i class="bi bi-telephone-fill text-danger me-2"></i>Hotline: <strong class="text-white">1900 8899</strong> (8:00 - 22:00)
                    </p>
                    <p class="small text-slate-400 mb-0">
                        <i class="bi bi-envelope-fill text-danger me-2"></i>Email: support@solevibe.vn
                    </p>
                </div>
            </div>

            <!-- Bản Quyền & Phương Thức Thanh Toán -->
            <div class="pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center small text-slate-500 gap-2">
                <p class="mb-0">© 2026 <strong class="text-white">SoleVibe</strong>. All rights reserved. Nền tảng thương mại điện tử chuyên giày & sneaker.</p>
                <div class="d-flex align-items-center gap-3">
                    <span>Thanh toán an toàn: COD, MoMo, VNPay, Chuyển khoản</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts bổ sung từ view con -->
    @stack('scripts')
</body>
@auth
<div id="chat-box" style="position: fixed; bottom: 25px; right: 25px; z-index: 9999;">
    <button id="chat-toggle" type="button" class="btn btn-danger rounded-circle shadow-lg p-3 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
        <i class="bi bi-chat-dots-fill fs-4"></i>
    </button>
    <div id="chat-popup" class="card shadow-lg border-0" style="display: none; width: 340px; border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
            <span class="fw-bold"><i class="bi bi-headset me-2 text-danger"></i>Hỗ trợ khách hàng</span>
            <button id="chat-close" type="button" class="btn btn-sm btn-outline-light rounded-circle border-0">✕</button>
        </div>
        <div id="chat-messages" class="card-body bg-light" style="height: 280px; overflow-y: auto;">
            <small class="text-muted">Đang tải lịch sử...</small>
        </div>
        <div class="card-footer bg-white border-top-0 p-2">
            <div class="input-group">
                <input type="text" id="chat-input" class="form-control border-0 bg-light rounded-start" placeholder="Nhập tin nhắn..." autocomplete="off">
                <button id="send-btn" type="button" class="btn btn-danger rounded-end"><i class="bi bi-send-fill"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("chat-toggle");
    const chatPopup = document.getElementById("chat-popup");
    const closeBtn = document.getElementById("chat-close");
    const sendBtn = document.getElementById("send-btn");
    const input = document.getElementById("chat-input");
    const chatBox = document.getElementById("chat-messages");

    if (!toggleBtn) return;

    toggleBtn.onclick = () => { chatPopup.style.display = "block"; toggleBtn.style.display = "none"; loadMessages(); };
    closeBtn.onclick = () => { chatPopup.style.display = "none"; toggleBtn.style.display = "flex"; };

    function loadMessages() {
        fetch("{{ route('user.chat.messages') }}")
            .then(res => res.json())
            .then(messages => {
                let html = "";
                if(messages.length === 0) {
                    html = "<div class='text-center text-muted mt-4'><small>Bắt đầu cuộc trò chuyện với SoleVibe Admin</small></div>";
                } else {
                    messages.forEach(msg => {
                        const isMe = msg.sender_id == "{{ Auth::id() }}";
                        html += `<div class="mb-2 ${isMe ? 'text-end' : 'text-start'}">
                            <span class="d-inline-block p-2 px-3 rounded-3 small ${isMe ? 'bg-danger text-white' : 'bg-white border text-dark'}">
                                ${msg.content}
                            </span>
                        </div>`;
                    });
                }
                chatBox.innerHTML = html;
                chatBox.scrollTop = chatBox.scrollHeight;
            }).catch(err => console.error("Lỗi:", err));
    }

    function sendMessage(e) {
        if (e) e.preventDefault();

        let message = input.value.trim();
        if (!message) return;
        
        input.disabled = true; 
        sendBtn.disabled = true;

        fetch("{{ route('user.chat.send') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ message: message })
        })
        .then(res => res.json())
        .then(() => { 
            input.value = ""; 
            input.disabled = false; 
            sendBtn.disabled = false; 
            input.focus(); 
            loadMessages(); 
        })
        .catch(() => { 
            input.disabled = false; 
            sendBtn.disabled = false; 
        });
    }

    sendBtn.onclick = (e) => sendMessage(e);
    input.addEventListener("keypress", (e) => { 
        if (e.key === "Enter") {
            e.preventDefault();
            sendMessage(e); 
        }
    });
    
    setInterval(() => { if (chatPopup.style.display === "block") loadMessages(); }, 3000);
});
</script>
@endauth
</html>