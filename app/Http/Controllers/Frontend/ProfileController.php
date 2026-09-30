<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load([
            'addresses' => function ($q) {
                $q->orderBy('is_default', 'desc')->latest();
            },
            'orders' => function ($q) {
                $q->with(['items.product', 'items.variation.attributeValues', 'items.review'])->latest();
            }
        ]);
        return view('frontend.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $user->update($request->only('name', 'phone'));
        return redirect()->back()->with('success', 'Cập nhật thông tin thành công');
    }

    public function storeAddress(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();
        
        if (Auth::user()->addresses()->count() == 0) {
            $data['is_default'] = 1;
        } else {
            $data['is_default'] = 0;
        }

        UserAddress::create($data);
        return redirect()->back()->with('success', 'Thêm địa chỉ thành công');
    }

    public function updateAddress(Request $request, $id)
    {
        $address = UserAddress::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $address->update($request->all());
        return redirect()->back()->with('success', 'Cập nhật địa chỉ thành công');
    }

    public function destroyAddress($id)
    {
        $address = UserAddress::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $address->delete();
        return redirect()->back()->with('success', 'Xóa địa chỉ thành công');
    }

    public function setDefaultAddress($id)
    {
        $userId = Auth::id();
        UserAddress::where('user_id', $userId)->update(['is_default' => 0]);
        UserAddress::where('id', $id)->where('user_id', $userId)->update(['is_default' => 1]);
        
        return redirect()->back()->with('success', 'Đã đặt làm địa chỉ mặc định');
    }

    public function cancelOrder(Order $order)
    {
        // 1. Kiểm tra quyền
        abort_if($order->user_id !== Auth::id(), 403, 'Bạn không có quyền thực hiện thao tác này.');

        // 2. Kiểm tra trạng thái: Chỉ cho phép hủy đơn hàng khi đang ở trạng thái 'pending'
        if ($order->order_status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể hủy đơn hàng đang chờ xác nhận.');
        }

        try {
            // 3. Hoàn tồn kho và cập nhật trạng thái đơn trong Transaction
            DB::transaction(function () use ($order) {
                $order->load(['items.product', 'items.variation']);

                foreach ($order->items as $item) {
                    // Nếu là biến thể thì cộng lại kho biến thể
                    if ($item->variation) {
                        $item->variation->increment('stock_quantity', $item->quantity);
                    }

                    // Nếu là sản phẩm thường thì cộng lại kho sản phẩm
                    if ($item->product) {
                        if (!$item->variation) {
                            $item->product->increment('stock_quantity', $item->quantity);
                        }

                        // Giảm lại số lượng đã bán nếu có
                        if ($item->product->sold_count >= $item->quantity) {
                            $item->product->decrement('sold_count', $item->quantity);
                        }
                    }
                }

                // Cập nhật trạng thái đơn hàng thành đã hủy
                $order->update([
                    'order_status' => 'cancelled',
                ]);
            });

            return redirect()->back()->with('success', 'Hủy đơn hàng thành công.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi hủy đơn hàng: ' . $e->getMessage());
        }
    }
}
