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
    <!-- Đổi Form này trỏ sang Checkout -->
    <form action="/checkout" method="POST" id="cart-form">
        @csrf
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="cart-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3" style="width: 40px;">
                                            <input class="form-check-input" type="checkbox" id="check-all">
                                        </th>
                                        <th>Sản phẩm</th>
                                        <th>Đơn giá</th>
                                        <th style="width: 150px;">Số lượng</th>
                                        <th>Thành tiền</th>
                                        <th class="text-center">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $cartKey => $item)
                                    @php
                                        $price = $item['sale_price'] ?? $item['price'];
                                    @endphp
                                    <tr class="cart-item-row" data-key="{{ $cartKey }}">
                                        <td class="ps-3">
                                            <!-- Checkbox mặc định trống -->
                                            <input class="form-check-input item-checkbox" type="checkbox" name="selected_items[]" value="{{ $cartKey }}" data-price="{{ $price }}">
                                        </td>
                                        <td class="d-flex align-items-center gap-3">
                                            @php
                                                $imgUrl = str_starts_with($item['thumbnail'], 'http') ? $item['thumbnail'] : asset('storage/' . $item['thumbnail']);
                                            @endphp
                                            <img src="{{ $imgUrl }}" class="rounded shadow-sm" style="width: 60px; height: 60px; object-fit: cover;" alt="{{ $item['name'] }}">
                                            <div>
                                                <a href="{{ route('frontend.products.show', $item['slug']) }}" class="text-decoration-none text-dark fw-bold d-block">{{ $item['name'] }}</a>
                                                @if(!empty($item['variation_label']))
                                                    <small class="text-muted border rounded px-2 py-1 bg-light d-inline-block mt-1">
                                                        Phân loại: <strong>{{ $item['variation_label'] }}</strong>
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold {{ isset($item['sale_price']) ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($price, 0, ',', '.') }}đ
                                            </span>
                                        </td>
                                        <td>
                                            <!-- Cụm nút +/- bằng JS (Không dùng form submit nữa) -->
                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                <button class="btn btn-outline-secondary btn-decrease" type="button">-</button>
                                                <input type="number" class="form-control text-center item-quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock_quantity'] }}">
                                                <button class="btn btn-outline-secondary btn-increase" type="button">+</button>
                                            </div>
                                        </td>
                                        <td class="fw-bold text-danger item-subtotal">
                                            {{ number_format($price * $item['quantity'], 0, ',', '.') }}đ
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-remove" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- KHU VỰC NÚT XÓA -->
                <div class="mt-3 d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-delete-selected" disabled>
                        <i class="bi bi-trash"></i> Xóa mục đã chọn
                    </button>
                    
                    <button type="button" class="btn btn-outline-danger btn-sm" id="btn-clear-cart">
                        <i class="bi bi-trash"></i> Xóa toàn bộ giỏ hàng
                    </button>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 border-top border-success border-4 sticky-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Tóm tắt thanh toán</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Sản phẩm đã chọn:</span>
                            <span class="fw-bold" id="summary-count">0</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4 align-items-center">
                            <span class="fw-bold fs-5">TỔNG CỘNG:</span>
                            <span class="fw-bold fs-3 text-danger" id="summary-total">0đ</span>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm" id="btn-checkout" disabled>
                            MUA HÀNG
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endif

<!-- CSRF Token cho Fetch API -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const checkAllBtn = document.getElementById('check-all');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const summaryTotal = document.getElementById('summary-total');
    const summaryCount = document.getElementById('summary-count');
    const btnCheckout = document.getElementById('btn-checkout');
    const btnDeleteSelected = document.getElementById('btn-delete-selected');

    // Hàm Format Tiền
    const formatMoney = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + 'đ';

    // 1. Logic Checkbox (Tính tổng tiền linh động)
    function calculateTotal() {
        let total = 0;
        let count = 0;
        let allChecked = true;
        let checkedCount = 0;

        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                const row = cb.closest('.cart-item-row');
                const price = parseFloat(cb.getAttribute('data-price'));
                const qty = parseInt(row.querySelector('.item-quantity').value);
                total += price * qty;
                count++;
                checkedCount++;
            } else {
                allChecked = false;
            }
        });

        summaryTotal.innerText = formatMoney(total);
        summaryCount.innerText = count;
        btnCheckout.disabled = (count === 0);
        
        if(checkAllBtn) checkAllBtn.checked = (itemCheckboxes.length > 0 && allChecked);
        if(btnDeleteSelected) btnDeleteSelected.disabled = (checkedCount === 0);
    }

    if(checkAllBtn) {
        checkAllBtn.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => cb.checked = this.checked);
            calculateTotal();
        });
    }

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', calculateTotal);
    });

    // 2. Logic Nút Tăng/Giảm Số Lượng (AJAX)
    document.querySelectorAll('.btn-decrease, .btn-increase').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('.cart-item-row');
            const input = row.querySelector('.item-quantity');
            const cartKey = row.getAttribute('data-key');
            let currentQty = parseInt(input.value);
            const maxQty = parseInt(input.getAttribute('max'));

            if (this.classList.contains('btn-decrease') && currentQty > 1) {
                currentQty--;
            } else if (this.classList.contains('btn-increase') && currentQty < maxQty) {
                currentQty++;
            } else {
                return; // Khỏi gọi API nếu ko đổi
            }

            input.value = currentQty;
            const price = parseFloat(row.querySelector('.item-checkbox').getAttribute('data-price'));
            row.querySelector('.item-subtotal').innerText = formatMoney(price * currentQty);
            calculateTotal();

            fetch(`/gio-hang/update/${cartKey}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: currentQty })
            });
        });
    });

    // 3. Logic Xóa Từng Sản Phẩm (AJAX)
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            if(!confirm('Bạn có muốn bỏ sản phẩm này khỏi giỏ?')) return;
            
            const row = this.closest('.cart-item-row');
            const cartKey = row.getAttribute('data-key');

            fetch(`/gio-hang/remove/${cartKey}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            }).then(response => response.json())
              .then(data => {
                  if(data.success) {
                      row.remove(); 
                      calculateTotal();
                      if(document.querySelectorAll('.cart-item-row').length === 0) location.reload();
                  }
              });
        });
    });

    // 4. Logic Xóa Nhiều Mục Đã Chọn (AJAX)
    if(btnDeleteSelected) {
        btnDeleteSelected.addEventListener('click', function() {
            if(!confirm('Xóa các sản phẩm đã chọn khỏi giỏ?')) return;

            const selectedKeys = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);

            fetch(`/gio-hang/remove-multiple`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ keys: selectedKeys })
            }).then(response => response.json())
              .then(data => {
                  if(data.success) {
                      document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                          cb.closest('.cart-item-row').remove();
                      });
                      calculateTotal();
                      if(document.querySelectorAll('.cart-item-row').length === 0) location.reload();
                  } else {
                      alert(data.message || 'Có lỗi xảy ra khi xóa sản phẩm');
                  }
              })
              .catch(error => {
                  console.error('Error removing selected items:', error);
                  alert('Có lỗi xảy ra trong quá trình xử lý!');
              });
        });
    }

    // 5. Logic Xóa Toàn Bộ
    const btnClearCart = document.getElementById('btn-clear-cart');
    if(btnClearCart) {
        btnClearCart.addEventListener('click', function() {
            if(!confirm('Xóa toàn bộ giỏ hàng?')) return;
            
            fetch(`/gio-hang/clear`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            }).then(() => {
                location.reload();
            });
        });
    }
});
</script>
@endsection