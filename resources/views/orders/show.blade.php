@extends('layouts.shop')

@section('title', 'Pesanan '.$order->order_number)

@section('content')
    <nav class="text-sm text-gray-500 mb-4">
        <a href="{{ route('orders.index') }}" class="hover:underline">Pesanan Saya</a> /
        <span>{{ $order->order_number }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Daftar produk --}}
        <div class="lg:col-span-2 bg-white rounded shadow overflow-x-auto">
            <h2 class="text-lg font-bold p-4 border-b">Produk yang Dibeli</h2>
            <table class="w-full text-left">
                <thead class="bg-gray-100 text-sm">
                    <tr>
                        <th class="p-3">Produk</th>
                        <th class="p-3">Harga</th>
                        <th class="p-3">Jumlah</th>
                        <th class="p-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr class="border-t">
                            <td class="p-3">{{ $item->product_name }}</td>
                            <td class="p-3 whitespace-nowrap">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="p-3">{{ $item->quantity }}</td>
                            <td class="p-3 whitespace-nowrap">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t bg-gray-50">
                        <td colspan="3" class="p-3 text-right font-semibold">Total</td>
                        <td class="p-3 font-bold text-amber-900 whitespace-nowrap">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Info pesanan --}}
        <aside class="space-y-4">
            <div class="bg-white rounded shadow p-5">
                <h2 class="text-lg font-bold mb-3">Info Pesanan</h2>
                <dl class="text-sm space-y-2">
                    <div><dt class="text-gray-500">No. Invoice</dt><dd class="font-medium">{{ $order->order_number }}</dd></div>
                    <div><dt class="text-gray-500">Tanggal</dt><dd>{{ $order->created_at->format('d M Y H:i') }}</dd></div>
                    <div>
                        <dt class="text-gray-500">Status Pembayaran</dt>
                        <dd><span class="px-2 py-1 rounded text-xs {{ $order->paymentBadgeClass() }}">{{ $order->paymentStatusLabel() }}</span></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Status Pesanan</dt>
                        <dd><span class="px-2 py-1 rounded text-xs {{ $order->orderBadgeClass() }}">{{ $order->orderStatusLabel() }}</span></dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded shadow p-5">
                <h2 class="text-lg font-bold mb-3">Pengiriman</h2>
                <div class="text-sm space-y-1">
                    <div class="font-medium">{{ $order->recipient_name }}</div>
                    <div>{{ $order->phone }}</div>
                    <div>{{ $order->address }}</div>
                    <div>{{ $order->city }} {{ $order->postal_code }}</div>
                </div>
            </div>

            @if ($order->payment_status === 'pending')
                <div class="bg-amber-50 border border-amber-200 rounded p-5 text-sm">
                    {{-- Tombol Bayar (Midtrans) dibuat di Tahap 7 --}}
                    Pembayaran via Midtrans akan diaktifkan di tahap berikutnya.
                </div>
            @endif
        </aside>
    </div>
@endsection