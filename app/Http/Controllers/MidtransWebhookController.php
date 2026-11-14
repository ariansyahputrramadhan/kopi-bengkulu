<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentStatusService;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request, PaymentStatusService $service)
    {
        $payload = $request->all();

        // 1. Verifikasi signature: SHA512(order_id + status_code + gross_amount + ServerKey)
        $expected = hash(
            'sha512',
            ($payload['order_id'] ?? '')
            .($payload['status_code'] ?? '')
            .($payload['gross_amount'] ?? '')
            .config('midtrans.server_key')
        );

        if (! hash_equals($expected, (string) ($payload['signature_key'] ?? ''))) {
            return response()->json(['message' => 'Signature tidak valid.'], 403);
        }

        // 2. Cari pesanan
        $order = Order::where('order_number', $payload['order_id'])->first();

        if (! $order) {
            return response()->json(['message' => 'Pesanan tidak ditemukan.'], 404);
        }

        // 3. Pastikan nominal sama dengan total di database
        if ((int) round((float) $payload['gross_amount']) !== (int) $order->total_amount) {
            return response()->json(['message' => 'Nominal tidak sesuai.'], 400);
        }

        // 4. Terapkan status
        $service->apply(
            $order,
            $payload['transaction_status'] ?? 'pending',
            $payload['fraud_status'] ?? null,
            $payload['payment_type'] ?? null,
        );

        return response()->json(['message' => 'OK']);
    }
}