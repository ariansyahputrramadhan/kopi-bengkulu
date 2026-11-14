@extends('layouts.shop')

@section('title', 'Pesanan Saya')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Riwayat Pesanan</h1>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3">No. Invoice</th>
                    <th class="p-3">Tanggal</th>
                    <th class="p-3">Item</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Pembayaran</th>
                    <th class="p-3">Pesanan</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-t">
                        <td class="p-3 font-medium whitespace-nowrap">{{ $order->order_number }}</td>
                        <td class="p-3 whitespace-nowrap">{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td class="p-3">{{ $order->items_count }}</td>
                        <td class="p-3 whitespace-nowrap">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs {{ $order->paymentBadgeClass() }}">
                                {{ $order->paymentStatusLabel() }}
                            </span>
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs {{ $order->orderBadgeClass() }}">
                                {{ $order->orderStatusLabel() }}
                            </span>
                        </td>
                        <td class="p-3">
                            <a href="{{ route('orders.show', $order) }}" class="px-3 py-1 bg-amber-700 text-white rounded hover:bg-amber-800">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-gray-500">Kamu belum punya pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
@endsection