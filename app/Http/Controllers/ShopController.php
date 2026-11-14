<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function home()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        $products = Product::with('category')->latest()->take(8)->get();

        return view('shop.home', compact('categories', 'products'));
    }

    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $query = Product::with('category')
            ->when($request->q, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->category, fn ($q, $slug) => $q->whereHas(
                'category',
                fn ($c) => $c->where('slug', $slug)
            ));

        match ($request->sort) {
            'termurah' => $query->orderBy('price'),
            'termahal' => $query->orderByDesc('price'),
            'nama'     => $query->orderBy('name'),
            default    => $query->latest(),
        };

        $products = $query->paginate(9)->withQueryString();

        return view('shop.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load('category');

        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'related'));
    }
}