@extends('layouts.shop')

@section('title', 'Keranjang')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Keranjang Belanja</h1>

    @if ($items->isEmpty())
        <div class="bg-white rounded shadow p-10 text-center">
            <p class="text-gray-500 mb-4">Keranjang kamu masih kosong.</p>
            <a href="{{ route('shop.index') }}" class="inline-block px-5 py-2 bg-amber-700 text-white rounded hover:bg-amber-800">
                Mulai Belanja
            </a>
        </div>
    @else
        @if ($hasStockProblem)
            <div class="mb-4 p-3 rounded bg-yellow-100 text-yellow-800">
                Ada produk yang jumlahnya melebihi stok saat ini. Kurangi jumlahnya sebelum checkout.
            </div>
        @endif

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-3">Produk</th>
                        <th class="p-3">Harga</th>
                        <th class="p-3">Jumlah</th>
                        <th class="p-3">Subtotal</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        @php $product = $item->product; @endphp
                        <tr class="border-t align-middle">
                            <td class="p-3">
                                <div class="flex items-center gap-3 min-w-48">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                         class="w-16 h-16 object-cover rounded">
                                    <div>
                                        <a href="{{ route('shop.show', $product) }}" class="font-semibold hover:underline">
                                            {{ $product->name }}
                                        </a>
                                        <div class="text-xs text-gray-500">Stok: {{ $product->stock }}</div>
                                        @if ($item->quantity > $product->stock)
                                            <div class="text-xs text-red-600 font-semibold">Melebihi stok!</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="p-3 whitespace-nowrap">Rp {{ number_format($product->price, 0, ',', '.') }}</td>

                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    {{-- Kurangi --}}
                                    <form method="POST" action="{{ route('cart.update', $item) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}">
                                        <button class="w-8 h-8 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-40"
                                                @disabled($item->quantity <= 1)>-</button>
                                    </form>

                                    <span class="w-8 text-center">{{ $item->quantity }}</span>

                                    {{-- Tambah --}}
                                    <form method="POST" action="{{ route('cart.update', $item) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                        <button class="w-8 h-8 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-40"
                                                @disabled($item->quantity >= $product->stock)>+</button>
                                    </form>
                                </div>
                            </td>

                            <td class="p-3 whitespace-nowrap font-semibold">
                                Rp {{ number_format($product->price * $item->quantity, 0, ',', '.') }}
                            </td>

                            <td class="p-3">
                                <form method="POST" action="{{ route('cart.destroy', $item) }}"
                                      onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Total --}}
        <div class="mt-4 bg-white rounded shadow p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="text-sm text-gray-500">Total Belanja</div>
                <div class="text-3xl font-bold text-amber-900">Rp {{ number_format($total, 0, ',', '.') }}</div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('shop.index') }}" class="px-5 py-3 bg-gray-300 rounded hover:bg-gray-400">Lanjut Belanja</a>
                {{-- Tombol checkout diaktifkan di Tahap 6 --}}
                <button disabled class="px-5 py-3 bg-gray-300 text-gray-500 rounded cursor-not-allowed">
                    Checkout (segera hadir)
                </button>
            </div>
        </div>
    @endif
@endsection