<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PaymentStatusService
{
    /**
     * Terapkan status dari Midtrans ke pesanan.
     * Hanya pesanan berstatus "pending" yang bisa berubah (status final tidak diubah lagi),
     * jadi notifikasi ganda dari Midtrans aman (idempotent) dan stok tidak dikembalikan dua kali.
     */
    public function apply(Order $order, string $transactionStatus, ?string $fraudStatus = null, ?string $paymentType = null): void
    {
        DB::transaction(function () use ($order, $transactionStatus, $fraudStatus, $paymentType) {
            // Kunci baris pesanan agar webhook & cek status tidak bentrok
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            if ($order->payment_status !== 'pending') {
                return;
            }

            $newStatus = match (true) {
                $transactionStatus === 'capture'
                    => $fraudStatus === 'challenge' ? 'pending' : 'paid',
                $transactionStatus === 'settlement'
                    => 'paid',
                in_array($transactionStatus, ['deny', 'cancel', 'failure'], true)
                    => 'failed',
                $transactionStatus === 'expire'
                    => 'expired',
                default
                    => 'pending',
            };

            if ($paymentType) {
                $order->payment_type = $paymentType;
            }

            if ($newStatus === 'paid') {
                $order->payment_status = 'paid';
                $order->paid_at = now();
            } elseif (in_array($newStatus, ['failed', 'expired'], true)) {
                $order->payment_status = $newStatus;
                $order->order_status = 'cancelled';
                $this->restoreStock($order);
            }

            $order->save();
        });
    }

    private function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product_id) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }
        }
    }
}