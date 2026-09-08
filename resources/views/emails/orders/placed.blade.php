<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng #{{ $order->order_code }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px 0;
        }
        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
        }
        .email-header {
            background-color: #198754;
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }
        .email-body {
            padding: 24px;
        }
        .order-info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #198754;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .order-info-box p {
            margin: 4px 0;
            font-size: 14px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th {
            background-color: #f1f3f5;
            color: #495057;
            text-align: left;
            padding: 10px 12px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .total-summary {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .total-summary td {
            padding: 6px 12px;
            font-size: 14px;
        }
        .total-summary .grand-total {
            font-size: 17px;
            font-weight: bold;
            color: #198754;
            border-top: 1px dashed #dee2e6;
            padding-top: 10px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 4px;
        }
        .badge-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .badge-cod {
            background-color: #cfe2ff;
            color: #084298;
        }
        .badge-momo {
            background-color: #f8d7da;
            color: #a30052;
        }
        .email-footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 16px 20px;
            font-size: 13px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        .note-box {
            font-style: italic;
            color: #6c757d;
            font-size: 13px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>CẢM ƠN BẠN ĐÃ ĐẶT HÀNG!</h1>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Đơn hàng của bạn đã được tiếp nhận và đang được xử lý.</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Thông tin đơn hàng & Người nhận -->
            <div class="order-info-box">
                <p><strong>Mã đơn hàng:</strong> <span style="color: #198754; font-weight: bold;">#{{ $order->order_code }}</span></p>
                <p><strong>Ngày đặt hàng:</strong> {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</p>
                <p><strong>Trạng thái đơn hàng:</strong> <span class="badge badge-pending">Chờ xác nhận</span></p>
                <p><strong>Phương thức thanh toán:</strong> 
                    <span class="badge {{ $order->payment_method === 'momo' ? 'badge-momo' : 'badge-cod' }}">
                        {{ strtoupper($order->payment_method) }} ({{ $order->payment_method === 'momo' ? 'Ví MoMo' : 'Thanh toán khi nhận hàng (COD)' }})
                    </span>
                </p>
            </div>

            <div style="margin-bottom: 20px;">
                <h3 style="margin-bottom: 8px; font-size: 15px; color: #198754; border-bottom: 1px solid #e9ecef; padding-bottom: 6px;">THÔNG TIN GIAO HÀNG</h3>
                <p style="margin: 4px 0; font-size: 14px;"><strong>Người nhận:</strong> {{ $order->receiver_name }}</p>
                <p style="margin: 4px 0; font-size: 14px;"><strong>Số điện thoại:</strong> {{ $order->receiver_phone }}</p>
                <p style="margin: 4px 0; font-size: 14px;"><strong>Địa chỉ:</strong> {{ $order->specific_address }}, {{ $order->ward }}, {{ $order->district }}, {{ $order->province }}</p>
                @if($order->note)
                    <p class="note-box"><strong>Ghi chú:</strong> {{ $order->note }}</p>
                @endif
            </div>

            <!-- Danh sách sản phẩm -->
            <h3 style="margin-bottom: 10px; font-size: 15px; color: #198754; border-bottom: 1px solid #e9ecef; padding-bottom: 6px;">DANH SÁCH CÂY GIỐNG ĐÃ ĐẶT</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Cây giống</th>
                        <th class="text-center" style="width: 15%;">SL</th>
                        <th class="text-right" style="width: 25%;">Đơn giá</th>
                        <th class="text-right" style="width: 25%;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product_name }}</strong>
                                @if($item->variation_label)
                                    <div style="font-size: 12px; color: #6c757d; margin-top: 2px;">
                                        Phân loại: {{ $item->variation_label }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                            <td class="text-right" style="font-weight: 500;">{{ number_format($item->subtotal, 0, ',', '.') }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Bảng tổng tiền -->
            <table class="total-summary">
                <tr>
                    <td class="text-right" style="color: #6c757d;">Tạm tính:</td>
                    <td class="text-right" style="width: 30%;">{{ number_format($order->total_amount - $order->shipping_fee, 0, ',', '.') }} đ</td>
                </tr>
                <tr>
                    <td class="text-right" style="color: #6c757d;">Phí vận chuyển:</td>
                    <td class="text-right">{{ number_format($order->shipping_fee, 0, ',', '.') }} đ</td>
                </tr>
                <tr class="grand-total">
                    <td class="text-right">Tổng thanh toán:</td>
                    <td class="text-right">{{ number_format($order->total_amount, 0, ',', '.') }} đ</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p style="margin: 0 0 5px 0;">Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ hotline hỗ trợ hoặc phản hồi trực tiếp email này.</p>
            <p style="margin: 0; font-weight: bold; color: #198754;">Fruit Tree - Cửa hàng cây giống & nông sản chất lượng cao</p>
        </div>
    </div>
</body>
</html>
