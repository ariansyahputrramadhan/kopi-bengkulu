<div class="mb-4">
    <label class="block font-medium mb-1" for="name">Nama Kategori</label>
    <input type="text" id="name" name="name"
           value="{{ old('name', $category->name ?? '') }}"
           class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror">
    @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="flex gap-2">
    <button class="px-4 py-2 bg-amber-700 text-white rounded hover:bg-amber-800">Simpan</button>
    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</a>
</div>