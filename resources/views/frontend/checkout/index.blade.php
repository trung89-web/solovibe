@extends('frontend.layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-shield-check text-success me-2"></i>Thanh Toán Đơn Hàng</h2>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
        @csrf
        <div class="row">
            <!-- Left Column: Address and Payment Details -->
            <div class="col-lg-7 mb-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4 border-bottom pb-3">
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>Thông tin giao hàng
                        </h5>

                        {{-- Chọn địa chỉ đã lưu --}}
                        @if(isset($addresses) && $addresses->count() > 0)
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2 text-dark">
                                    <i class="bi bi-journal-bookmark me-1 text-primary"></i> Sổ địa chỉ của bạn:
                                </label>
                                <div class="list-group mb-3">
                                    @foreach($addresses as $index => $addr)
                                        <label class="list-group-item list-group-item-action d-flex align-items-start p-3 cursor-pointer address-card mb-2 rounded border {{ $addr->is_default || $index === 0 ? 'border-primary bg-light' : '' }}">
                                            <input class="form-check-input me-3 mt-1 address-radio" 
                                                   type="radio" 
                                                   name="selected_address_id" 
                                                   value="{{ $addr->id }}"
                                                   data-name="{{ $addr->receiver_name }}"
                                                   data-phone="{{ $addr->receiver_phone }}"
                                                   data-province="{{ $addr->province }}"
                                                   data-district="{{ $addr->district }}"
                                                   data-ward="{{ $addr->ward }}"
                                                   data-specific="{{ $addr->specific_address }}"
                                                   {{ $addr->is_default || $index === 0 ? 'checked' : '' }}>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <strong class="text-dark fs-6">{{ $addr->receiver_name }}</strong>
                                                    <span class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $addr->receiver_phone }}</span>
                                                </div>
                                                <div class="text-secondary small mt-1">
                                                    {{ $addr->specific_address }}, {{ $addr->ward }}, {{ $addr->district }}, {{ $addr->province }}
                                                </div>
                                                @if($addr->is_default)
                                                    <span class="badge bg-primary mt-2">Địa chỉ mặc định</span>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach

                                    <label class="list-group-item list-group-item-action d-flex align-items-center p-3 cursor-pointer address-card rounded border">
                                        <input class="form-check-input me-3 address-radio" 
                                               type="radio" 
                                               name="selected_address_id" 
                                               value="new" 
                                               id="radio-new-address"
                                               {{ $addresses->isEmpty() ? 'checked' : '' }}>
                                        <div>
                                            <strong class="text-primary"><i class="bi bi-plus-circle-fill me-1"></i> Nhập thông tin / địa chỉ nhận hàng mới</strong>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @endif

                        {{-- Form nhập chi tiết địa chỉ --}}
                        <div id="address-details-wrapper" class="p-3 bg-light rounded border">
                            <h6 class="fw-bold mb-3 text-secondary" id="address-form-title">
                                @if(isset($addresses) && $addresses->count() > 0)
                                    Thông tin chi tiết người nhận
                                @else
                                    Nhập thông tin giao hàng
                                @endif
                            </h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Họ và tên người nhận <span class="text-danger">*</span></label>
                                <input type="text" name="receiver_name" id="receiver_name" class="form-control" required value="{{ old('receiver_name', auth()->user()?->name) }}" placeholder="Ví dụ: Nguyễn Văn A">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="receiver_phone" id="receiver_phone" class="form-control" required value="{{ old('receiver_phone', auth()->user()?->phone) }}" placeholder="Ví dụ: 0987654321">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <label class="form-label fw-bold">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                                    <select id="province" name="province" class="form-select province-select" required>
                                        <option value="" disabled selected>Chọn Tỉnh/Thành</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <label class="form-label fw-bold">Quận / Huyện <span class="text-danger">*</span></label>
                                    <select id="district" name="district" class="form-select district-select" required>
                                        <option value="" disabled selected>Chọn Quận/Huyện</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Phường / Xã <span class="text-danger">*</span></label>
                                    <select id="ward" name="ward" class="form-select ward-select" required>
                                        <option value="" disabled selected>Chọn Phường/Xã</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                                <input type="text" name="specific_address" id="specific_address" class="form-control" required placeholder="Số nhà, tên đường, tòa nhà..." value="{{ old('specific_address') }}">
                            </div>

                            <div class="mb-3" id="save-address-check-container" style="display: none;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="save_address" value="1" id="save_address">
                                    <label class="form-check-label text-muted small cursor-pointer" for="save_address">
                                        Lưu địa chỉ này vào <strong>Sổ địa chỉ</strong> của tôi cho lần mua hàng sau
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 mt-4">
                            <label class="form-label fw-bold">Ghi chú đơn hàng (Tùy chọn)</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Giao hàng vào giờ hành chính, gọi trước khi giao..."></textarea>
                        </div>

                        <h5 class="card-title mt-5 mb-3 border-bottom pb-3">
                            <i class="bi bi-wallet2 text-success me-2"></i>Phương thức thanh toán
                        </h5>
                        <div class="form-check mb-3 p-3 border rounded">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" checked>
                            <label class="form-check-label fw-bold d-flex align-items-center cursor-pointer" for="payment_cod">
                                <i class="bi bi-truck fs-4 text-primary me-2"></i>
                                <span>Thanh toán khi nhận hàng (COD)</span>
                            </label>
                        </div>
                        <div class="form-check p-3 border rounded">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_momo" value="momo">
                            <label class="form-check-label fw-bold d-flex align-items-center cursor-pointer" for="payment_momo">
                                <i class="bi bi-qr-code-scan fs-4 text-danger me-2"></i>
                                <span>Thanh toán qua ví MoMo</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4 border-bottom pb-3">
                            <i class="bi bi-basket-fill text-success me-2"></i>Đơn hàng của bạn
                        </h5>
                        
                        <div class="mb-3 border-bottom pb-3" style="max-height: 380px; overflow-y: auto;">
                            @foreach($selected_items as $item)
                                @php
                                    $price = $item['sale_price'] ?? $item['price'];
                                    $subtotal = $price * $item['quantity'];
                                    $thumbnail = $item['thumbnail'] ?? '';
                                    if (!empty($thumbnail)) {
                                        $imgUrl = str_starts_with($thumbnail, 'http') ? $thumbnail : asset('storage/' . $thumbnail);
                                    } else {
                                        $imgUrl = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="65" height="65" fill="%23ccc" viewBox="0 0 16 16"><rect width="100%" height="100%" fill="%23f8f9fa"/><text x="50%" y="50%" fill="%23999" dominant-baseline="middle" text-anchor="middle" font-size="10">No Image</text></svg>';
                                    }
                                @endphp
                                <div class="d-flex mb-3 align-items-center">
                                    <img src="{{ $imgUrl }}" alt="{{ $item['name'] }}" class="rounded me-3 shadow-sm" style="width: 65px; height: 65px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 text-dark fw-bold" style="font-size: 0.95rem;">{{ $item['name'] }}</h6>
                                        @if(!empty($item['variation_label']))
                                            <div class="text-muted small">Phân loại: <span class="fw-semibold">{{ $item['variation_label'] }}</span></div>
                                        @endif
                                        <div class="d-flex justify-content-between mt-1 small">
                                            <span class="text-muted">SL: x{{ $item['quantity'] }}</span>
                                            <strong class="text-danger">{{ number_format($subtotal, 0, ',', '.') }} ₫</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Tạm tính:</span>
                            <span>{{ number_format($total, 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted">
                            <span>Phí vận chuyển:</span>
                            <span class="text-secondary fw-semibold" id="shipping-fee-display">Chưa tính</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4 fs-5">
                            <strong>TỔNG CỘNG:</strong>
                            <strong class="text-danger fs-4" id="order-total-display">{{ number_format($total, 0, ',', '.') }} ₫</strong>
                        </div>
                        
                        <input type="hidden" name="shipping_fee" id="shipping_fee_input" value="0">
                        
                        <button type="submit" id="btn-submit-order" class="btn btn-danger btn-lg w-100 py-3 fw-bold shadow-sm" disabled>
                            <i class="bi bi-check-circle-fill me-2"></i><span id="btn-submit-text">XÁC NHẬN ĐẶT HÀNG</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    const receiverNameInput = document.getElementById('receiver_name');
    const receiverPhoneInput = document.getElementById('receiver_phone');
    const specificAddressInput = document.getElementById('specific_address');
    const addressRadios = document.querySelectorAll('.address-radio');
    const saveAddressContainer = document.getElementById('save-address-check-container');
    const addressFormTitle = document.getElementById('address-form-title');

    let locationData = [];

    function populateDistricts(selectedProvinceName, targetDistrict = null, targetWard = null) {
        districtSelect.innerHTML = '<option value="" disabled selected>Chọn Quận/Huyện</option>';
        wardSelect.innerHTML = '<option value="" disabled selected>Chọn Phường/Xã</option>';

        const province = locationData.find(item => item.Name === selectedProvinceName || item.Id === selectedProvinceName);
        if (!province || !province.Districts) return;

        province.Districts.forEach(dist => {
            const opt = document.createElement('option');
            opt.value = dist.Name;
            opt.dataset.id = dist.Id;
            opt.textContent = dist.Name;
            if (targetDistrict && (dist.Name === targetDistrict || dist.Id === targetDistrict)) {
                opt.selected = true;
            }
            districtSelect.appendChild(opt);
        });

        if (targetDistrict) {
            populateWards(selectedProvinceName, targetDistrict, targetWard);
        }
    }

    function populateWards(selectedProvinceName, selectedDistrictName, targetWard = null) {
        wardSelect.innerHTML = '<option value="" disabled selected>Chọn Phường/Xã</option>';

        const province = locationData.find(item => item.Name === selectedProvinceName || item.Id === selectedProvinceName);
        if (!province || !province.Districts) return;

        const district = province.Districts.find(item => item.Name === selectedDistrictName || item.Id === selectedDistrictName);
        if (!district || !district.Wards) return;

        district.Wards.forEach(w => {
            const opt = document.createElement('option');
            opt.value = w.Name;
            opt.dataset.id = w.Id;
            opt.textContent = w.Name;
            if (targetWard && (w.Name === targetWard || w.Id === targetWard)) {
                opt.selected = true;
            }
            wardSelect.appendChild(opt);
        });
    }

    fetch('https://cdn.jsdelivr.net/gh/kenzouno1/DiaGioiHanhChinhVN@master/data.json')
        .then(res => res.json())
        .then(data => {
            locationData = data;

            provinceSelect.innerHTML = '<option value="" disabled selected>Chọn Tỉnh/Thành</option>';
            data.forEach(prov => {
                const opt = document.createElement('option');
                opt.value = prov.Name;
                opt.dataset.id = prov.Id;
                opt.textContent = prov.Name;
                provinceSelect.appendChild(opt);
            });

            provinceSelect.addEventListener('change', function () {
                populateDistricts(this.value);
                const submitBtn = document.getElementById('btn-submit-order');
                if (submitBtn) submitBtn.disabled = true;
                const shippingDisplay = document.getElementById('shipping-fee-display');
                if (shippingDisplay) {
                    shippingDisplay.textContent = 'Chưa tính';
                    shippingDisplay.className = 'text-secondary fw-semibold';
                }
            });

            districtSelect.addEventListener('change', function () {
                populateWards(provinceSelect.value, this.value);
                calculateShipping();
            });
            
            wardSelect.addEventListener('change', function () {
                calculateShipping();
            });

            const checkedRadio = document.querySelector('.address-radio:checked');
            if (checkedRadio) {
                applyAddressRadio(checkedRadio);
            }
        })
        .catch(err => console.error('Error fetching administrative data:', err));

    function calculateShipping() {
        const province = provinceSelect.value;
        const district = districtSelect.value;
        const ward = wardSelect.value;
        const subtotal = {{ $total }};
        const submitBtn = document.getElementById('btn-submit-order');
        
        if (!province || !district || !ward) {
            if (submitBtn) submitBtn.disabled = true;
            return;
        }

        const shippingDisplay = document.getElementById('shipping-fee-display');
        const totalDisplay = document.getElementById('order-total-display');
        const shippingInput = document.getElementById('shipping_fee_input');

        // Disable nút Submit và đổi text trước khi gọi AJAX/fetch
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Đang tính phí...';
        }

        shippingDisplay.innerHTML = '<span class="spinner-border spinner-border-sm text-primary" role="status"></span> Đang tính phí...';
        
        fetch('{{ route('checkout.calculateShipping') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                province_name: province,
                district_name: district,
                ward_name: ward,
                subtotal: subtotal
            })
        })
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                shippingDisplay.textContent = data.formatted_shipping_fee;
                shippingDisplay.classList.remove('text-secondary', 'text-success');
                shippingDisplay.classList.add('text-danger');
                
                totalDisplay.textContent = data.formatted_total;
                shippingInput.value = data.shipping_fee;

                // Gỡ thuộc tính disabled và phục hồi text
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>XÁC NHẬN ĐẶT HÀNG';
                }
            } else {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>XÁC NHẬN ĐẶT HÀNG';
                }
                alert('Không thể tính phí vận chuyển, vui lòng thử lại');
            }
        })
        .catch(err => {
            console.error('Lỗi tính phí ship', err);
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>XÁC NHẬN ĐẶT HÀNG';
            }
            shippingDisplay.textContent = 'Lỗi tính phí';
            shippingDisplay.className = 'text-danger fw-semibold';
            alert('Không thể tính phí vận chuyển, vui lòng thử lại');
        });
    }

    function applyAddressRadio(radio) {
        if (!radio) return;

        if (radio.value === 'new') {
            if (saveAddressContainer) saveAddressContainer.style.display = 'block';
            if (addressFormTitle) addressFormTitle.innerText = 'Nhập thông tin giao hàng mới';

            receiverNameInput.value = '';
            receiverPhoneInput.value = '';
            specificAddressInput.value = '';

            provinceSelect.value = '';
            districtSelect.innerHTML = '<option value="" disabled selected>Chọn Quận/Huyện</option>';
            wardSelect.innerHTML = '<option value="" disabled selected>Chọn Phường/Xã</option>';

            const submitBtn = document.getElementById('btn-submit-order');
            if (submitBtn) submitBtn.disabled = true;

            const shippingDisplay = document.getElementById('shipping-fee-display');
            if (shippingDisplay) {
                shippingDisplay.textContent = 'Chưa tính';
                shippingDisplay.className = 'text-secondary fw-semibold';
            }
            const shippingInput = document.getElementById('shipping_fee_input');
            if (shippingInput) shippingInput.value = '0';
        } else {
            if (saveAddressContainer) saveAddressContainer.style.display = 'none';
            if (addressFormTitle) addressFormTitle.innerText = 'Thông tin chi tiết người nhận (Từ sổ địa chỉ)';

            receiverNameInput.value = radio.dataset.name || '';
            receiverPhoneInput.value = radio.dataset.phone || '';
            specificAddressInput.value = radio.dataset.specific || '';

            const p = radio.dataset.province;
            const d = radio.dataset.district;
            const w = radio.dataset.ward;

            if (p) {
                provinceSelect.value = p;
                populateDistricts(p, d, w);
                if (d && w) {
                    calculateShipping();
                }
            }
        }

        document.querySelectorAll('.address-card').forEach(card => card.classList.remove('border-primary', 'bg-light'));
        const parentCard = radio.closest('.address-card');
        if (parentCard) parentCard.classList.add('border-primary', 'bg-light');
    }

    addressRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            applyAddressRadio(this);
        });
    });
});
</script>

