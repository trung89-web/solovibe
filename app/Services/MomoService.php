<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class MomoService
{
    protected function getConfigs(): array
    {
        return [
            'endpoint'     => config('services.momo.endpoint')     ?: 'https://test-payment.momo.vn/v2/gateway/api/create',
            'partnerCode'  => config('services.momo.partner_code') ?: 'MOMOBKUN20180529',
            'accessKey'    => config('services.momo.access_key')   ?: 'klm05TvNBzhg7h7j',
            'secretKey'    => config('services.momo.secret_key')   ?: 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa',
            'verifySsl'    => filter_var(config('services.momo.verify_ssl', false), FILTER_VALIDATE_BOOLEAN),
        ];
    }

    public function createPayment(Order $order, PaymentTransaction $transaction): array
    {
        $cfg = $this->getConfigs();

        $orderCode   = (string) ($order->order_code ?? $order->id);
        $orderInfo   = 'Thanh toan don hang #' . $orderCode;
        $total       = $order->total_amount ?? $order->total_price ?? 0;
        $amount      = (string) ((int) $total);
        $orderId     = $orderCode . '_' . time();
        
        $redirectUrl = config('services.momo.redirect_url') ?: url('/payment/momo/callback');
        $ipnUrl      = config('services.momo.ipn_url') ?: url('/payment/momo/ipn');
        
        $extraData   = base64_encode($orderCode); // Yêu cầu của Momo API v2 là base64
        $requestId   = (string) time();
        $requestType = 'payWithMethod'; // Hoặc captureWallet để hiển thị chọn phương thức

        $rawHash = "accessKey={$cfg['accessKey']}&amount={$amount}&extraData={$extraData}&ipnUrl={$ipnUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$cfg['partnerCode']}&redirectUrl={$redirectUrl}&requestId={$requestId}&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawHash, $cfg['secretKey']);

        $data = [
            'partnerCode' => $cfg['partnerCode'],
            'partnerName' => 'SoleVibe Store',
            'storeId'     => 'MomoStore',
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
        ];

        $transaction->update([
            'gateway_order_id' => $orderId,
            'request_payload'  => $data,
        ]);

        try {
            $response = Http::timeout(30)->connectTimeout(15)->withOptions([
                'verify' => $cfg['verifySsl'],
            ])->post($cfg['endpoint'], $data);

            $result = $response->json() ?? [];
        } catch (\Exception $e) {
            $result = [
                'resultCode' => 99,
                'message'    => 'Lỗi kết nối API MoMo: ' . $e->getMessage()
            ];
        }

        $transaction->update([
            'response_payload' => $result,
            'result_code'      => isset($result['resultCode']) ? (int) $result['resultCode'] : null,
            'message'          => $result['message'] ?? null,
            'status'           => isset($result['payUrl']) ? 'initiated' : 'failed',
        ]);

        return $result;
    }

    public function markPaid(PaymentTransaction $transaction, array $payload): void
    {
        $transaction->update([
            'transaction_id'   => $payload['transId'] ?? null,
            'result_code'      => (int) ($payload['resultCode'] ?? 0),
            'message'          => $payload['message'] ?? null,
            'response_payload' => $payload,
            'status'           => 'paid',
            'paid_at'          => Carbon::now(),
        ]);
    }

    public function markFailed(PaymentTransaction $transaction, array $payload): void
    {
        $transaction->update([
            'transaction_id'   => $payload['transId'] ?? null,
            'result_code'      => isset($payload['resultCode']) ? (int) $payload['resultCode'] : null,
            'message'          => $payload['message'] ?? null,
            'response_payload' => $payload,
            'status'           => 'failed',
        ]);
    }
}