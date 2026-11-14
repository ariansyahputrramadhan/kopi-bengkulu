@extends('layouts.shop')

@section('title', 'Checkout')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Checkout</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form penerima --}}
        <form method="POST" action="{{ route('checkout.store') }}"
              class="lg:col-span-2 bg-white rounded shadow p-6">
            @csrf

            <h2 class="text-lg font-bold mb-4">Data Penerima & Alamat Pengiriman</h2>

            <div class="mb-4">
                <label class="block font-medium mb-1" for="recipient_name">Nama Penerima</label>
                <input type="text" id="recipient_name" name="recipient_name"
                       value="{{ old('recipient_name', auth()->user()->name) }}"
                       class="w-full border rounded px-3 py-2 @error('recipient_name') border-red-500 @enderror">
                @error('recipient_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-1" for="phone">Nomor HP</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="081234567890"
                       class="w-full border rounded px-3 py-2 @error('phone') border-red-500 @enderror">
                @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-1" for="address">Alamat Lengkap</label>
                <textarea id="address" name="address" rows="3"
                          class="w-full border rounded px-3 py-2 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                @error('address') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block font-medium mb-1" for="city">Kota</label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}"
                           class="w-full border rounded px-3 py-2 @error('city') border-red-500 @enderror">
                    @error('city') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-medium mb-1" for="postal_code">Kode Pos</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" maxlength="5"
                           class="w-full border rounded px-3 py-2 @error('postal_code') border-red-500 @enderror">
                    @error('postal_code') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-2">
                <button class="px-6 py-3 bg-amber-700 text-white rounded hover:bg-amber-800">Buat Pesanan</button>
                <a href="{{ route('cart.index') }}" class="px-6 py-3 bg-gray-300 rounded hover:bg-gray-400">Kembali ke Keranjang</a>
            </div>
        </form>

        {{-- Ringkasan pesanan --}}
        <aside class="bg-white rounded shadow p-6 h-fit">
            <h2 class="text-lg font-bold mb-4">Ringkasan Pesanan</h2>

            <ul class="divide-y">
                @foreach ($items as $item)
                    <li class="py-3 flex justify-between gap-3 text-sm">
                        <div>
                            <div class="font-medium">{{ $item->product->name }}</div>
                            <div class="text-gray-500">
                                {{ $item->quantity }} x Rp {{ number_format($item->product->price, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="font-semibold whitespace-nowrap">
                            Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="border-t mt-2 pt-4 flex justify-between items-center">
                <span class="font-medium">Total</span>
                <span class="text-2xl font-bold text-amber-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <p class="text-xs text-gray-500 mt-3">Total dihitung ulang oleh server saat pesanan dibuat.</p>
        </aside>
    </div>
@endsection