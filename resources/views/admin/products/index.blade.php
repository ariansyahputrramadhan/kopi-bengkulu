@extends('layouts.admin')

@section('title', 'Produk')
@section('heading', 'Manajemen Produk')

@section('content')
    <div class="flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between mb-4">
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-amber-700 text-white rounded hover:bg-amber-800 text-center">
            + Tambah Produk
        </a>

        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..."
                   class="border rounded px-3 py-2">
            <button class="px-4 py-2 bg-gray-700 text-white rounded">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3">Gambar</th>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Kategori</th>
                    <th class="p-3">Harga</th>
                    <th class="p-3">Stok</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="border-t">
                        <td class="p-3">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-14 h-14 object-cover rounded">
                        </td>
                        <td class="p-3">{{ $product->name }}</td>
                        <td class="p-3">{{ $product->category->name }}</td>
                        <td class="p-3">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="p-3">{{ $product->stock }}</td>
                        <td class="p-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="px-3 py-1 bg-blue-600 text-white rounded">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                      onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1 bg-red-600 text-white rounded">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-4 text-center text-gray-500">Belum ada produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
@endsection