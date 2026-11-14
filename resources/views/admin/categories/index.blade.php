@extends('layouts.admin')

@section('title', 'Kategori')
@section('heading', 'Manajemen Kategori')

@section('content')
    <a href="{{ route('admin.categories.create') }}" class="inline-block mb-4 px-4 py-2 bg-amber-700 text-white rounded hover:bg-amber-800">
        + Tambah Kategori
    </a>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Jumlah Produk</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr class="border-t">
                        <td class="p-3">{{ $categories->firstItem() + $loop->index }}</td>
                        <td class="p-3">{{ $category->name }}</td>
                        <td class="p-3">{{ $category->products_count }}</td>
                        <td class="p-3 flex gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="px-3 py-1 bg-blue-600 text-white rounded">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                  onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 bg-red-600 text-white rounded">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-4 text-center text-gray-500">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>
@endsection