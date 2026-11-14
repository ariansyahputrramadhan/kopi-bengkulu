@php $p = $product ?? null; @endphp

<div class="mb-4">
    <label class="block font-medium mb-1" for="category_id">Kategori</label>
    <select id="category_id" name="category_id" class="w-full border rounded px-3 py-2 @error('category_id') border-red-500 @enderror">
        <option value="">-- Pilih kategori --</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $p->category_id ?? '') == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block font-medium mb-1" for="name">Nama Produk</label>
    <input type="text" id="name" name="name" value="{{ old('name', $p->name ?? '') }}"
           class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror">
    @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block font-medium mb-1" for="description">Deskripsi</label>
    <textarea id="description" name="description" rows="4"
              class="w-full border rounded px-3 py-2 @error('description') border-red-500 @enderror">{{ old('description', $p->description ?? '') }}</textarea>
    @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block font-medium mb-1" for="price">Harga (Rp)</label>
        <input type="number" id="price" name="price" min="0" value="{{ old('price', $p->price ?? '') }}"
               class="w-full border rounded px-3 py-2 @error('price') border-red-500 @enderror">
        @error('price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block font-medium mb-1" for="stock">Stok</label>
        <input type="number" id="stock" name="stock" min="0" value="{{ old('stock', $p->stock ?? 0) }}"
               class="w-full border rounded px-3 py-2 @error('stock') border-red-500 @enderror">
        @error('stock') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mb-6">
    <label class="block font-medium mb-1" for="image">Gambar Produk (maks 2 MB)</label>
    @if ($p && $p->image)
        <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-32 h-32 object-cover rounded mb-2">
    @endif
    <input type="file" id="image" name="image" accept="image/*"
           class="w-full border rounded px-3 py-2 @error('image') border-red-500 @enderror">
    @error('image') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="flex gap-2">
    <button class="px-4 py-2 bg-amber-700 text-white rounded hover:bg-amber-800">Simpan</button>
    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</a>
</div>