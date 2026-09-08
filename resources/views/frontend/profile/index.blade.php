@extends('frontend.layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Cột trái: Nav Menu -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active text-start rounded-0 border-bottom p-3" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="true">
                            <i class="bi bi-person me-2"></i> Hồ sơ của tôi
                        </button>
                        <button class="nav-link text-start rounded-0 border-bottom p-3" id="v-pills-address-tab" data-bs-toggle="pill" data-bs-target="#v-pills-address" type="button" role="tab" aria-controls="v-pills-address" aria-selected="false">
                            <i class="bi bi-geo-alt me-2"></i> Sổ địa chỉ
                        </button>
                        <button class="nav-link text-start rounded-0 p-3" id="v-pills-orders-tab" data-bs-toggle="pill" data-bs-target="#v-pills-orders" type="button" role="tab" aria-controls="v-pills-orders" aria-selected="false">
                            <i class="bi bi-bag me-2"></i> Lịch sử đơn hàng
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Tab Content -->
        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="tab-content" id="v-pills-tabContent">
                        
                        <!-- Tab 1: Hồ sơ -->
                        <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                            <h4 class="mb-4">Hồ sơ của tôi</h4>
                            <form action="{{ url('/profile/update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label">Họ và tên</label>
                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $user->phone ?? '' }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                                </div>
                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                            </form>
                        </div>

                        <!-- Tab 2: Sổ địa chỉ -->
                        <div class="tab-pane fade" id="v-pills-address" role="tabpanel" aria-labelledby="v-pills-address-tab">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0">Sổ địa chỉ</h4>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                    + Thêm địa chỉ mới
                                </button>
                            </div>
                            
                            @if($user->addresses->isEmpty())
                                <p class="text-muted">Bạn chưa có địa chỉ nào.</p>
                            @else
                                <div class="row">
                                    @foreach($user->addresses as $address)
                                        <div class="col-12 mb-3">
                                            <div class="border rounded p-3 {{ $address->is_default ? 'border-primary' : '' }}">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            {{ $address->receiver_name }} 
                                                            @if($address->is_default)
                                                                <span class="badge bg-primary ms-2">Mặc định</span>
                                                            @endif
                                                        </h6>
                                                        <p class="mb-1 text-muted small">{{ $address->receiver_phone }}</p>
                                                        <p class="mb-0 text-muted small">
                                                            {{ $address->specific_address }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->province }}
                                                        </p>
                                                    </div>
                                                    <div class="text-end">
                                                        <button class="btn btn-link btn-sm text-decoration-none" data-bs-toggle="modal" data-bs-target="#editAddressModal{{ $address->id }}">Sửa</button>
                                                        @if(!$address->is_default)
                                                            <form action="{{ url('/profile/address/'.$address->id.'/default') }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-outline-primary btn-sm ms-2">Thiết lập mặc định</button>
                                                            </form>
                                                            <form action="{{ url('/profile/address/'.$address->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger btn-sm ms-2" onclick="return confirm('Bạn có chắc muốn xóa địa chỉ này?')">Xóa</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Edit Address Modal -->
                                        <div class="modal fade" id="editAddressModal{{ $address->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ url('/profile/address/'.$address->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Cập nhật địa chỉ</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Tên người nhận <span class="text-danger">*</span></label>
                                                                <input type="text" name="receiver_name" class="form-control" value="{{ $address->receiver_name }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                                                <input type="text" name="receiver_phone" class="form-control" value="{{ $address->receiver_phone }}" required>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4 mb-2 mb-md-0">
                                                                    <label class="form-label">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                                                                    <select name="province" id="edit_province_{{ $address->id }}" class="form-select edit-province province-select" data-address-id="{{ $address->id }}" data-selected="{{ $address->province }}" required>
                                                                        <option value="" disabled selected>Chọn Tỉnh/Thành</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4 mb-2 mb-md-0">
                                                                    <label class="form-label">Quận / Huyện <span class="text-danger">*</span></label>
                                                                    <select name="district" id="edit_district_{{ $address->id }}" class="form-select edit-district district-select" data-address-id="{{ $address->id }}" data-selected="{{ $address->district }}" required>
                                                                        <option value="" disabled selected>Chọn Quận/Huyện</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label">Phường / Xã <span class="text-danger">*</span></label>
                                                                    <select name="ward" id="edit_ward_{{ $address->id }}" class="form-select edit-ward ward-select" data-address-id="{{ $address->id }}" data-selected="{{ $address->ward }}" required>
                                                                        <option value="" disabled selected>Chọn Phường/Xã</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                                                                <input type="text" name="specific_address" class="form-control" value="{{ $address->specific_address }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                            <button type="submit" class="btn btn-primary">Lưu</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Tab 3: Đơn hàng -->
                        <div class="tab-pane fade" id="v-pills-orders" role="tabpanel" aria-labelledby="v-pills-orders-tab">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0">Lịch sử đơn hàng</h4>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis border px-3 py-2">
                                    {{ $user->orders->count() }} đơn hàng
                                </span>
                            </div>

                            @if($user->orders->isEmpty())
                                <div class="text-center py-5">
                                    <div class="mb-3 text-muted opacity-50">
                                        <i class="bi bi-basket3" style="font-size: 4rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-secondary">Bạn chưa có đơn hàng nào</h5>
                                    <p class="text-muted small mb-4">Hãy khám phá các loại cây giống năng suất cao và đặt mua ngay hôm nay!</p>
                                    <a href="{{ route('frontend.products.index') }}" class="btn btn-primary px-4 shadow-sm">
                                        <i class="bi bi-shop me-1"></i> Khám phá sản phẩm
                                    </a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle border">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-3">Mã đơn hàng</th>
                                                <th>Ngày đặt</th>
                                                <th>Người nhận</th>
                                                <th>Tổng tiền</th>
                                                <th>Thanh toán</th>
                                                <th>Trạng thái</th>
                                                <th class="text-end pe-3">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->orders as $order)
                                                <tr>
                                                    <td class="ps-3">
                                                        <strong class="text-primary font-monospace">{{ $order->order_code }}</strong>
                                                        <div class="text-muted small">{{ $order->items->count() }} sản phẩm</div>
                                                    </td>
                                                    <td>
                                                        <div class="text-dark small">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                                    </td>
                                                    <td>
                                                        <div class="fw-medium text-dark small">{{ $order->receiver_name }}</div>
                                                        <div class="text-muted small">{{ $order->receiver_phone }}</div>
                                                    </td>
                                                    <td>
                                                        <strong class="text-danger small">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</strong>
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
                                                                COD
                                                            @elseif($order->payment_method === 'momo')
                                                                MoMo
                                                            @else
                                                                {{ strtoupper($order->payment_method) }}
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($order->order_status === 'pending')
                                                            <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Chờ xác nhận</span>
                                                        @elseif($order->order_status === 'processing')
                                                            <span class="badge bg-info text-dark"><i class="bi bi-gear me-1"></i>Đang xử lý</span>
                                                        @elseif($order->order_status === 'shipping')
                                                            <span class="badge bg-primary"><i class="bi bi-truck me-1"></i>Đang giao</span>
                                                        @elseif($order->order_status === 'completed')
                                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Hoàn thành</span>
                                                        @elseif($order->order_status === 'cancelled')
                                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Đã hủy</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ $order->order_status }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <button class="btn btn-outline-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#orderModal-{{ $order->id }}">
                                                            <i class="bi bi-eye me-1"></i>Chi tiết
                                                        </button>
                                                        @if($order->order_status === 'pending')
                                                            <form action="{{ route('profile.orders.cancel', $order->id) }}" method="POST" class="d-inline-block mt-1">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')">
                                                                    Hủy
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Modals Chi tiết đơn hàng đặt ngoài table -->
                                @foreach($user->orders as $order)
                                    <div class="modal fade" id="orderModal-{{ $order->id }}" tabindex="-1" aria-labelledby="orderModalLabel-{{ $order->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title fw-bold text-dark mb-0" id="orderModalLabel-{{ $order->id }}">
                                                        Chi tiết đơn hàng #{{ $order->order_code }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <!-- Order Info Bar -->
                                                    <div class="row g-3 mb-4">
                                                        <div class="col-sm-6 col-lg-3">
                                                            <div class="p-3 bg-light rounded border h-100">
                                                                <span class="text-muted small d-block mb-1">Ngày đặt hàng:</span>
                                                                <strong class="text-dark small">{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-lg-3">
                                                            <div class="p-3 bg-light rounded border h-100">
                                                                <span class="text-muted small d-block mb-1">Trạng thái đơn:</span>
                                                                @if($order->order_status === 'pending')
                                                                    <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                                                @elseif($order->order_status === 'processing')
                                                                    <span class="badge bg-info text-dark">Đang xử lý</span>
                                                                @elseif($order->order_status === 'shipping')
                                                                    <span class="badge bg-primary">Đang giao</span>
                                                                @elseif($order->order_status === 'completed')
                                                                    <span class="badge bg-success">Hoàn thành</span>
                                                                @elseif($order->order_status === 'cancelled')
                                                                    <span class="badge bg-danger">Đã hủy</span>
                                                                @else
                                                                    <span class="badge bg-secondary">{{ $order->order_status }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-lg-3">
                                                            <div class="p-3 bg-light rounded border h-100">
                                                                <span class="text-muted small d-block mb-1">Phương thức TT:</span>
                                                                <strong class="text-dark small">
                                                                    @if($order->payment_method === 'cod')
                                                                        COD
                                                                    @elseif($order->payment_method === 'momo')
                                                                        MoMo
                                                                    @else
                                                                        {{ strtoupper($order->payment_method) }}
                                                                    @endif
                                                                </strong>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-lg-3">
                                                            <div class="p-3 bg-light rounded border h-100">
                                                                <span class="text-muted small d-block mb-1">Trạng thái TT:</span>
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

                                                    <!-- Address Block -->
                                                    <div class="card border mb-4">
                                                        <div class="card-header bg-white fw-bold py-2">
                                                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>Địa chỉ nhận hàng
                                                        </div>
                                                        <div class="card-body py-3">
                                                            <p class="mb-1">
                                                                <strong>{{ $order->receiver_name }}</strong> 
                                                                <span class="text-muted ms-2">({{ $order->receiver_phone }})</span>
                                                            </p>
                                                            <p class="mb-0 text-secondary small">
                                                                {{ $order->specific_address }}, {{ $order->ward }}, {{ $order->district }}, {{ $order->province }}
                                                            </p>
                                                            @if(!empty($order->note))
                                                                <div class="mt-2 p-2 bg-light rounded border text-muted small">
                                                                    <strong>Ghi chú:</strong> {{ $order->note }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Items Table -->
                                                    <h6 class="fw-bold mb-3">
                                                        <i class="bi bi-bag-fill text-success me-2"></i>Danh sách sản phẩm ({{ $order->items->count() }})
                                                    </h6>
                                                    <div class="table-responsive border rounded mb-3">
                                                        <table class="table table-hover align-middle mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="ps-3" style="width: 70px;">Ảnh</th>
                                                                    <th>Tên sản phẩm</th>
                                                                    <th>Thuộc tính / Phân loại</th>
                                                                    <th class="text-center">Đơn giá</th>
                                                                    <th class="text-center">Số lượng</th>
                                                                    <th class="text-end pe-3">Thành tiền</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($order->items as $item)
                                                                    @php
                                                                        $thumbnail = $item->product?->thumbnail ?? '';
                                                                        if (!empty($thumbnail)) {
                                                                            $imgUrl = str_starts_with($thumbnail, 'http') ? $thumbnail : asset('storage/' . $thumbnail);
                                                                        } else {
                                                                            $imgUrl = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="%23ccc" viewBox="0 0 16 16"><rect width="100%" height="100%" fill="%23f8f9fa"/><text x="50%" y="50%" fill="%23999" dominant-baseline="middle" text-anchor="middle" font-size="8">No Image</text></svg>';
                                                                        }
                                                                        $variationText = $item->variation_label ?? ($item->variation?->attributeValues ? $item->variation->attributeValues->pluck('value')->implode(', ') : '');
                                                                    @endphp
                                                                    <tr>
                                                                        <td class="ps-3 py-2">
                                                                            <img src="{{ $imgUrl }}" alt="{{ $item->product_name }}" class="rounded border" style="width: 48px; height: 48px; object-fit: cover;">
                                                                        </td>
                                                                        <td>
                                                                            <div class="fw-semibold text-dark small">{{ $item->product_name }}</div>
                                                                        </td>
                                                                        <td>
                                                                            @if(!empty($variationText))
                                                                                <span class="badge bg-light text-secondary border small">
                                                                                    {{ $variationText }}
                                                                                </span>
                                                                            @else
                                                                                <span class="text-muted small">-</span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="text-center small">{{ number_format($item->price, 0, ',', '.') }} ₫</td>
                                                                        <td class="text-center small fw-semibold">x{{ $item->quantity }}</td>
                                                                        <td class="text-end pe-3 text-danger fw-bold small">{{ number_format($item->subtotal ?? ($item->price * $item->quantity), 0, ',', '.') }} ₫</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <!-- Total Payment Block -->
                                                    <div class="card border-0 bg-light p-3 rounded text-end">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="fw-bold text-dark">Tổng thanh toán:</span>
                                                            <strong class="text-danger fs-5">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer d-flex justify-content-between">
                                                    <div>
                                                        @if($order->order_status === 'pending')
                                                            <form action="{{ route('profile.orders.cancel', $order->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')">
                                                                    <i class="bi bi-x-circle me-1"></i> Hủy đơn hàng
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('/profile/address') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm địa chỉ mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" name="receiver_name" class="form-control" required placeholder="Ví dụ: Nguyễn Văn A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" name="receiver_phone" class="form-control" required placeholder="Ví dụ: 0987654321">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="form-label">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                            <select id="add_province" name="province" class="form-select province-select" required>
                                <option value="" disabled selected>Chọn Tỉnh/Thành</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="form-label">Quận / Huyện <span class="text-danger">*</span></label>
                            <select id="add_district" name="district" class="form-select district-select" required>
                                <option value="" disabled selected>Chọn Quận/Huyện</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phường / Xã <span class="text-danger">*</span></label>
                            <select id="add_ward" name="ward" class="form-select ward-select" required>
                                <option value="" disabled selected>Chọn Phường/Xã</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                        <input type="text" name="specific_address" class="form-control" required placeholder="Số nhà, tên đường...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. URL Hash & Query Tab switcher
    const hash = window.location.hash;
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');

    if (hash === '#orders' || tabParam === 'orders') {
        const ordersTabBtn = document.getElementById('v-pills-orders-tab');
        if (ordersTabBtn && window.bootstrap) {
            const tab = new bootstrap.Tab(ordersTabBtn);
            tab.show();
        }
    } else if (hash === '#address' || tabParam === 'address') {
        const addressTabBtn = document.getElementById('v-pills-address-tab');
        if (addressTabBtn && window.bootstrap) {
            const tab = new bootstrap.Tab(addressTabBtn);
            tab.show();
        }
    }

    // 2. Location Administrative Data Handler
    let locationData = [];

    function populateDistricts(provinceName, districtSelect, wardSelect, targetDistrict = null, targetWard = null) {
        districtSelect.innerHTML = '<option value="" disabled selected>Chọn Quận/Huyện</option>';
        wardSelect.innerHTML = '<option value="" disabled selected>Chọn Phường/Xã</option>';

        const province = locationData.find(item => item.Name === provinceName || item.Id === provinceName);
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
            populateWards(provinceName, targetDistrict, wardSelect, targetWard);
        }
    }

    function populateWards(provinceName, districtName, wardSelect, targetWard = null) {
        wardSelect.innerHTML = '<option value="" disabled selected>Chọn Phường/Xã</option>';

        const province = locationData.find(item => item.Name === provinceName || item.Id === provinceName);
        if (!province || !province.Districts) return;

        const district = province.Districts.find(item => item.Name === districtName || item.Id === districtName);
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

            // Setup Add Address Modal
            const addProv = document.getElementById('add_province');
            const addDist = document.getElementById('add_district');
            const addWard = document.getElementById('add_ward');

            if (addProv && addDist && addWard) {
                addProv.innerHTML = '<option value="" disabled selected>Chọn Tỉnh/Thành</option>';
                locationData.forEach(prov => {
                    const opt = document.createElement('option');
                    opt.value = prov.Name;
                    opt.dataset.id = prov.Id;
                    opt.textContent = prov.Name;
                    addProv.appendChild(opt);
                });

                addProv.addEventListener('change', function () {
                    populateDistricts(this.value, addDist, addWard);
                });

                addDist.addEventListener('change', function () {
                    populateWards(addProv.value, this.value, addWard);
                });
            }

            // Setup Edit Address Modals
            document.querySelectorAll('.edit-province').forEach(editProv => {
                const addrId = editProv.dataset.addressId;
                const editDist = document.getElementById('edit_district_' + addrId);
                const editWard = document.getElementById('edit_ward_' + addrId);

                const selectedProv = editProv.dataset.selected;
                const selectedDist = editDist ? editDist.dataset.selected : null;
                const selectedWard = editWard ? editWard.dataset.selected : null;

                editProv.innerHTML = '<option value="" disabled selected>Chọn Tỉnh/Thành</option>';
                locationData.forEach(prov => {
                    const opt = document.createElement('option');
                    opt.value = prov.Name;
                    opt.dataset.id = prov.Id;
                    opt.textContent = prov.Name;
                    if (selectedProv && (prov.Name === selectedProv || prov.Id === selectedProv)) {
                        opt.selected = true;
                    }
                    editProv.appendChild(opt);
                });

                if (selectedProv && editDist && editWard) {
                    populateDistricts(selectedProv, editDist, editWard, selectedDist, selectedWard);
                }

                editProv.addEventListener('change', function () {
                    if (editDist && editWard) {
                        populateDistricts(this.value, editDist, editWard);
                    }
                });

                if (editDist) {
                    editDist.addEventListener('change', function () {
                        if (editProv && editWard) {
                            populateWards(editProv.value, this.value, editWard);
                        }
                    });
                }
            });
        })
        .catch(err => console.error('Error fetching administrative data:', err));
});
</script>
