@extends('admin.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="fw-bold text-dark mb-1">📦 Quản lý Đơn hàng</h2>
        <p class="text-muted mb-0">Theo dõi, xử lý và cập nhật trạng thái đơn hàng của khách hàng</p>
    </div>
</div>

<!-- Bộ lọc & Tìm kiếm -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('orders.index') }}" class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Tìm kiếm theo mã đơn, người nhận, số điện thoại..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang chuẩn bị hàng</option>
                    <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Hoàn trả</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Lọc
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Bảng danh sách đơn hàng -->
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th class="ps-3">Mã đơn hàng</th>
                <th>Ngày đặt</th>
                <th>Người nhận</th>
                <th>Tổng tiền</th>
                <th>Thanh toán</th>
                <th>Trạng thái</th>
                <th class="text-center" style="width: 140px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td class="ps-3">
                        <strong class="text-primary font-monospace">{{ $order->order_code }}</strong>
                        <div class="text-muted small">{{ $order->items->count() }} sản phẩm</div>
                    </td>
                    <td>
                        <div class="text-dark small">{{ $order->created_at->format('d/m/Y') }}</div>
                        <div class="text-muted small">{{ $order->created_at->format('H:i') }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $order->receiver_name }}</div>
                        <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $order->receiver_phone }}</div>
                    </td>
                    <td>
                        <strong class="text-danger fs-6">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</strong>
                    </td>
                    <td>
                        @if($order->payment_status === 'paid')
                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">
                                <i class="bi bi-check2 me-1"></i>Đã thanh toán
                            </span>
                        @elseif($order->payment_status === 'failed')
                            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">
                                <i class="bi bi-x me-1"></i>Thất bại
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                <i class="bi bi-hourglass-split me-1"></i>Chờ thanh toán
                            </span>
                        @endif
                        <div class="text-muted small mt-1">
                            @if($order->payment_method === 'cod')
                                COD (Khi nhận hàng)
                            @elseif($order->payment_method === 'momo')
                                Ví MoMo
                            @else
                                {{ strtoupper($order->payment_method) }}
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($order->order_status === 'pending')
                            <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Chờ xác nhận</span>
                        @elseif($order->order_status === 'processing')
                            <span class="badge bg-info text-dark"><i class="bi bi-gear me-1"></i>Đang chuẩn bị</span>
                        @elseif($order->order_status === 'shipping')
                            <span class="badge bg-primary"><i class="bi bi-truck me-1"></i>Đang giao hàng</span>
                        @elseif($order->order_status === 'completed')
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Hoàn thành</span>
                        @elseif($order->order_status === 'cancelled')
                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Đã hủy</span>
                        @elseif($order->order_status === 'returned')
                            <span class="badge bg-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i>Hoàn trả</span>
                        @else
                            <span class="badge bg-secondary">{{ $order->order_status }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary px-3">
                            <i class="bi bi-eye me-1"></i> Chi tiết
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Không tìm thấy đơn hàng nào phù hợp.
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
