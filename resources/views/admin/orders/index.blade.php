@extends('admin.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="fw-bold text-dark mb-1">📦 Quản lý Đơn hàng</h2>
        <p class="text-muted mb-0">Theo dõi, lọc, xử lý và cập nhật trạng thái đơn hàng của khách hàng</p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <ul class="nav nav-pills flex-wrap gap-2 mb-3">
            @foreach($tabs as $key => $tab)
                @php
                    $query = request()->query();
                    $query['tab'] = $key;
                    $query['page'] = 1;
                @endphp
                <li class="nav-item">
                    <a href="{{ route('orders.index', $query) }}" class="nav-link {{ $activeTab === $key ? 'active' : '' }}">
                        {{ $tab['label'] }}
                        <span class="badge bg-light text-dark ms-2">{{ $tabCounts[$key] ?? 0 }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <form method="GET" action="{{ route('orders.index') }}" class="row g-3 align-items-end">
            <div class="col-lg-3 col-md-6">
                <label class="form-label small text-muted mb-1">Tìm kiếm</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Mã đơn / tên / SĐT" value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small text-muted mb-1">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="packed" {{ request('status') === 'packed' ? 'selected' : '' }}>Đã đóng gói</option>
                    <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Đang vận chuyển</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Đã giao</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Hoàn trả</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small text-muted mb-1">TT thanh toán</label>
                <select name="payment_status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Thất bại</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small text-muted mb-1">Phương thức</label>
                <select name="payment_method" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>COD</option>
                    <option value="momo" {{ request('payment_method') === 'momo' ? 'selected' : '' }}>MoMo</option>
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label small text-muted mb-1">Sắp xếp</label>
                <select name="sort" class="form-select">
                    <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                    <option value="amount_desc" {{ request('sort') === 'amount_desc' ? 'selected' : '' }}>Giá cao nhất</option>
                    <option value="amount_asc" {{ request('sort') === 'amount_asc' ? 'selected' : '' }}>Giá thấp nhất</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small text-muted mb-1">Từ ngày</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small text-muted mb-1">Đến ngày</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small text-muted mb-1">Hiển thị</label>
                <select name="per_page" class="form-select">
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Lọc
                </button>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th class="ps-3">Mã đơn</th>
                <th>Ngày đặt</th>
                <th>Người nhận</th>
                <th>Tổng tiền</th>
                <th>TT thanh toán</th>
                <th>Trạng thái</th>
                <th class="text-center" style="width: 150px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="ps-3">
                        <div class="fw-bold text-primary font-monospace">{{ $order->order_code }}</div>
                        <small class="text-muted">{{ $order->items->count() }} sản phẩm</small>
                    </td>
                    <td>
                        <div class="small fw-semibold">{{ $order->created_at->format('d/m/Y') }}</div>
                        <div class="small text-muted">{{ $order->created_at->format('H:i') }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $order->receiver_name }}</div>
                        <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $order->receiver_phone }}</div>
                    </td>
                    <td>
                        <strong class="text-danger">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</strong>
                    </td>
                    <td>
                        @if($order->payment_status === 'paid')
                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Đã thanh toán</span>
                        @elseif($order->payment_status === 'failed')
                            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">Thất bại</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Chờ thanh toán</span>
                        @endif
                        <div class="small text-muted mt-1">
                            {{ $order->payment_method === 'cod' ? 'COD' : strtoupper($order->payment_method) }}
                        </div>
                    </td>
                    <td>
                        @if($order->order_status === 'pending')
                            <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                        @elseif($order->order_status === 'processing')
                            <span class="badge bg-info text-dark">Đang xử lý</span>
                        @elseif($order->order_status === 'packed')
                            <span class="badge bg-secondary">Đã đóng gói</span>
                        @elseif($order->order_status === 'shipping')
                            <span class="badge bg-primary">Đang vận chuyển</span>
                        @elseif($order->order_status === 'completed')
                            <span class="badge bg-success">Đã giao</span>
                        @elseif($order->order_status === 'cancelled')
                            <span class="badge bg-danger">Đã hủy</span>
                        @elseif($order->order_status === 'returned')
                            <span class="badge bg-secondary">Hoàn trả</span>
                        @else
                            <span class="badge bg-dark">{{ $order->order_status }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex flex-column gap-2">
                            <form action="{{ route('orders.update', $order) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <select name="order_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                    <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                    <option value="packed" {{ $order->order_status === 'packed' ? 'selected' : '' }}>Đã đóng gói</option>
                                    <option value="shipping" {{ $order->order_status === 'shipping' ? 'selected' : '' }}>Đang vận chuyển</option>
                                    <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Đã giao</option>
                                    <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                    <option value="returned" {{ $order->order_status === 'returned' ? 'selected' : '' }}>Hoàn trả</option>
                                </select>
                            </form>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> Chi tiết
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Không có đơn hàng nào phù hợp.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $orders->links('pagination::bootstrap-5') }}
</div>
@endsection
