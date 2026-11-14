<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $items = auth()->user()
            ->cartItems()
            ->with('product.category')
            ->get();

        $total = $items->sum(fn ($item) => $item->product->price * $item->quantity);

        // Ada item yang jumlahnya melebihi stok saat ini?
        $hasStockProblem = $items->contains(fn ($item) => $item->quantity > $item->product->stock);

        return view('cart.index', compact('items', 'total', 'hasStockProblem'));
    }

    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $qty = $data['quantity'] ?? 1;

        if ($product->stock < 1) {
            return back()->with('error', 'Maaf, stok produk ini habis.');
        }

        $item = CartItem::firstOrNew([
            'user_id'    => auth()->id(),
            'product_id' => $product->id,
        ]);

        $newQty = ($item->exists ? $item->quantity : 0) + $qty;

        if ($newQty > $product->stock) {
            return back()->with('error', "Stok {$product->name} hanya tersisa {$product->stock}. Jumlah di keranjang kamu: " . ($item->exists ? $item->quantity : 0) . '.');
        }

        $item->quantity = $newQty;
        $item->save();

        return redirect()->route('cart.index')
            ->with('success', "{$product->name} ditambahkan ke keranjang.");
    }

    public function update(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === auth()->id(), 403);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $stock = $cartItem->product->stock;

        if ($data['quantity'] > $stock) {
            return back()->with('error', "Stok {$cartItem->product->name} hanya tersisa {$stock}.");
        }

        $cartItem->update(['quantity' => $data['quantity']]);

        return back()->with('success', 'Jumlah produk diperbarui.');
    }

    public function destroy(CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === auth()->id(), 403);

        $cartItem->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}