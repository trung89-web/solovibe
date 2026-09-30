@extends('admin.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
        <h2 class="fw-bold text-dark mb-0">
            Chi tiết Đơn hàng: <span class="text-primary font-monospace">#{{ $order->order_code }}</span>
        </h2>
        <span class="text-muted small">
            <i class="bi bi-calendar3 me-1"></i> Ngày đặt: {{ $order->created_at->format('d/m/Y H:i:s') }}
        </span>
    </div>
    <div>
        @if($order->order_status === 'pending')
            <span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="bi bi-clock-history me-1"></i>Chờ xác nhận</span>
        @elseif($order->order_status === 'processing')
            <span class="badge bg-info text-dark fs-6 px-3 py-2"><i class="bi bi-gear me-1"></i>Đang xử lý</span>
        @elseif($order->order_status === 'packed')
            <span class="badge bg-secondary fs-6 px-3 py-2"><i class="bi bi-box-seam me-1"></i>Đã đóng gói</span>
        @elseif($order->order_status === 'shipping')
            <span class="badge bg-primary fs-6 px-3 py-2"><i class="bi bi-truck me-1"></i>Đang vận chuyển</span>
        @elseif($order->order_status === 'completed')
            <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle me-1"></i>Đã giao</span>
        @elseif($order->order_status === 'cancelled')
            <span class="badge bg-danger fs-6 px-3 py-2"><i class="bi bi-x-circle me-1"></i>Đã hủy</span>
        @elseif($order->order_status === 'returned')
            <span class="badge bg-secondary fs-6 px-3 py-2"><i class="bi bi-arrow-counterclockwise me-1"></i>Hoàn trả</span>
        @else
            <span class="badge bg-secondary fs-6 px-3 py-2">{{ $order->order_status }}</span>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Cột trái: Thông tin sản phẩm & Khách hàng -->
    <div class="col-lg-8">
        
        <!-- Danh sách sản phẩm -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-bag-check-fill text-success me-2"></i> Danh sách sản phẩm ({{ $order->items->count() }})
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 70px;">Ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Phân loại / Thuộc tính</th>
                                <th class="text-center">Đơn giá</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end pe-3">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $itemsSubtotal = 0;
                            @endphp
                            @foreach($order->items as $item)
                                @php
                                    $itemSubtotal = $item->subtotal ?? ($item->price * $item->quantity);
                                    $itemsSubtotal += $itemSubtotal;
                                    $thumbnail = $item->product?->thumbnail ?? '';
                                    if (!empty($thumbnail)) {
                                        $imgUrl = str_starts_with($thumbnail, 'http') ? $thumbnail : asset('storage/' . $thumbnail);
                                    } else {
                                        $imgUrl = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="%23ccc" viewBox="0 0 16 16"><rect width="100%" height="100%" fill="%23f8f9fa"/><text x="50%" y="50%" fill="%23999" dominant-baseline="middle" text-anchor="middle" font-size="8">No Image</text></svg>';
                                    }
                                    $variationText = $item->variation_label ?? ($item->variation?->attributeValues ? $item->variation->attributeValues->pluck('value')->implode(' - ') : '');
                                @endphp
                                <tr>
                                    <td class="ps-3 py-3">
                                        <img src="{{ $imgUrl }}" alt="{{ $item->product_name }}" class="rounded border" style="width: 52px; height: 52px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $item->product_name }}</div>
                                        @if($item->product)
                                            <a href="{{ route('frontend.products.show', $item->product->slug) }}" target="_blank" class="text-muted small text-decoration-none">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Xem trên web
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($variationText))
                                            <span class="badge bg-light text-dark border">{{ $variationText }}</span>
                                        @else
                                            <span class="text-muted small">Mặc định</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format($item->price, 0, ',', '.') }} ₫</td>
                                    <td class="text-center fw-bold">x{{ $item->quantity }}</td>
                                    <td class="text-end pe-3 text-danger fw-bold">{{ number_format($itemSubtotal, 0, ',', '.') }} ₫</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Thông tin khách hàng & Giao hàng -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-geo-alt-fill text-danger me-2"></i> Thông tin khách hàng & Giao hàng
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border h-100">
                            <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-person-circle me-1"></i> Người nhận hàng</h6>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Họ và tên:</span>
                                <strong class="text-dark">{{ $order->receiver_name }}</strong>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Số điện thoại:</span>
                                <strong class="text-primary font-monospace">{{ $order->receiver_phone }}</strong>
                            </div>
                            <div>
                                <span class="text-muted small d-block">Tài khoản đặt hàng:</span>
                                @if($order->user)
                                    <span class="text-dark">{{ $order->user->name }} ({{ $order->user->email }})</span>
                                @else
                                    <span class="text-muted">Khách vãng lai / Đã xóa</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border h-100">
                            <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-truck me-1"></i> Địa chỉ nhận hàng</h6>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Địa chỉ chi tiết:</span>
                                <span class="text-dark fw-medium">{{ $order->specific_address }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Khu vực:</span>
                                <span class="text-dark">{{ $order->ward }}, {{ $order->district }}, {{ $order->province }}</span>
                            </div>
                            @if(!empty($order->note))
                                <div class="mt-2 p-2 bg-white rounded border text-muted small">
                                    <strong>Ghi chú từ khách:</strong> {{ $order->note }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Cột phải: Cập nhật trạng thái & Tóm tắt tài chính -->
    <div class="col-lg-4">
        
        <!-- Form cập nhật trạng thái đơn hàng -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title fw-bold mb-0">
                    <i class="bi bi-pencil-square me-2"></i> Cập nhật Đơn hàng
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Trạng thái đơn hàng <span class="text-danger">*</span></label>
                        <select name="order_status" class="form-select form-select-lg">
                            <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>⏳ Chờ xác nhận</option>
                            <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>⚙️ Đang xử lý</option>
                            <option value="packed" {{ $order->order_status === 'packed' ? 'selected' : '' }}>📦 Đã đóng gói</option>
                            <option value="shipping" {{ $order->order_status === 'shipping' ? 'selected' : '' }}>🚚 Đang vận chuyển</option>
                            <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>✅ Đã giao</option>
                            <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>❌ Đã hủy</option>
                            <option value="returned" {{ $order->order_status === 'returned' ? 'selected' : '' }}>🔄 Hoàn trả</option>
                        </select>
                        <div class="form-text text-muted">
                            <i class="bi bi-info-circle me-1"></i> Trạng thái <strong>Đang giao hàng</strong> được hệ thống tự cập nhật, không cho admin chỉnh tay.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Trạng thái thanh toán</label>
                        <select name="payment_status" class="form-select">
                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Thanh toán thất bại</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" onclick="return confirm('Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng này?')">
                        <i class="bi bi-check2-circle me-1"></i> Cập nhật trạng thái
                    </button>
                </form>
            </div>
        </div>

        <!-- Tóm tắt tài chính -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-receipt text-primary me-2"></i> Tóm tắt tài chính
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tạm tính tiền hàng:</span>
                    <span class="fw-semibold text-dark">{{ number_format($itemsSubtotal, 0, ',', '.') }} ₫</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Phí vận chuyển:</span>
                    <span class="fw-semibold text-dark">{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold text-dark fs-5">Tổng thanh toán:</span>
                    <strong class="text-danger fs-4">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</strong>
                </div>

                <div class="p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Phương thức:</span>
                        <strong class="text-dark small">
                            @if($order->payment_method === 'cod')
                                COD (Thanh toán khi nhận hàng)
                            @elseif($order->payment_method === 'momo')
                                Ví điện tử MoMo
                            @else
                                {{ strtoupper($order->payment_method) }}
                            @endif
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Trạng thái TT:</span>
                        @if($order->payment_status === 'paid')
                            <span class="badge bg-success">Đã thanh toán</span>
                        @elseif($order->payment_status === 'failed')
                            <span class="badge bg-danger">Thất bại</span>
                        @else
                            <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
