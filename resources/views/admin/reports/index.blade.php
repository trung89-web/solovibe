@extends('admin.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="fw-bold text-dark mb-1">📊 Báo cáo & Thống kê</h2>
        <p class="text-muted mb-0">Tổng quan hiệu suất bán hàng, doanh thu và hoạt động khách hàng</p>
    </div>
    <a href="{{ route('admin.reports.export') }}" class="btn btn-success">
        <i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Doanh thu tổng</div>
                <h3 class="fw-bold mb-0 text-danger">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Đơn hàng</div>
                <h3 class="fw-bold mb-0 text-primary">{{ number_format($totalOrders) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Hoàn thành</div>
                <h3 class="fw-bold mb-0 text-success">{{ number_format($completedOrders) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">Khách mới hôm nay</div>
                <h3 class="fw-bold mb-0 text-warning">{{ number_format($newCustomers) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 fw-bold">Doanh thu theo tháng</div>
            <div class="card-body">
                <canvas id="revenueChart" style="max-height: 260px; height: 260px;"></canvas>
                <div class="table-responsive mt-3">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tháng</th>
                                <th>Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monthlyRevenue as $item)
                                <tr>
                                    <td>{{ $item->month }}</td>
                                    <td class="fw-semibold text-danger">{{ number_format($item->revenue, 0, ',', '.') }} ₫</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">Chưa có dữ liệu doanh thu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 fw-bold">Tình trạng đơn hàng</div>
            <div class="card-body">
                @foreach(['pending' => 'Chờ xác nhận', 'processing' => 'Đang chuẩn bị', 'shipping' => 'Đang giao', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'] as $status => $label)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">{{ $label }}</span>
                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">
                            {{ $orderStats[$status]->total ?? 0 }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 fw-bold">Top sản phẩm bán chạy</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-end">Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-center">{{ $item->total_qty }}</td>
                            <td class="text-end fw-semibold text-danger">{{ number_format($item->total_sales, 0, ',', '.') }} ₫</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Chưa có sản phẩm nào được bán.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartLabels = @json($monthlyRevenue->pluck('month')->all());
    const chartValues = @json($monthlyRevenue->pluck('revenue')->all());

    const ctx = document.getElementById('revenueChart');
    if (ctx && chartLabels.length) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Doanh thu',
                    data: chartValues,
                    backgroundColor: '#f43f5e',
                    borderRadius: 8,
                    maxBarThickness: 28
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' ₫';
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection
