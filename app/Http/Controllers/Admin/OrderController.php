<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();

        // Lọc theo trạng thái đơn hàng nếu có
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        // Tìm kiếm theo mã đơn, tên hoặc số điện thoại người nhận
        if ($request->filled('search')) {
            $keyword = trim($request->search);
            $query->where(function ($q) use ($keyword) {
                $q->where('order_code', 'like', "%{$keyword}%")
                  ->orWhere('receiver_name', 'like', "%{$keyword}%")
                  ->orWhere('receiver_phone', 'like', "%{$keyword}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'items.variation.attributeValues']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn hàng (xử lý hoàn tồn kho nếu hủy đơn)
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,shipping,completed,cancelled,returned',
            'payment_status' => 'nullable|in:pending,paid,failed',
        ], [
            'order_status.required' => 'Vui lòng chọn trạng thái đơn hàng.',
            'order_status.in' => 'Trạng thái đơn hàng không hợp lệ.',
            'payment_status.in' => 'Trạng thái thanh toán không hợp lệ.',
        ]);

        try {
            DB::transaction(function () use ($request, $order) {
                $newStatus = $request->order_status;
                $oldStatus = $order->order_status;

                // Nếu chuyển sang 'cancelled' và trạng thái hiện tại khác 'cancelled' => Hoàn kho
                if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                    $order->load(['items.product', 'items.variation']);

                    foreach ($order->items as $item) {
                        // Hoàn kho biến thể
                        if ($item->variation) {
                            $item->variation->increment('stock_quantity', $item->quantity);
                        }

                        // Hoàn kho sản phẩm thường
                        if ($item->product) {
                            if (!$item->variation) {
                                $item->product->increment('stock_quantity', $item->quantity);
                            }

                            // Giảm lại số lượng bán đã cộng
                            if ($item->product->sold_count >= $item->quantity) {
                                $item->product->decrement('sold_count', $item->quantity);
                            }
                        }
                    }
                }

                // Nếu từ 'cancelled' phục hồi sang trạng thái khác => Trừ lại kho
                if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                    $order->load(['items.product', 'items.variation']);

                    foreach ($order->items as $item) {
                        if ($item->variation) {
                            $item->variation->decrement('stock_quantity', $item->quantity);
                        }

                        if ($item->product) {
                            if (!$item->variation) {
                                $item->product->decrement('stock_quantity', $item->quantity);
                            }
                            $item->product->increment('sold_count', $item->quantity);
                        }
                    }
                }

                // Cập nhật thông tin đơn hàng
                $updateData = ['order_status' => $newStatus];
                if ($request->filled('payment_status')) {
                    $updateData['payment_status'] = $request->payment_status;
                }

                $order->update($updateData);
            });

            return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật đơn hàng: ' . $e->getMessage());
        }
    }
}
