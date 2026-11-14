<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 text-gray-800 flex flex-col min-h-screen">

    {{-- Navbar --}}
    <header class="bg-amber-900 text-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="text-xl font-bold">{{ config('app.name') }}</a>

            <nav class="flex flex-wrap items-center gap-4 text-sm">
                <a href="{{ route('home') }}" class="hover:text-amber-200">Beranda</a>
                <a href="{{ route('shop.index') }}" class="hover:text-amber-200">Produk</a>

                @guest
                    <a href="{{ route('login') }}" class="hover:text-amber-200">Login</a>
                    <a href="{{ route('register') }}" class="px-3 py-1 bg-amber-600 rounded hover:bg-amber-500">Daftar</a>
                @endguest

                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-amber-200">Panel Admin</a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="hover:text-amber-200">{{ auth()->user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="px-3 py-1 bg-red-600 rounded hover:bg-red-700">Logout</button>
                    </form>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Konten --}}
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 py-6">
        @if (session('success'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-amber-950 text-amber-100 text-center text-sm py-4">
        &copy; {{ date('Y') }} {{ config('app.name') }}. Tugas Besar Praktikum Pemrograman E-Commerce.
    </footer>
</body>
</html>