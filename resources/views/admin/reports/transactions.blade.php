@extends('admin.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="fw-bold text-dark mb-1">💳 Giao dịch thanh toán</h2>
        <p class="text-muted mb-0">Theo dõi giao dịch COD và MoMo, trạng thái thanh toán, tìm kiếm và lọc đơn giản.</p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.transactions') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Mã đơn, người nhận, giao dịch...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Gateway</label>
                <select name="gateway" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="cod" {{ request('gateway') === 'cod' ? 'selected' : '' }}>COD</option>
                    <option value="momo" {{ request('gateway') === 'momo' ? 'selected' : '' }}>MoMo</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Thất bại</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">Lọc</button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Mã đơn</th>
                <th>Người nhận</th>
                <th>Gateway</th>
                <th>Số tiền</th>
                <th>Trạng thái</th>
                <th>Ngày thanh toán</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td class="fw-semibold text-primary">{{ $transaction->order->order_code ?? 'N/A' }}</td>
                    <td>{{ $transaction->order->receiver_name ?? '-' }}</td>
                    <td>
                        @if($transaction->gateway === 'momo')
                            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">MoMo</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">COD</span>
                        @endif
                    </td>
                    <td class="fw-semibold text-danger">{{ number_format($transaction->amount, 0, ',', '.') }} ₫</td>
                    <td>
                        @if($transaction->status === 'paid')
                            <span class="badge bg-success">Đã thanh toán</span>
                        @elseif($transaction->status === 'failed')
                            <span class="badge bg-danger">Thất bại</span>
                        @elseif($transaction->status === 'cancelled')
                            <span class="badge bg-secondary">Đã hủy</span>
                        @else
                            <span class="badge bg-warning text-dark">Chờ xử lý</span>
                        @endif
                    </td>
                    <td>{{ $transaction->paid_at ? $transaction->paid_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>
                        @if($transaction->gateway === 'cod' && $transaction->order)
                            <form action="{{ route('admin.reports.cod.update', $transaction->order) }}" method="POST" class="d-flex gap-2 align-items-center">
                                @csrf
                                @method('PUT')
                                <select name="payment_status" class="form-select form-select-sm">
                                    <option value="pending" {{ $transaction->status === 'pending' ? 'selected' : '' }}>Chờ</option>
                                    <option value="paid" {{ $transaction->status === 'paid' ? 'selected' : '' }}>Đã nhận</option>
                                    <option value="failed" {{ $transaction->status === 'failed' ? 'selected' : '' }}>Thất bại</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Cập nhật</button>
                            </form>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">Không có giao dịch nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $transactions->links('pagination::bootstrap-5') }}
</div>
@endsection
