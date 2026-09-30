<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MomoService;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;

class MomoController extends Controller
{
    public function startPayment($order_id, MomoService $momoService)
    {
        $cleanId = ltrim($order_id, '#');
        $order = Order::where('id', $cleanId)
                      ->orWhere('order_code', $cleanId)
                      ->orWhere('order_code', '#' . $cleanId)
                      ->firstOrFail();

        $amount = $order->total_amount ?? $order->total_price ?? 0;

        $transaction = PaymentTransaction::create([
            'order_id'       => $order->id,
            'user_id'        => auth()->id() ?? $order->user_id,
            'gateway'        => 'momo',
            'payment_method' => 'momo',
            'amount'         => $amount,
            'status'         => 'pending',
        ]);

        try {
            $result = $momoService->createPayment($order, $transaction);

            if (!empty($result['payUrl'])) {
                return redirect()->away($result['payUrl']);
            }

            return redirect()->back()->with('error', $result['message'] ?? 'Không thể kết nối MoMo.');
        } catch (\Exception $e) {
            Log::error('MoMo Start Payment Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khởi tạo thanh toán.');
        }
    }

    public function callback(Request $request, MomoService $momoService)
    {
        $payload = $request->all();
        Log::info('--- MOMO CALLBACK RECEIVED ---', $payload);

        try {
            // Lấy mã đơn hàng từ extraData hoặc tách từ orderId
            $rawCode = !empty($payload['extraData']) ? base64_decode($payload['extraData']) : null;
            if (!$rawCode && !empty($payload['orderId'])) {
                $parts = explode('_', $payload['orderId']);
                $rawCode = $parts[0] ?? null;
            }

            // Xử lý linh hoạt tìm kiếm đơn hàng dù CSDL có chứa dấu # hay không
            $cleanCode = ltrim($rawCode, '#');
            $order = Order::where('order_code', $cleanCode)
                          ->orWhere('order_code', '#' . $cleanCode)
                          ->orWhere('id', $cleanCode)
                          ->first();

            if (!$order) {
                Log::error('MoMo Callback Error: Không tìm thấy đơn hàng ' . $rawCode);
                return redirect()->route('home')->with('error', 'Không tìm thấy thông tin đơn hàng.');
            }

            $resultCode = isset($payload['resultCode']) ? (int)$payload['resultCode'] : -1;

            // Xử lý khi thanh toán thành công (resultCode = 0)
            if ($resultCode === 0) {
                // 1. Cập nhật trạng thái đơn hàng sau khi thanh toán thành công
                $order->update([
                    'payment_status' => 'paid',
                    'order_status'  => 'processing',
                ]);

                // 2. Cập nhật hoặc ghi nhận giao dịch thành công
                PaymentTransaction::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'user_id'          => $order->user_id,
                        'gateway'          => 'momo',
                        'payment_method'   => 'momo',
                        'transaction_id'   => $payload['transId'] ?? null,
                        'amount'           => $payload['amount'] ?? 0,
                        'status'           => 'paid',
                        'result_code'      => $resultCode,
                        'response_payload' => $payload,
                    ]
                );

                // 3. Xóa giỏ hàng
                session()->forget('cart');

                // 4. Quay về Trang chủ với thông báo thành công
                return redirect()->route('home')->with('success', 'Thanh toán đơn hàng qua MoMo thành công!');
            }

            // Thanh toán thất bại hoặc bị hủy
            $order->update([
                'payment_status' => 'failed',
                'order_status'   => 'cancelled',
            ]);

            return redirect()->route('home')->with('error', 'Thanh toán không thành công hoặc đã bị hủy.');

        } catch (\Exception $e) {
            Log::error('MoMo Callback Exception: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Đã xảy ra lỗi trong quá trình xử lý thanh toán.');
        }
    }

    public function ipn(Request $request, MomoService $momoService)
    {
        $payload = $request->all();
        Log::info('--- MOMO IPN RECEIVED ---', $payload);

        try {
            $rawCode = !empty($payload['extraData']) ? base64_decode($payload['extraData']) : null;
            if (!$rawCode && !empty($payload['orderId'])) {
                $parts = explode('_', $payload['orderId']);
                $rawCode = $parts[0] ?? null;
            }

            $cleanCode = ltrim($rawCode, '#');
            $order = Order::where('order_code', $cleanCode)
                          ->orWhere('order_code', '#' . $cleanCode)
                          ->orWhere('id', $cleanCode)
                          ->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $resultCode = isset($payload['resultCode']) ? (int)$payload['resultCode'] : -1;

            if ($resultCode === 0) {
                $order->update([
                    'payment_status' => 'paid',
                    'order_status'  => 'processing',
                ]);

                PaymentTransaction::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'user_id'          => $order->user_id,
                        'gateway'          => 'momo',
                        'payment_method'   => 'momo',
                        'transaction_id'   => $payload['transId'] ?? null,
                        'amount'           => $payload['amount'] ?? 0,
                        'status'           => 'paid',
                        'result_code'      => $resultCode,
                        'response_payload' => $payload,
                    ]
                );

                return response()->json(['message' => 'Success'], 200);
            }

            $order->update([
                'payment_status' => 'failed',
                'order_status'   => 'cancelled',
            ]);

            return response()->json(['message' => 'Failed transaction'], 400);

        } catch (\Exception $e) {
            Log::error('MoMo IPN Exception: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}