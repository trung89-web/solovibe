<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - SoleVibe Sneaker</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --sv-admin-dark: #0f172a;
            --sv-admin-surface: #1e293b;
            --sv-admin-primary: #f43f5e;
            --sv-admin-primary-hover: #e11d48;
            --sv-admin-bg: #f8fafc;
            --sv-admin-border: rgba(255, 255, 255, 0.08);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--sv-admin-bg);
            color: #334155;
            letter-spacing: -0.01em;
        }

        /* Sidebar Styling */
        .admin-sidebar {
            width: 280px;
            background-color: var(--sv-admin-dark);
            border-right: 1px solid var(--sv-admin-border);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .brand-logo-admin {
            background: linear-gradient(135deg, rgba(244, 63, 94, 0.15), rgba(251, 146, 60, 0.05));
            border: 1px solid rgba(244, 63, 94, 0.2);
            border-radius: 16px;
            padding: 12px 16px;
            transition: transform 0.2s ease;
        }

        .brand-logo-admin:hover {
            transform: translateY(-2px);
        }

        .brand-icon {
            background: linear-gradient(135deg, #f43f5e, #fb923c);
            color: white;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(244, 63, 94, 0.35);
        }

        /* User Profile Card */
        .user-card-admin {
            background-color: var(--sv-admin-surface);
            border: 1px solid var(--sv-admin-border);
            border-radius: 14px;
            padding: 12px 16px;
        }

        /* Navigation Links */
        .admin-nav-link {
            color: #94a3b8 !important;
            font-weight: 600;
            font-size: 0.92rem;
            padding: 11px 16px !important;
            border-radius: 12px;
            display: flex;
            align-items: center;
            transition: all 0.25s ease;
            text-decoration: none;
            margin-bottom: 6px;
        }

        .admin-nav-link:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.06);
            transform: translateX(4px);
        }

        .admin-nav-link.active-admin {
            color: #ffffff !important;
            background: linear-gradient(135deg, #f43f5e, #be123c) !important;
            box-shadow: 0 4px 15px rgba(244, 63, 94, 0.35);
        }

        .admin-nav-link.active-admin:hover {
            transform: none;
        }

        /* Main Content Container */
        .main-content-admin {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
            min-height: calc(100vh - 110px);
        }

        /* Logout Button */
        .btn-admin-logout {
            background-color: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--sv-admin-border);
            color: #cbd5e1;
            font-weight: 600;
            border-radius: 12px;
            padding: 10px;
            transition: all 0.2s ease;
        }

        .btn-admin-logout:hover {
            background-color: rgba(244, 63, 94, 0.15);
            border-color: rgba(244, 63, 94, 0.3);
            color: #f43f5e;
        }

        /* Custom Alert Cards */
        .custom-alert {
            border: none;
            border-radius: 14px;
            backdrop-filter: blur(8px);
        }

        /* Admin Livechat Floating Styles */
        #admin-chat-box {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 9999;
        }

        #chat-toggle-btn {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            border: 1px solid rgba(244, 63, 94, 0.4);
            color: white;
            padding: 12px 22px;
            border-radius: 50rem;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }

        #chat-toggle-btn:hover {
            transform: translateY(-3px);
            border-color: #f43f5e;
            box-shadow: 0 12px 30px rgba(244, 63, 94, 0.3);
        }

        .chat-widget-card {
            width: 520px;
            height: 460px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            border: 1px solid rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .user-chat-item {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-chat-item:hover {
            background-color: #f8fafc;
        }

        .user-chat-item.active-chat-user {
            background: linear-gradient(135deg, rgba(244, 63, 94, 0.1), rgba(244, 63, 94, 0.03));
            border-left: 4px solid #f43f5e;
            font-weight: 700;
        }
    </style>

    @stack('scripts') <!-- Dành cho CKEditor hoặc Script con -->
</head>
<body class="bg-light">

    <div class="d-flex" style="min-height: 100vh;">
        
        <!-- SIDEBAR BAN QUẢN TRỊ -->
        <aside class="admin-sidebar p-3 shadow-lg">
            
            <!-- Logo & Link Về Trang Chủ Website -->
            <a href="{{ route('home') }}" class="text-decoration-none mb-4 mt-2 d-block">
                <div class="brand-logo-admin d-flex align-items-center gap-3">
                    <span class="brand-icon"><i class="bi bi-lightning-charge-fill fs-5"></i></span>
                    <div>
                        <h5 class="fw-bold text-white m-0 tracking-tight">Sole<span class="text-danger">Vibe</span></h5>
                        <small class="text-slate-400 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Admin Portal</small>
                    </div>
                </div>
            </a>            
            
            <!-- Thông Tin Quản Trị Viên -->
            <div class="user-card-admin mb-4 d-flex align-items-center gap-3">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; font-size: 0.95rem;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <div class="fw-bold text-white text-truncate" style="font-size: 0.9rem;">{{ Auth::user()->name }}</div>
                    <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 mt-1" style="font-size: 0.7rem;">
                        {{ Auth::user()->roles->pluck('name')->implode(', ') }}
                    </span>
                </div>
            </div>

            <!-- Menu Điều Hướng Chi Tiết -->
            <ul class="nav flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('categories.index') }}" class="admin-nav-link {{ request()->routeIs('categories.*') ? 'active-admin' : '' }}">
                        <i class="bi bi-folder2-open fs-5 me-3"></i> Quản lý Danh mục
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('products.index') }}" class="admin-nav-link {{ request()->routeIs('products.*') ? 'active-admin' : '' }}">
                        <i class="bi bi-box-seam fs-5 me-3"></i> Quản lý Sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('orders.index') }}" class="admin-nav-link {{ request()->routeIs('orders.*') ? 'active-admin' : '' }}">
                        <i class="bi bi-cart-check fs-5 me-3"></i> Quản lý Đơn hàng
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="admin-nav-link {{ request()->routeIs('admin.users.*') ? 'active-admin' : '' }}">
                        <i class="bi bi-people fs-5 me-3"></i> Quản lý Người dùng
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.reports.index') }}" class="admin-nav-link {{ request()->routeIs('admin.reports.index') ? 'active-admin' : '' }}">
                        <i class="bi bi-bar-chart fs-5 me-3"></i> Thống kê tài chính
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.reports.transactions') }}" class="admin-nav-link {{ request()->routeIs('admin.reports.transactions') ? 'active-admin' : '' }}">
                        <i class="bi bi-currency-exchange fs-5 me-3"></i> Giao dịch thanh toán
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('attributes.index') }}" class="admin-nav-link {{ request()->routeIs('attributes.*') ? 'active-admin' : '' }}">
                        <i class="bi bi-tags fs-5 me-3"></i> Quản lý Thuộc tính
                    </a>
                </li>
            </ul>

            <!-- Form Đăng Xuất -->
            <div class="pt-3 border-top border-secondary border-opacity-25 mt-3">
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="from_admin" value="1">
                    
                    <button type="submit" class="btn btn-admin-logout w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                        <span>Đăng xuất Admin</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- NỘI DUNG CHÍNH (MAIN CONTENT) -->
        <div class="flex-grow-1 d-flex flex-column" style="height: 100vh; overflow-y: auto;">
            
            <!-- Topbar Header phụ phía trên -->
            <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center sticky-top">
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <i class="bi bi-speedometer2 text-danger"></i>
                    <span>Hệ thống Quản lý SoleVibe</span>
                </div>
                <div>
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-dark rounded-pill px-3" target="_blank">
                        <i class="bi bi-globe me-1"></i> Xem Website
                    </a>
                </div>
            </header>

            <main class="p-4 flex-grow-1">
                
                <!-- Khu vực hiển thị thông báo flash -->
                @if(session('success'))
                    <div class="alert alert-success custom-alert shadow-sm alert-dismissible fade show p-3 mb-4 d-flex align-items-center" role="alert" style="background-color: #ecfdf5; color: #065f46;">
                        <i class="bi bi-check-circle-fill fs-5 me-3 text-success"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger custom-alert shadow-sm alert-dismissible fade show p-3 mb-4 d-flex align-items-center" role="alert" style="background-color: #fef2f2; color: #991b1b;">
                        <i class="bi bi-exclamation-octagon-fill fs-5 me-3 text-danger"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Khung chứa nội dung thay đổi từng trang -->
                <div class="main-content-admin p-4">
                    @yield('content')
                </div>

            </main>
        </div>
    </div>

    <!-- MODULE LIVECHAT CHAT VỚI KHÁCH HÀNG (GÓC PHẢI MÀN HÌNH) -->
    <div id="admin-chat-box">
        <button id="chat-toggle-btn" class="btn">
            <i class="bi bi-headset text-danger me-2 fs-5 align-middle"></i> Livechat Khách Hàng
        </button>

        <div id="chat-popup" class="card chat-widget-card" style="display: none;">
            <!-- Chat Header -->
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger rounded-circle p-2"><i class="bi bi-chat-dots-fill"></i></span>
                    <span class="fw-bold">Trung Tâm Hỗ Trợ Chat</span>
                </div>
                <button id="chat-close" class="btn btn-sm btn-outline-light rounded-circle border-0">✕</button>
            </div>

            <!-- Chat Body: Chia 2 Cột (Danh sách Khách - Nội dung Chat) -->
            <div class="card-body p-0 d-flex flex-grow-1" style="height: 320px;">
                <!-- Cột Trái: Danh sách Khách hàng -->
                <div id="user-list" class="border-end bg-light" style="width: 40%; overflow-y: auto;">
                    <div class="p-3 text-center text-muted"><small>Đang tải danh sách...</small></div>
                </div>

                <!-- Cột Phải: Khung Tin Nhắn -->
                <div id="chat-messages" class="p-3 bg-white d-flex flex-column" style="width: 60%; overflow-y: auto;">
                    <div class="text-center my-auto text-muted small">
                        <i class="bi bi-chat-square-text fs-3 d-block mb-2 text-slate-300"></i>
                        Chọn một khách hàng để xem nội dung trò chuyện
                    </div>
                </div>
            </div>

            <!-- Chat Footer Input -->
            <div class="card-footer bg-white p-2 border-top">
                <div class="input-group">
                    <input type="text" id="chat-input" class="form-control border-0 bg-light rounded-start px-3" placeholder="Nhập câu trả lời..." disabled>
                    <button id="send-btn" class="btn btn-danger px-3 rounded-end" disabled>
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SCRIPT XỬ LÝ LIVECHAT ADMIN -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let currentUserId = null;
            const chatToggleBtn = document.getElementById("chat-toggle-btn");
            const chatPopup = document.getElementById("chat-popup");
            const chatCloseBtn = document.getElementById("chat-close");
            const userListEl = document.getElementById("user-list");
            const chatMessagesEl = document.getElementById("chat-messages");
            const chatInput = document.getElementById("chat-input");
            const sendBtn = document.getElementById("send-btn");

            // Mở / Đóng Khung Chat
            chatToggleBtn.onclick = () => {
                chatPopup.style.display = "flex";
                chatToggleBtn.style.display = "none";
                loadUsers();
            };

            chatCloseBtn.onclick = () => {
                chatPopup.style.display = "none";
                chatToggleBtn.style.display = "block";
            };

            // Tải Danh sách Khách hàng đã gửi tin nhắn
            function loadUsers() {
                fetch("{{ route('admin.chat.users') }}")
                    .then(res => res.json())
                    .then(users => {
                        let html = "";
                        if(users.length === 0) {
                            html = `<div class="p-3 text-center text-muted small">Chưa có cuộc trò chuyện nào</div>`;
                        } else {
                            if (!currentUserId) {
                                currentUserId = users[0].id;
                            }

                            users.forEach(user => {
                                const activeClass = (Number(currentUserId) === Number(user.id)) ? 'active-chat-user' : '';
                                html += `
                                    <div class="user-chat-item small ${activeClass}" data-id="${user.id}">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-person-circle fs-6 text-slate-400"></i>
                                            <span class="text-truncate">${user.name}</span>
                                        </div>
                                    </div>`;
                            });
                        }
                        userListEl.innerHTML = html;

                        // Gán sự kiện click chọn User
                        document.querySelectorAll('.user-chat-item').forEach(item => {
                            item.onclick = function() {
                                currentUserId = this.getAttribute('data-id');
                                document.querySelectorAll('.user-chat-item').forEach(el => el.classList.remove('active-chat-user'));
                                this.classList.add('active-chat-user');
                                chatInput.disabled = false;
                                sendBtn.disabled = false;
                                loadMessages();
                            };
                        });

                        if (currentUserId) {
                            const activeItem = document.querySelector(`.user-chat-item[data-id="${currentUserId}"]`);
                            if (activeItem) {
                                activeItem.classList.add('active-chat-user');
                                chatInput.disabled = false;
                                sendBtn.disabled = false;
                                loadMessages();
                            }
                        }
                    })
                    .catch(err => console.error("Lỗi tải người dùng:", err));
            }

            // Tải Tin nhắn của Khách hàng được chọn
            function loadMessages() {
                if (!currentUserId) return;
                fetch(`/admin/chat/messages/${currentUserId}`)
                    .then(res => res.json())
                    .then(messages => {
                        let html = "";
                        if (messages.length === 0) {
                            html = `<div class="text-center my-auto text-muted small">Chưa có tin nhắn</div>`;
                        } else {
                            messages.forEach(msg => {
                                const isMe = msg.sender_id == "{{ Auth::id() }}";
                                html += `
                                    <div class="mb-2 ${isMe ? 'text-end' : 'text-start'}">
                                        <span class="d-inline-block p-2 px-3 rounded-3 small ${isMe ? 'bg-dark text-white' : 'bg-light text-dark border'}">
                                            ${msg.content}
                                        </span>
                                    </div>`;
                            });
                        }
                        chatMessagesEl.innerHTML = html;
                        chatMessagesEl.scrollTop = chatMessagesEl.scrollHeight;
                    })
                    .catch(err => console.error("Lỗi tải tin nhắn:", err));
            }

            // Gửi tin nhắn trả lời Khách hàng
            function sendMessage() {
                const message = chatInput.value.trim();
                if (!message || !currentUserId) return;

                chatInput.disabled = true;
                sendBtn.disabled = true;

                fetch("{{ route('admin.chat.send') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: message, user_id: currentUserId })
                })
                .then(res => res.json())
                .then(() => {
                    chatInput.value = "";
                    chatInput.disabled = false;
                    sendBtn.disabled = false;
                    chatInput.focus();
                    loadMessages();
                })
                .catch(err => {
                    chatInput.disabled = false;
                    sendBtn.disabled = false;
                    console.error("Lỗi gửi tin nhắn:", err);
                });
            }

            sendBtn.onclick = sendMessage;
            chatInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter") sendMessage();
            });

            // Tự động làm mới danh sách tin nhắn mỗi 3 giây khi mở chat
            setInterval(() => {
                if (chatPopup.style.display === "flex") {
                    loadUsers();
                    if (currentUserId) loadMessages();
                }
            }, 3000);
        });
    </script>
</body>
</html>