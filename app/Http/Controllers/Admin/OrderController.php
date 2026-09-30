<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng
     */
    public function index(Request $request)
    {
        $tabs = [
            'all' => ['label' => 'Tất cả', 'statuses' => []],
            'pending' => ['label' => 'Chờ xử lý', 'statuses' => ['pending', 'processing']],
            'packed' => ['label' => 'Chờ lấy hàng', 'statuses' => ['packed']],
            'shipping' => ['label' => 'Đang giao', 'statuses' => ['shipping']],
            'completed' => ['label' => 'Thành công', 'statuses' => ['completed']],
            'returned' => ['label' => 'Hoàn hàng', 'statuses' => ['returned']],
            'cancelled' => ['label' => 'Đã hủy', 'statuses' => ['cancelled']],
        ];

        $activeTab = $request->get('tab', 'all');
        $activeTab = array_key_exists($activeTab, $tabs) ? $activeTab : 'all';

        $query = Order::with(['user', 'items'])->latest();

        if ($request->filled('tab') && $activeTab !== 'all') {
            $query->whereIn('order_status', $tabs[$activeTab]['statuses']);
        }

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('search')) {
            $keyword = trim($request->search);
            $query->where(function ($q) use ($keyword) {
                $q->where('order_code', 'like', "%{$keyword}%")
                  ->orWhere('receiver_name', 'like', "%{$keyword}%")
                  ->orWhere('receiver_phone', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'amount_asc':
                $query->orderBy('total_amount', 'asc');
                break;
            case 'amount_desc':
                $query->orderBy('total_amount', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $perPage = (int) ($request->get('per_page', 15));
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $orders = $query->paginate($perPage)->withQueryString();

        $tabCounts = [];
        foreach ($tabs as $key => $tab) {
            $countQuery = Order::query();
            if ($key !== 'all') {
                $countQuery->whereIn('order_status', $tab['statuses']);
            }
            $tabCounts[$key] = $countQuery->count();
        }

        return view('admin.orders.index', compact('orders', 'tabs', 'tabCounts', 'activeTab'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show(Order $order)
    {
        // Bổ sung load danh sách nhật ký giao dịch MoMo/COD để Admin xem lịch sử thanh toán
        $order->load([
            'user', 
            'items.product', 
            'items.variation.attributeValues',
            'paymentTransactions' => function ($q) {
                $q->latest();
            }
        ]);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn hàng (xử lý hoàn tồn kho nếu hủy đơn)
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,packed,shipping,completed,cancelled,returned',
            'payment_status' => 'nullable|in:pending,paid,failed',
        ], [
            'order_status.required' => 'Vui lòng chọn trạng thái đơn hàng.',
            'order_status.in' => 'Trạng thái đơn hàng không hợp lệ theo quy trình.',
            'payment_status.in' => 'Trạng thái thanh toán không hợp lệ.',
        ]);

        try {
            DB::transaction(function () use ($request, $order) {
                $newStatus = $request->order_status;
                $oldStatus = $order->order_status;

                $allowedStatuses = $order->getAllowedNextStatuses();
                if (!in_array($newStatus, $allowedStatuses, true)) {
                    throw new \InvalidArgumentException('Không thể chuyển đơn hàng từ trạng thái "' . ($oldStatus ?? 'chưa xác định') . '" sang "' . $newStatus . '" theo quy trình hiện tại.');
                }

                if ($newStatus === 'completed') {
                    if ($order->payment_method === 'cod') {
                        $request->merge(['payment_status' => 'paid']);
                    } elseif ($request->input('payment_status') !== 'paid') {
                        $request->merge(['payment_status' => 'paid']);
                    }
                }

                if ($newStatus === 'cancelled' && $request->input('payment_status') !== 'failed') {
                    $request->merge(['payment_status' => 'failed']);
                }

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

                    // Đồng bộ chuyển trạng thái các giao dịch MoMo đang chờ thành 'cancelled'
                    PaymentTransaction::where('order_id', $order->id)
                        ->where('status', 'pending')
                        ->update([
                            'status' => 'cancelled',
                            'message' => 'Đơn hàng đã bị hủy bởi Quản trị viên'
                        ]);
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

                    // Nếu Admin cập nhật thủ công payment_status sang 'paid', tự động ghi nhận vào PaymentTransaction
                    if ($request->payment_status === 'paid') {
                        PaymentTransaction::where('order_id', $order->id)
                            ->where('status', '!=', 'paid')
                            ->update([
                                'status' => 'paid',
                                'paid_at' => now(),
                                'message' => 'Đã xác nhận thanh toán thủ công bởi Quản trị viên'
                            ]);
                    }
                }

                $order->update($updateData);
            });

            return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật đơn hàng: ' . $e->getMessage());
        }
    }
}