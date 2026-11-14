@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    @php
        $cards = [
            ['Total Produk', $stats['total_products'], 'bg-blue-500'],
            ['Total Kategori', $stats['total_categories'], 'bg-indigo-500'],
            ['Total Pelanggan', $stats['total_customers'], 'bg-teal-500'],
            ['Total Pesanan', $stats['total_orders'], 'bg-amber-500'],
            ['Menunggu Pembayaran', $stats['pending_orders'], 'bg-orange-500'],
            ['Sudah Dibayar', $stats['paid_orders'], 'bg-green-600'],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($cards as [$label, $value, $color])
            <div class="{{ $color }} text-white rounded-lg p-5 shadow">
                <div class="text-sm opacity-90">{{ $label }}</div>
                <div class="text-4xl font-bold mt-1">{{ $value }}</div>
            </div>
        @endforeach
    </div>
@endsection