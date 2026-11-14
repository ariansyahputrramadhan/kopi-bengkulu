<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products'   => Product::count(),
            'total_categories' => Category::count(),
            'total_customers'  => User::where('role', 'customer')->count(),
            'total_orders'     => Order::count(),
            'pending_orders'   => Order::where('payment_status', 'pending')->count(),
            'paid_orders'      => Order::where('payment_status', 'paid')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}