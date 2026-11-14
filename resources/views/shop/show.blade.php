@extends('layouts.shop')

@section('title', $product->name)

@section('content')
    <nav class="text-sm text-gray-500 mb-4">
        <a href="{{ route('home') }}" class="hover:underline">Beranda</a> /
        <a href="{{ route('shop.index') }}" class="hover:underline">Produk</a> /
        <span>{{ $product->name }}</span>
    </nav>

    <div class="bg-white rounded-lg shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
             class="w-full rounded-lg object-cover max-h-96">

        <div>
            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}"
               class="text-sm text-amber-700 font-medium hover:underline">{{ $product->category->name }}</a>

            <h1 class="text-3xl font-bold mt-1">{{ $product->name }}</h1>

            <p class="text-3xl font-bold text-amber-900 mt-3">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </p>

            <p class="mt-3">
                @if ($product->stock > 0)
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">Stok tersedia: {{ $product->stock }}</span>
                @else
                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">Stok habis</span>
                @endif
            </p>

            <div class="mt-5 text-gray-700 leading-relaxed">
                {!! nl2br(e($product->description ?: 'Belum ada deskripsi.')) !!}
            </div>

            {{-- Tombol keranjang --}}
            <div class="mt-6">
                @guest
                    <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-amber-700 text-white rounded hover:bg-amber-800">
                        Login untuk membeli
                    </a>
                @endguest

                @auth
                    @if (auth()->user()->isAdmin())
                        <p class="text-sm text-gray-500">Akun admin tidak dapat berbelanja.</p>
                    @elseif ($product->stock < 1)
                        <button disabled class="px-6 py-3 bg-gray-300 text-gray-500 rounded cursor-not-allowed">
                            Stok Habis
                        </button>
                    @else
                        <form method="POST" action="{{ route('cart.store', $product) }}" class="flex items-center gap-3">
                            @csrf
                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                                   class="w-20 border rounded px-3 py-2">
                            <button class="px-6 py-3 bg-amber-700 text-white rounded hover:bg-amber-800">
                                Tambah ke Keranjang
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    @if ($related->isNotEmpty())
        <section class="mt-8">
            <h2 class="text-xl font-bold mb-3">Produk Terkait</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($related as $item)
                    @include('shop._product-card', ['product' => $item->setRelation('category', $product->category)])
                @endforeach
            </div>
        </section>
    @endif
@endsection