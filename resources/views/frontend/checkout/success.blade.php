@extends('frontend.layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <!-- Hero Card: Success Header -->
            <div class="card shadow-sm border-0 mb-4 text-center overflow-hidden">
                <div class="card-body p-4 p-md-5 bg-white">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success" style="width: 80px; height: 80px;">
                            <i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-dark mb-2">Đặt Hàng Thành Công!</h2>
                    <p class="text-muted fs-6 mb-4">
                        Cảm ơn bạn đã tin tưởng lựa chọn mua sắm tại <strong>SoleVibe - Sneaker & Footwear</strong>.<br>
                        Đơn hàng của bạn đã được tiếp nhận và đang được đóng gói cẩn thận bằng hộp Double-Box chuẩn form.
                    </p>

                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        @if($order->payment_method === 'momo' && $order->payment_status !== 'paid')
                            <a href="{{ route('payment.momo', $order->id) }}" class="btn btn-danger px-4 py-2 fw-bold shadow-sm rounded-pill">
                                <i class="bi bi-qr-code-scan me-1"></i> Thanh toán ngay qua MoMo
                            </a>
                        @endif
                        <a href="{{ route('profile.index') }}#orders" class="btn btn-outline-danger px-4 py-2 fw-semibold shadow-sm rounded-pill">
                            <i class="bi bi-bag-check me-1"></i> Xem đơn hàng trong tài khoản
                        </a>
                        <a href="{{ route('frontend.products.index') }}" class="btn btn-outline-dark px-4 py-2 fw-semibold rounded-pill">
                            <i class="bi bi-shop me-1"></i> Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order Details Grid -->
            <div class="row g-4 mb-4">
                <!-- Info 1: Order Meta & Status -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-receipt text-primary me-2"></i>Thông tin đơn hàng
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Mã đơn hàng:</span>
                                    <strong class="text-primary font-monospace fs-6">{{ $order->order_code }}</strong>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Thời gian đặt:</span>
                                    <span class="fw-medium text-dark">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Phương thức thanh toán:</span>
                                    <span class="fw-medium text-dark">
                                        @if($order->payment_method === 'cod')
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis border">
                                                <i class="bi bi-truck me-1"></i>COD (Khi nhận hàng)
                                            </span>
                                        @elseif($order->payment_method === 'momo')
                                            <span class="badge bg-danger-subtle text-danger-emphasis border">
                                                <i class="bi bi-qr-code-scan me-1"></i>Ví MoMo
                                            </span>
                                        @else
                                            {{ strtoupper($order->payment_method) }}
                                        @endif
                                    </span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Trạng thái thanh toán:</span>
                                    <span>
                                        @if($order->payment_status === 'paid')
                                            <span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i>Đã thanh toán</span>
                                        @elseif($order->payment_status === 'failed')
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Thất bại</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Chờ thanh toán</span>
                                        @endif
                                    </span>
                                </li>
                                <li class="d-flex justify-content-between pt-2">
                                    <span class="text-muted">Trạng thái đơn hàng:</span>
                                    <span>
                                        @if($order->order_status === 'pending')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Chờ xác nhận</span>
                                        @elseif($order->order_status === 'processing')
                                            <span class="badge bg-info text-dark"><i class="bi bi-gear me-1"></i>Đang xử lý</span>
                                        @elseif($order->order_status === 'shipping')
                                            <span class="badge bg-primary"><i class="bi bi-truck me-1"></i>Đang giao hàng</span>
                                        @elseif($order->order_status === 'completed')
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Hoàn thành</span>
                                        @elseif($order->order_status === 'cancelled')
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Đã hủy</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $order->order_status }}</span>
                                        @endif
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Info 2: Shipping Destination -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-geo-alt-fill text-danger me-2"></i>Địa chỉ nhận hàng
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <strong class="text-dark fs-6">{{ $order->receiver_name }}</strong>
                                <span class="text-muted small ms-2"><i class="bi bi-telephone me-1"></i>{{ $order->receiver_phone }}</span>
                            </div>
                            <p class="text-secondary small mb-3">
                                <i class="bi bi-pin-map me-1 text-danger"></i>
                                {{ $order->specific_address }}, {{ $order->ward }}, {{ $order->district }}, {{ $order->province }}
                            </p>

                            @if(!empty($order->note))
                                <div class="p-2 bg-light rounded border text-muted small">
                                    <strong><i class="bi bi-chat-left-text me-1"></i>Ghi chú:</strong> {{ $order->note }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchased Products List -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-bag-check-fill text-success me-2"></i>Chi tiết sản phẩm đã mua ({{ $order->items->count() }} mặt hàng)
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-4" style="min-width: 250px;">Sản phẩm</th>
                                    <th scope="col" class="text-center" style="width: 120px;">Đơn giá</th>
                                    <th scope="col" class="text-center" style="width: 100px;">Số lượng</th>
                                    <th scope="col" class="text-end pe-4" style="width: 150px;">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    @php
                                        $thumbnail = $item->product?->thumbnail ?? '';
                                        if (!empty($thumbnail)) {
                                            $imgUrl = str_starts_with($thumbnail, 'http') ? $thumbnail : asset('storage/' . $thumbnail);
                                        } else {
                                            $imgUrl = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="%23ccc" viewBox="0 0 16 16"><rect width="100%" height="100%" fill="%23f8f9fa"/><text x="50%" y="50%" fill="%23999" dominant-baseline="middle" text-anchor="middle" font-size="9">No Image</text></svg>';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $imgUrl }}" alt="{{ $item->product_name }}" class="rounded me-3 border" style="width: 55px; height: 55px; object-fit: cover;">
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                                                    @if(!empty($item->variation_label))
                                                        <span class="badge bg-light text-secondary border small mt-1">
                                                            <i class="bi bi-tag me-1"></i>{{ $item->variation_label }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ number_format($item->price, 0, ',', '.') }} ₫</td>
                                        <td class="text-center fw-semibold">x{{ $item->quantity }}</td>
                                        <td class="text-end pe-4 fw-bold text-danger">{{ number_format($item->subtotal, 0, ',', '.') }} ₫</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-semibold py-2">Tạm tính:</td>
                                    <td class="text-end pe-4 py-2">{{ number_format($order->total_amount - ($order->shipping_fee ?? 0), 0, ',', '.') }} ₫</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-semibold py-2">Phí vận chuyển:</td>
                                    <td class="text-end pe-4 py-2 text-success fw-bold">
                                        @if(($order->shipping_fee ?? 0) > 0)
                                            {{ number_format($order->shipping_fee, 0, ',', '.') }} ₫
                                        @else
                                            Miễn phí
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold fs-5 text-dark py-3">Tổng thanh toán:</td>
                                    <td class="text-end pe-4 fw-bold fs-4 text-danger py-3">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer Support Contact -->
            <div class="p-3 bg-light rounded text-center text-muted small mb-5">
                <i class="bi bi-info-circle me-1"></i> Nếu bạn cần hỗ trợ hoặc có thắc mắc về đơn hàng, vui lòng liên hệ hotline: <strong class="text-dark">1900 6868</strong> hoặc email: <strong class="text-dark">support@solevibe.vn</strong>.
            </div>
        </div>
    </div>
</div>
@endsection