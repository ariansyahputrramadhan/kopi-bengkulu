<a href="{{ route('shop.show', $product) }}"
   class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden flex flex-col">
    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
    <div class="p-4 flex-1 flex flex-col">
        <span class="text-xs text-amber-700 font-medium">{{ $product->category->name }}</span>
        <h3 class="font-semibold mt-1 flex-1">{{ $product->name }}</h3>
        <div class="mt-3 flex items-center justify-between">
            <span class="font-bold text-amber-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
            @if ($product->stock > 0)
                <span class="text-xs text-green-700">Stok {{ $product->stock }}</span>
            @else
                <span class="text-xs text-red-600 font-semibold">Habis</span>
            @endif
        </div>
    </div>
</a>