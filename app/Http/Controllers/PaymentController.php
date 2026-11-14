<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentStatusService;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct()
    {
        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production');
        \Midtrans\Config::$isSanitized  = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds        = config('midtrans.is_3ds');
    }

    /** Buat (atau pakai ulang) Snap token, dikirim sebagai JSON ke browser. */
    public function token(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if ($order->payment_status !== 'pending') {
            return response()->json(['message' => 'Pesanan ini sudah tidak menunggu pembayaran.'], 422);
        }

        if ($order->snap_token) {
            return response()->json(['token' => $order->snap_token]);
        }

        $order->load('items', 'user');

        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                // Diambil dari database, bukan dari browser
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->recipient_name,
                'email'      => $order->user->email,
                'phone'      => $order->phone,
            ],
            'item_details' => $order->items->map(fn ($item) => [
                'id'       => (string) ($item->product_id ?? $item->id),
                'price'    => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name'     => Str::limit($item->product_name, 50, ''),
            ])->values()->all(),
            'expiry' => [
                'unit'     => 'hours',
                'duration' => 24,
            ],
        ];

        try {
            $token = \Midtrans\Snap::getSnapToken($params);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => 'Gagal membuat transaksi pembayaran. Coba lagi.'], 500);
        }

        $order->update(['snap_token' => $token]);

        return response()->json(['token' => $token]);
    }

    /** Tanya langsung status ke server Midtrans (berguna kalau webhook belum sampai). */
    public function check(Order $order, PaymentStatusService $service)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if ($order->payment_status !== 'pending') {
            return redirect()->route('orders.show', $order);
        }

        try {
            $status = (object) \Midtrans\Transaction::status($order->order_number);
        } catch (\Throwable $e) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Belum ada transaksi pembayaran yang tercatat di Midtrans untuk pesanan ini.');
        }

        $service->apply(
            $order,
            $status->transaction_status ?? 'pending',
            $status->fraud_status ?? null,
            $status->payment_type ?? null,
        );

        return redirect()->route('orders.show', $order)
            ->with('success', 'Status pembayaran diperbarui.');
    }
}