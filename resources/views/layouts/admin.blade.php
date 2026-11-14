<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800">
<div class="min-h-screen md:flex">

    {{-- Sidebar --}}
    <aside class="bg-amber-900 text-white md:w-60 md:min-h-screen p-4">
        <div class="text-xl font-bold mb-6">{{ config('app.name') }}<br><span class="text-sm font-normal text-amber-200">Panel Admin</span></div>

        @php
            $link = fn ($pattern) => request()->routeIs($pattern)
                ? 'block px-3 py-2 rounded bg-amber-700'
                : 'block px-3 py-2 rounded hover:bg-amber-800';
        @endphp

        <nav class="space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="{{ $link('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.categories.index') }}" class="{{ $link('admin.categories.*') }}">Kategori</a>
            <a href="{{ route('admin.products.index') }}" class="{{ $link('admin.products.*') }}">Produk</a>
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="mt-8">
            @csrf
            <button class="w-full text-left px-3 py-2 rounded bg-red-600 hover:bg-red-700">Logout</button>
        </form>
    </aside>

    {{-- Konten --}}
    <main class="flex-1 p-4 md:p-8">
        <h1 class="text-2xl font-bold mb-6">@yield('heading')</h1>

        @if (session('success'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>