@extends('layouts.shop')

@section('title', 'Produk')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Katalog Produk</h1>

    {{-- Pencarian, filter, urutkan --}}
    <form method="GET" action="{{ route('shop.index') }}"
          class="bg-white rounded shadow p-4 mb-6 grid grid-cols-1 md:grid-cols-5 gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..."
               class="border rounded px-3 py-2 md:col-span-2">

        <select name="category" class="border rounded px-3 py-2">
            <option value="">Semua kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <select name="sort" class="border rounded px-3 py-2">
            <option value="">Terbaru</option>
            <option value="termurah" @selected(request('sort') === 'termurah')>Harga termurah</option>
            <option value="termahal" @selected(request('sort') === 'termahal')>Harga termahal</option>
            <option value="nama" @selected(request('sort') === 'nama')>Nama A-Z</option>
        </select>

        <div class="flex gap-2">
            <button class="flex-1 px-4 py-2 bg-amber-700 text-white rounded hover:bg-amber-800">Terapkan</button>
            <a href="{{ route('shop.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Reset</a>
        </div>
    </form>

    {{-- Daftar produk --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($products as $product)
            @include('shop._product-card', ['product' => $product])
        @empty
            <p class="col-span-full text-center text-gray-500 py-10">Produk tidak ditemukan.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
@endsection