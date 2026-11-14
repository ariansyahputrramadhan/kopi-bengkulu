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
                    @if ($order->payment_type)
                        <div><dt class="text-gray-500">Metode Pembayaran</dt><dd>{{ str_replace('_', ' ', ucfirst($order->payment_type)) }}</dd></div>
                    @endif
                    @if ($order->paid_at)
                        <div><dt class="text-gray-500">Dibayar pada</dt><dd>{{ $order->paid_at->format('d M Y H:i') }}</dd></div>
                    @endif
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

            {{-- Pembayaran --}}
            @if ($order->payment_status === 'pending')
                <div class="bg-amber-50 border border-amber-200 rounded p-5 text-sm space-y-3">
                    <p>Selesaikan pembayaran sebesar
                        <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                        melalui Midtrans.</p>

                    <button id="pay-button" type="button"
                            class="w-full px-5 py-3 bg-amber-700 text-white rounded hover:bg-amber-800 disabled:opacity-50">
                        Bayar Sekarang
                    </button>
                    <p id="pay-error" class="text-red-600 hidden"></p>

                    <form id="check-form" method="POST" action="{{ route('payment.check', $order) }}">
                        @csrf
                        <button class="w-full px-5 py-2 bg-white border rounded hover:bg-gray-50">
                            Cek Status Pembayaran
                        </button>
                    </form>
                </div>
            @elseif ($order->payment_status === 'paid')
                <div class="bg-green-50 border border-green-200 text-green-800 rounded p-5 text-sm">
                    Pembayaran berhasil diterima. Terima kasih!
                </div>
            @elseif ($order->payment_status === 'expired')
                <div class="bg-red-50 border border-red-200 text-red-800 rounded p-5 text-sm">
                    Waktu pembayaran habis dan pesanan dibatalkan. Silakan buat pesanan baru.
                </div>
            @else
                <div class="bg-red-50 border border-red-200 text-red-800 rounded p-5 text-sm">
                    Pembayaran gagal atau dibatalkan, dan pesanan dibatalkan. Silakan buat pesanan baru.
                </div>
            @endif
        </aside>
    </div>

    @if ($order->payment_status === 'pending')
        <script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script>
            const payButton = document.getElementById('pay-button');
            const payError  = document.getElementById('pay-error');
            const checkForm = document.getElementById('check-form');

            payButton.addEventListener('click', async () => {
                payButton.disabled = true;
                payError.classList.add('hidden');

                try {
                    const res = await fetch(@json(route('payment.token', $order)), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();

                    if (!res.ok) {
                        throw new Error(data.message || 'Terjadi kesalahan.');
                    }

                    window.snap.pay(data.token, {
                        onSuccess: () => checkForm.submit(),
                        onPending: () => checkForm.submit(),
                        onError:   () => checkForm.submit(),
                        onClose:   () => { payButton.disabled = false; },
                    });
                } catch (e) {
                    payError.textContent = e.message;
                    payError.classList.remove('hidden');
                    payButton.disabled = false;
                }
            });
        </script>
    @endif
@endsection