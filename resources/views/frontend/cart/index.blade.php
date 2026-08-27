@extends('frontend.layouts.app')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-success"><i class="bi bi-cart-check"></i> GIỎ HÀNG CỦA BẠN</h3>
</div>

@if(empty($cart))
    <div class="text-center py-5 bg-white shadow-sm rounded">
        <i class="bi bi-cart-x text-muted" style="font-size: 5rem;"></i>
        <h5 class="mt-3 text-muted">Giỏ hàng của bạn đang trống!</h5>
        <a href="{{ route('frontend.products.index') }}" class="btn btn-success mt-3 shadow-sm">Tiếp tục mua sắm</a>
    </div>
@else
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Sản phẩm</th>
                                    <th>Đơn giá</th>
                                    <th style="width: 130px;">Số lượng</th>
                                    <th>Thành tiền</th>
                                    <th class="text-center">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $cartKey => $item)
                                <tr>
                                    <td class="ps-3 d-flex align-items-center gap-3">
                                        @php
                                            $imgUrl = str_starts_with($item['thumbnail'], 'http') ? $item['thumbnail'] : asset('storage/' . $item['thumbnail']);
                                        @endphp
                                        <img src="{{ $imgUrl }}" class="rounded shadow-sm" style="width: 70px; height: 70px; object-fit: cover;" alt="{{ $item['name'] }}">
                                        
                                        <div>
                                            <a href="{{ route('frontend.products.show', $item['slug']) }}" class="text-decoration-none text-dark fw-bold d-block">{{ $item['name'] }}</a>
                                            <!-- Hiển thị nhãn Phân loại nếu có -->
                                            @if(!empty($item['variation_label']))
                                                <small class="text-muted border rounded px-2 py-1 bg-light d-inline-block mt-1">
                                                    Phân loại: <strong>{{ $item['variation_label'] }}</strong>
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if(isset($item['sale_price']))
                                            <span class="text-danger fw-bold">{{ number_format($item['sale_price'], 0, ',', '.') }}đ</span>
                                            <br><small class="text-muted text-decoration-line-through">{{ number_format($item['price'], 0, ',', '.') }}đ</small>
                                        @else
                                            <span class="text-success fw-bold">{{ number_format($item['price'], 0, ',', '.') }}đ</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('cart.update', $cartKey) }}" method="POST" class="d-flex align-items-center gap-1 m-0">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" class="form-control form-control-sm text-center" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock_quantity'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                    </td>
                                    <td class="fw-bold">
                                        {{ number_format(($item['sale_price'] ?? $item['price']) * $item['quantity'], 0, ',', '.') }}đ
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('cart.remove', $cartKey) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Bạn có muốn bỏ sản phẩm này khỏi giỏ?');">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <form action="{{ route('cart.clear') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Xóa toàn bộ giỏ hàng?');">
                        <i class="bi bi-trash"></i> Xóa toàn bộ giỏ hàng
                    </button>
                </form>
            </div>
        </div>

        <!-- Khối Tóm tắt (Giữ nguyên của bạn) -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 border-top border-success border-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Tóm tắt đơn hàng</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Tổng phụ:</span>
                        <span class="fw-bold">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">TỔNG CỘNG:</span>
                        <span class="fw-bold fs-4 text-danger">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                    </div>
                    <button class="btn btn-success btn-lg w-100 fw-bold shadow-sm" onclick="alert('Chức năng Thanh toán sẽ được phát triển ở bước sau!');">
                        TIẾN HÀNH THANH TOÁN
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection