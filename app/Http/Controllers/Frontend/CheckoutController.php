<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index(Request $request, CartService $cartService)
    {
        $selectedKeys = $request->input('selected_items', []);
        
        if (empty($selectedKeys)) {
            $checkoutItems = session('checkout_items');
            $checkoutTotal = session('checkout_total');

            if (!empty($checkoutItems) && is_array($checkoutItems)) {
                $selected_items = $checkoutItems;
                $total = $checkoutTotal ?? 0;
                $addresses = Auth::check() ? Auth::user()->addresses()->orderBy('is_default', 'desc')->get() : collect();
                return view('frontend.checkout.index', compact('selected_items', 'total', 'addresses'));
            }

            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn sản phẩm trước khi thanh toán!');
        }

        $cart = $cartService->getSessionCart();
        $selected_items = [];
        $total = 0;

        foreach ($selectedKeys as $key) {
            if (isset($cart[$key])) {
                $item = $cart[$key];
                $selected_items[$key] = $item;
                $price = $item['sale_price'] ?? $item['price'];
                $total += $price * $item['quantity'];
            }
        }

        if (empty($selected_items)) {
            return redirect()->route('cart.index')->with('error', 'Vui lòng chọn sản phẩm trước khi thanh toán!');
        }

        // Lưu tạm vào session để process xử lý
        session()->put('checkout_items', $selected_items);
        session()->put('checkout_total', $total);

        // Lấy danh sách địa chỉ đã lưu của người dùng
        $addresses = Auth::check() ? Auth::user()->addresses()->orderBy('is_default', 'desc')->get() : collect();

        return view('frontend.checkout.index', compact('selected_items', 'total', 'addresses'));
    }

    public function process(Request $request, CartService $cartService)
    {
        $request->validate([
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'specific_address' => 'required|string|max:255',
            'payment_method' => 'required|string|in:cod,momo',
            'shipping_fee' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:1000',
        ], [
            'receiver_name.required' => 'Vui lòng nhập tên người nhận.',
            'receiver_phone.required' => 'Vui lòng nhập số điện thoại người nhận.',
            'province.required' => 'Vui lòng chọn Tỉnh / Thành phố.',
            'district.required' => 'Vui lòng chọn Quận / Huyện.',
            'ward.required' => 'Vui lòng chọn Phường / Xã.',
            'specific_address.required' => 'Vui lòng nhập địa chỉ cụ thể.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
        ]);

        $checkoutItems = session('checkout_items');

        if (empty($checkoutItems) || !is_array($checkoutItems)) {
            return redirect()->route('cart.index')->with('error', 'Không có sản phẩm nào để thanh toán hoặc phiên mua hàng đã hết hạn.');
        }

        try {
            DB::beginTransaction();

            $verifiedItems = [];
            $itemsTotal = 0;

            // 1. Kiểm tra toàn vẹn dữ liệu từng sản phẩm (Tồn tại, Giá thực tế, Số lượng tồn kho)
            foreach ($checkoutItems as $key => $item) {
                $productId = $item['product_id'] ?? null;
                $variationId = $item['variation_id'] ?? null;
                $quantity = (int)($item['quantity'] ?? 0);

                if ($quantity <= 0) {
                    throw new \Exception("Số lượng sản phẩm không hợp lệ.");
                }

                if (!empty($variationId)) {
                    // Sản phẩm có biến thể
                    $variation = ProductVariation::with(['product', 'attributeValues'])->lockForUpdate()->find($variationId);
                    if (!$variation || !$variation->product) {
                        throw new \Exception("Một số biến thể sản phẩm đã không còn tồn tại.");
                    }

                    if ($variation->stock_quantity < $quantity) {
                        $label = $variation->attributeValues->pluck('value')->implode(' - ');
                        $labelStr = $label ? " ({$label})" : '';
                        throw new \Exception("Sản phẩm '{$variation->product->name}{$labelStr}' không đủ số lượng tồn kho (chỉ còn {$variation->stock_quantity} sản phẩm).");
                    }

                    $unitPrice = ($variation->sale_price && $variation->sale_price < $variation->price)
                        ? (float)$variation->sale_price
                        : (float)$variation->price;

                    $variationLabel = $variation->attributeValues->pluck('value')->implode(' - ');
                    $subtotal = $unitPrice * $quantity;
                    $itemsTotal += $subtotal;

                    $verifiedItems[] = [
                        'product_id' => $variation->product_id,
                        'variation_id' => $variation->id,
                        'product_name' => $variation->product->name,
                        'variation_label' => $variationLabel ?: null,
                        'quantity' => $quantity,
                        'price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'variation_model' => $variation,
                        'product_model' => $variation->product,
                    ];
                } else {
                    // Sản phẩm thường (không có biến thể)
                    $product = Product::lockForUpdate()->find($productId);
                    if (!$product) {
                        throw new \Exception("Sản phẩm đã chọn không tồn tại trong hệ thống.");
                    }

                    if ($product->stock_quantity < $quantity) {
                        throw new \Exception("Sản phẩm '{$product->name}' không đủ số lượng tồn kho (chỉ còn {$product->stock_quantity} sản phẩm).");
                    }

                    $unitPrice = ($product->sale_price && $product->sale_price < $product->price)
                        ? (float)$product->sale_price
                        : (float)$product->price;

                    $subtotal = $unitPrice * $quantity;
                    $itemsTotal += $subtotal;

                    $verifiedItems[] = [
                        'product_id' => $product->id,
                        'variation_id' => null,
                        'product_name' => $product->name,
                        'variation_label' => null,
                        'quantity' => $quantity,
                        'price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'variation_model' => null,
                        'product_model' => $product,
                    ];
                }
            }

            $shippingFee = (float)$request->input('shipping_fee', 0);

            // 2. Lưu địa chỉ nếu người dùng chọn lưu
            if ($request->boolean('save_address') && Auth::check()) {
                $exists = Auth::user()->addresses()
                    ->where('receiver_name', $request->receiver_name)
                    ->where('receiver_phone', $request->receiver_phone)
                    ->where('province', $request->province)
                    ->where('district', $request->district)
                    ->where('ward', $request->ward)
                    ->where('specific_address', $request->specific_address)
                    ->exists();

                if (!$exists) {
                    UserAddress::create([
                        'user_id' => Auth::id(),
                        'receiver_name' => $request->receiver_name,
                        'receiver_phone' => $request->receiver_phone,
                        'province' => $request->province,
                        'district' => $request->district,
                        'ward' => $request->ward,
                        'specific_address' => $request->specific_address,
                        'is_default' => Auth::user()->addresses()->count() == 0 ? 1 : 0,
                    ]);
                }
            }

            // 3. Tạo đơn hàng mới
            $orderCode = 'FT-' . date('YmdHis') . '-' . strtoupper(Str::random(4));
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_code' => $orderCode,
                'receiver_name' => $request->receiver_name,
                'receiver_phone' => $request->receiver_phone,
                'province' => $request->province,
                'district' => $request->district,
                'ward' => $request->ward,
                'specific_address' => $request->specific_address,
                'payment_method' => $request->payment_method,
                'shipping_fee' => $request->input('shipping_fee', 0),
                'total_amount' => $itemsTotal + $request->input('shipping_fee', 0),
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'note' => $request->note,
            ]);

            // 4. Tạo chi tiết đơn hàng & cập nhật tồn kho + số lượng bán
            foreach ($verifiedItems as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'variation_id' => $itemData['variation_id'],
                    'product_name' => $itemData['product_name'],
                    'variation_label' => $itemData['variation_label'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'subtotal' => $itemData['subtotal'],
                ]);

                // Trừ tồn kho
                if (!empty($itemData['variation_model'])) {
                    $itemData['variation_model']->decrement('stock_quantity', $itemData['quantity']);
                }
                if (!empty($itemData['product_model'])) {
                    if (empty($itemData['variation_model'])) {
                        $itemData['product_model']->decrement('stock_quantity', $itemData['quantity']);
                    }
                    $itemData['product_model']->increment('sold_count', $itemData['quantity']);
                }
            }

            // 5. Xóa các item đã mua khỏi giỏ hàng session
            $cartService->removeMultiple(array_keys($checkoutItems));
            
            // Xóa session checkout
            session()->forget(['checkout_items', 'checkout_total']);

            DB::commit();

            return redirect()->route('checkout.success', ['orderCode' => $order->order_code])
                ->with('success', 'Chúc mừng bạn đã đặt hàng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi khi xử lý đơn hàng: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function success(Request $request, $orderCode = null)
    {
        $order = null;
        if ($orderCode) {
            $order = Order::with(['items.product', 'items.variation.attributeValues'])
                ->where('order_code', $orderCode)
                ->where('user_id', Auth::id())
                ->first();
        }

        if (!$order) {
            // Lấy đơn hàng gần nhất của user nếu không truyền orderCode
            $order = Order::with(['items.product', 'items.variation.attributeValues'])
                ->where('user_id', Auth::id())
                ->latest()
                ->first();
        }

        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin đơn hàng.');
        }

        return view('frontend.checkout.success', compact('order'));
    }

    public function calculateShippingFee(Request $request)
    {
        $districtName = $request->input('district_name');
        $wardName = $request->input('ward_name');
        $subtotal = $request->input('subtotal', 0);

        $defaultFee = 35000; // Phí mặc định nếu lỗi

        Log::info('--- BẮT ĐẦU TÍNH PHÍ GHN ---', [
            'district_name' => $districtName,
            'ward_name' => $wardName,
            'subtotal' => $subtotal
        ]);

        try {
            $token = config('services.ghn.token') ?? env('GHN_TOKEN');
            if (empty($token)) {
                Log::error('GHN Token bị rỗng!');
            }

            $baseUrl = config('services.ghn.base_url');
            $shopId = (int)config('services.ghn.shop_id');
            $fromDistrictId = (int)config('services.ghn.from_district_id');

            $masterHeaders = [
                'Token' => trim($token),
                'Content-Type' => 'application/json',
            ];

            $feeHeaders = [
                'Token' => trim($token),
                'ShopId' => $shopId,
                'Content-Type' => 'application/json',
            ];

            // 1. Lấy District ID từ tên huyện
            $districtsResponse = Http::withoutVerifying()->withHeaders($masterHeaders)->get(rtrim($baseUrl, '/') . '/master-data/district');
            if (!$districtsResponse->successful()) {
                Log::error('GHN Lỗi lấy danh sách Huyện', ['status' => $districtsResponse->status(), 'response' => $districtsResponse->json()]);
                throw new \Exception('Không thể lấy danh sách quận/huyện từ GHN.');
            }

            $districts = $districtsResponse->json('data');
            $toDistrictId = null;
            
            $cleanDistrictName = trim(mb_strtolower(str_replace(['Quận ', 'Huyện ', 'Thành phố ', 'Thị xã '], '', $districtName)));
            
            foreach ($districts as $d) {
                $dName = trim(mb_strtolower($d['DistrictName']));
                if (str_contains($dName, $cleanDistrictName) || $dName === mb_strtolower($districtName)) {
                    $toDistrictId = (int)$d['DistrictID'];
                    break;
                }
            }

            if (!$toDistrictId) {
                Log::warning('GHN Không tìm thấy District ID cho: ' . $districtName);
                throw new \Exception('Không tìm thấy mã quận/huyện tương ứng.');
            }

            // 2. Lấy Ward Code từ tên xã
            $wardsResponse = Http::withoutVerifying()->withHeaders($masterHeaders)->get(rtrim($baseUrl, '/') . '/master-data/ward', ['district_id' => $toDistrictId]);
            $wards = $wardsResponse->json('data');
            $toWardCode = null;

            if ($wards) {
                $cleanWardName = trim(mb_strtolower(str_replace(['Phường ', 'Xã ', 'Thị trấn '], '', $wardName)));
                foreach ($wards as $w) {
                    $wName = trim(mb_strtolower($w['WardName']));
                    if (str_contains($wName, $cleanWardName) || $wName === mb_strtolower($wardName)) {
                        $toWardCode = (string)$w['WardCode'];
                        break;
                    }
                }
            }

            if (!$toWardCode && !empty($wards)) {
                Log::warning('GHN Không tìm thấy Ward Code khớp chính xác, lấy xã đầu tiên. Tên gốc: ' . $wardName);
                $toWardCode = (string)$wards[0]['WardCode']; // Fallback
            }

            if (!$toWardCode) {
                Log::error('GHN Không có dữ liệu xã cho Huyện ID: ' . $toDistrictId);
                throw new \Exception('Không tìm thấy xã/phường hợp lệ.');
            }

            // 3. Lấy service_id khả dụng thay vì fix cứng
            $servicesResponse = Http::withoutVerifying()->withHeaders($feeHeaders)->post(rtrim($baseUrl, '/') . '/v2/shipping-order/available-services', [
                "shop_id" => $shopId,
                "from_district" => $fromDistrictId,
                "to_district" => $toDistrictId
            ]);
            
            $serviceId = null;
            $serviceTypeId = 2; // Default
            
            if ($servicesResponse->successful() && $servicesResponse->json('code') == 200) {
                $services = $servicesResponse->json('data');
                if (!empty($services)) {
                    // Ưu tiên lấy service_id đầu tiên
                    $serviceId = (int)$services[0]['service_id'];
                    $serviceTypeId = (int)$services[0]['service_type_id'];
                }
            } else {
                Log::warning('GHN Không thể lấy danh sách gói cước', ['status' => $servicesResponse->status(), 'response' => $servicesResponse->json()]);
            }

            // 4. Tính phí vận chuyển
            $payload = [
                "from_district_id" => $fromDistrictId,
                "to_district_id" => $toDistrictId,
                "to_ward_code" => $toWardCode,
                "weight" => 1000,
                "length" => 20, 
                "width" => 20, 
                "height" => 20,
                "insurance_value" => (int)$subtotal,
            ];

            if ($serviceId) {
                $payload["service_id"] = $serviceId;
            } else {
                $payload["service_type_id"] = $serviceTypeId;
            }

            Log::info('GHN Payload tính phí:', $payload);

            $feeResponse = Http::withoutVerifying()->withHeaders($feeHeaders)->post(rtrim($baseUrl, '/') . '/v2/shipping-order/fee', $payload);

            if ($feeResponse->successful() && $feeResponse->json('code') == 200) {
                $fee = (int)$feeResponse->json('data.total');
                Log::info('GHN Tính phí thành công:', ['fee' => $fee]);
            } else {
                Log::error('GHN Lỗi tính phí API', ['status' => $feeResponse->status(), 'response' => $feeResponse->json()]);
                $fee = $defaultFee;
            }

        } catch (\Exception $e) {
            Log::error('GHN Lỗi Exception trong quá trình tính phí:', ['message' => $e->getMessage()]);
            $fee = $defaultFee;
        }

        return response()->json([
            'success' => true,
            'shipping_fee' => $fee,
            'formatted_shipping_fee' => number_format($fee, 0, ',', '.') . ' đ',
            'total' => $subtotal + $fee,
            'formatted_total' => number_format($subtotal + $fee, 0, ',', '.') . ' đ'
        ]);
    }
}

