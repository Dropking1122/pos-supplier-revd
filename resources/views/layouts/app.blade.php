<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $setting->company_name ?? config('app.name') }} - POS Supplier</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans">
<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-indigo-900 text-white transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0">
        <div class="flex items-center justify-between p-4 border-b border-indigo-700">
            <div>
                <h1 class="text-lg font-bold">🏪 POS Supplier</h1>
                <p class="text-xs text-indigo-300 truncate">{{ $setting->company_name ?? 'Toko Saya' }}</p>
            </div>
            <button @click="sidebarOpen=false" class="md:hidden text-indigo-300 hover:text-white">✕</button>
        </div>
        <nav class="p-3 space-y-1 overflow-y-auto h-full pb-20">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800' }}">
                <span>📊</span> Dashboard
            </a>
            <div class="pt-2 pb-1 text-xs text-indigo-400 uppercase tracking-wider px-3">Barang</div>
            <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('products.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800' }}">
                <span>📦</span> Data Barang
            </a>
            <div class="pt-2 pb-1 text-xs text-indigo-400 uppercase tracking-wider px-3">Transaksi</div>
            <a href="{{ route('sales.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('sales.index') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800' }}">
                <span>🧾</span> Riwayat Penjualan
            </a>
            <a href="{{ route('sales.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('sales.create') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800' }}">
                <span>➕</span> Transaksi Baru
            </a>
            <div class="pt-2 pb-1 text-xs text-indigo-400 uppercase tracking-wider px-3">Customer</div>
            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('customers.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800' }}">
                <span>👥</span> Data Customer
            </a>
            <a href="{{ route('debts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('debts.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800' }}">
                <span>💳</span> Hutang Customer
            </a>
            <div class="pt-2 pb-1 text-xs text-indigo-400 uppercase tracking-wider px-3">Laporan</div>
            <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800' }}">
                <span>📈</span> Laporan Penjualan
            </a>
            <div class="pt-2 pb-1 text-xs text-indigo-400 uppercase tracking-wider px-3">Pengaturan</div>
            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('settings.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800' }}">
                <span>⚙️</span> Pengaturan Toko
            </a>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-indigo-200 hover:bg-indigo-800">
                <span>👤</span> Profil
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-red-300 hover:bg-indigo-800 text-left">
                    <span>🚪</span> Keluar
                </button>
            </form>
        </nav>
    </aside>
    <!-- Overlay mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen=false" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"></div>
    <!-- Main -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm z-30 flex items-center justify-between px-4 py-3">
            <button @click="sidebarOpen=true" class="md:hidden text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h2 class="text-lg font-semibold text-gray-700">{{ $header ?? 'Dashboard' }}</h2>
            <div class="text-sm text-gray-500">{{ auth()->user()->name }}</div>
        </header>
        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            @if(session('message'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('message') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>
@livewireScripts
</body>
</html>
