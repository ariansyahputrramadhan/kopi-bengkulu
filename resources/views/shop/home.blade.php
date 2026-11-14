@extends('layouts.shop')

@section('title', 'Beranda')

@section('content')
    {{-- Hero --}}
    <section class="bg-amber-800 text-white rounded-lg p-8 md:p-12 mb-8">
        <h1 class="text-3xl md:text-4xl font-bold">Kopi Pilihan Khas Bengkulu</h1>
        <p class="mt-2 text-amber-100 max-w-xl">Biji kopi segar, kopi bubuk, dan peralatan seduh untuk menemani harimu.</p>
        <a href="{{ route('shop.index') }}" class="inline-block mt-5 px-5 py-2 bg-white text-amber-900 font-semibold rounded hover:bg-amber-100">
            Belanja Sekarang
        </a>
    </section>

    {{-- Kategori --}}
    <section class="mb-8">
        <h2 class="text-xl font-bold mb-3">Kategori</h2>
        <div class="flex flex-wrap gap-2">
            @foreach ($categories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                   class="px-4 py-2 bg-white rounded-full shadow text-sm hover:bg-amber-100">
                    {{ $category->name }} ({{ $category->products_count }})
                </a>
            @endforeach
        </div>
    </section>

    {{-- Produk terbaru --}}
    <section>
        <h2 class="text-xl font-bold mb-3">Produk Terbaru</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse ($products as $product)
                @include('shop._product-card', ['product' => $product])
            @empty
                <p class="text-gray-500">Belum ada produk.</p>
            @endforelse
        </div>
    </section>
@endsection