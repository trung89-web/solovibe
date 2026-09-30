<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('search')) {
            $keyword = trim($request->search);
            $query->where(function ($q) use ($keyword) {
                $q->where('order_code', 'like', "%{$keyword}%")
                    ->orWhere('receiver_name', 'like', "%{$keyword}%")
                    ->orWhere('receiver_phone', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('payment')) {
            $query->where('payment_status', $request->payment);
        }

        $orders = $query->paginate(12)->withQueryString();

        $totalRevenue = Order::where(function ($query) {
                $query->where('payment_status', 'paid')
                    ->orWhere('order_status', 'completed');
            })
            ->sum('total_amount');

        $todayRevenue = Order::whereDate('created_at', today())
            ->where(function ($query) {
                $query->where('payment_status', 'paid')
                    ->orWhere('order_status', 'completed');
            })
            ->sum('total_amount');

        $monthlyRevenue = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_amount) as revenue')
            ->where(function ($query) {
                $query->where('payment_status', 'paid')
                    ->orWhere('order_status', 'completed');
            })
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->limit(6)
            ->get();

        $orderStats = Order::selectRaw('order_status, COUNT(*) as total')
            ->groupBy('order_status')
            ->get()
            ->keyBy('order_status');

        $newCustomers = User::whereDate('created_at', today())->count();
        $totalOrders = Order::count();
        $completedOrders = Order::where('order_status', 'completed')->count();
        $pendingPayment = Order::where('payment_method', 'cod')->where('payment_status', '!=', 'paid')->sum('total_amount');

        $topProducts = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact(
            'orders',
            'totalRevenue',
            'todayRevenue',
            'monthlyRevenue',
            'orderStats',
            'newCustomers',
            'totalOrders',
            'completedOrders',
            'pendingPayment',
            'topProducts'
        ));
    }

    public function transactions(Request $request)
    {
        $transactions = PaymentTransaction::with('order.user')->latest();

        if ($request->filled('search')) {
            $keyword = trim($request->search);
            $transactions->where(function ($q) use ($keyword) {
                $q->where('gateway', 'like', "%{$keyword}%")
                    ->orWhere('status', 'like', "%{$keyword}%")
                    ->orWhere('transaction_id', 'like', "%{$keyword}%")
                    ->orWhereHas('order', function ($orderQuery) use ($keyword) {
                        $orderQuery->where('order_code', 'like', "%{$keyword}%")
                            ->orWhere('receiver_name', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('gateway')) {
            $transactions->where('gateway', $request->gateway);
        }

        if ($request->filled('status')) {
            $transactions->where('status', $request->status);
        }

        $transactions = $transactions->paginate(15)->withQueryString();

        return view('admin.reports.transactions', compact('transactions'));
    }

    public function updateCodPayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        if ($order->payment_method !== 'cod') {
            return redirect()->back()->with('error', 'Chức năng cập nhật thanh toán COD chỉ áp dụng cho đơn COD.');
        }

        DB::transaction(function () use ($order, $request) {
            $updateData = [
                'payment_status' => $request->payment_status,
            ];

            if ($request->payment_status === 'paid') {
                $updateData['order_status'] = 'completed';
            }

            $order->update($updateData);

            PaymentTransaction::updateOrCreate(
                ['order_id' => $order->id, 'gateway' => 'cod'],
                [
                    'gateway' => 'cod',
                    'amount' => $order->total_amount,
                    'status' => $request->payment_status,
                    'message' => $request->payment_status === 'paid' ? 'Đã xác nhận thanh toán COD sau khi giao hàng thành công.' : 'Chưa thanh toán COD.',
                    'paid_at' => $request->payment_status === 'paid' ? now() : null,
                    'response_payload' => ['manual_update' => true],
                ]
            );
        });

        return redirect()->back()->with('success', 'Cập nhật trạng thái thanh toán COD thành công.');
    }

    public function exportExcel()
    {
        $rows = Order::with('user')
            ->select('order_code', 'receiver_name', 'receiver_phone', 'payment_method', 'payment_status', 'order_status', 'total_amount', 'created_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($order) {
                return [
                    'Mã đơn' => $order->order_code,
                    'Khách hàng' => $order->user?->name ?? $order->receiver_name,
                    'SĐT' => $order->receiver_phone,
                    'Phương thức' => strtoupper($order->payment_method),
                    'TT thanh toán' => $order->payment_status,
                    'TT đơn hàng' => $order->order_status,
                    'Tổng tiền' => (float) $order->total_amount,
                    'Ngày tạo' => $order->created_at->format('d/m/Y H:i:s'),
                ];
            });

        $filename = 'bao-cao-don-hang-' . now()->format('Ymd_His') . '.csv';

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Mã đơn', 'Khách hàng', 'SĐT', 'Phương thức', 'TT thanh toán', 'TT đơn hàng', 'Tổng tiền', 'Ngày tạo']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['Mã đơn'],
                    $row['Khách hàng'],
                    $row['SĐT'],
                    $row['Phương thức'],
                    $row['TT thanh toán'],
                    $row['TT đơn hàng'],
                    $row['Tổng tiền'],
                    $row['Ngày tạo'],
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
