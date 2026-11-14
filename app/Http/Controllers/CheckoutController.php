<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        abort_if(auth()->user()->isAdmin(), 403, 'Akun admin tidak dapat berbelanja.');

        $items = auth()->user()->cartItems()->with('product')->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kamu masih kosong.');
        }

        if ($items->contains(fn ($item) => $item->quantity > $item->product->stock)) {
            return redirect()->route('cart.index')
                ->with('error', 'Ada produk yang melebihi stok. Perbaiki dulu jumlahnya.');
        }

        $total = $items->sum(fn ($item) => $item->product->price * $item->quantity);

        return view('checkout.index', compact('items', 'total'));
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->isAdmin(), 403, 'Akun admin tidak dapat berbelanja.');

        // Perhatikan: total TIDAK diterima dari browser
        $data = $request->validate([
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone'          => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'address'        => ['required', 'string', 'max:500'],
            'city'           => ['required', 'string', 'max:100'],
            'postal_code'    => ['required', 'digits:5'],
        ], [
            'phone.regex'        => 'Nomor HP tidak valid. Contoh: 081234567890.',
            'postal_code.digits' => 'Kode pos harus 5 digit angka.',
        ]);

        $user = auth()->user();

        try {
            $order = DB::transaction(function () use ($user, $data) {
                $cartItems = CartItem::where('user_id', $user->id)->get();

                if ($cartItems->isEmpty()) {
                    throw new \RuntimeException('Keranjang kamu masih kosong.');
                }

                // Kunci baris produk agar stok tidak diubah proses lain selama transaksi
                $products = Product::whereIn('id', $cartItems->pluck('product_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $total = 0;
                $lines = [];

                foreach ($cartItems as $item) {
                    $product = $products[$item->product_id];

                    if ($item->quantity > $product->stock) {
                        throw new \RuntimeException(
                            "Stok {$product->name} tidak cukup (tersisa {$product->stock})."
                        );
                    }

                    // Harga diambil dari database, bukan dari browser
                    $subtotal = $product->price * $item->quantity;
                    $total += $subtotal;

                    $lines[] = [
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'price'        => $product->price,
                        'quantity'     => $item->quantity,
                        'subtotal'     => $subtotal,
                    ];

                    $product->decrement('stock', $item->quantity);
                }

                $order = Order::create([
                    'user_id'        => $user->id,
                    'order_number'   => $this->generateOrderNumber(),
                    'recipient_name' => $data['recipient_name'],
                    'phone'          => $data['phone'],
                    'address'        => $data['address'],
                    'city'           => $data['city'],
                    'postal_code'    => $data['postal_code'],
                    'total_amount'   => $total,
                    'payment_status' => 'pending',
                    'order_status'   => 'pending',
                ]);

                $order->items()->createMany($lines);

                CartItem::where('user_id', $user->id)->delete();

                return $order;
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat. Silakan lanjutkan ke pembayaran.');
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'INV-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}