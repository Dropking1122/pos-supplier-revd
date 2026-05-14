<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    {{-- Anti-flicker: apply dark class BEFORE CSS loads --}}
    <script>try{const t=localStorage.getItem('theme');if(t==='dark'||(!t&&window.matchMedia('(prefers-color-scheme: dark)').matches)){document.documentElement.classList.add('dark');}}catch(e){}</script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $setting->company_name ?? config('app.name') }} - POS Supplier</title>
    <meta name="description" content="Sistem Point of Sale untuk {{ $setting->company_name ?? config('app.name') }}. Kelola penjualan, stok produk, dan laporan bisnis.">
    <meta name="robots" content="noindex, nofollow">

    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#4f46e5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ $setting->company_name ?? config('app.name') }}">
    <meta name="application-name" content="{{ $setting->company_name ?? config('app.name') }}">
    <meta name="msapplication-TileColor" content="#4f46e5">
    <meta name="msapplication-tap-highlight" content="no">

    {{-- PWA Manifest & Icons --}}
    <link rel="manifest" href="/manifest.json">
    @if($setting->company_logo)
    <link rel="icon" href="{{ asset($setting->company_logo) }}">
    <link rel="apple-touch-icon" href="{{ asset($setting->company_logo) }}">
    @else
    <link rel="icon" type="image/png" href="/pwa/icon/192">
    <link rel="apple-touch-icon" href="/pwa/icon/192">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-100 dark:bg-slate-900 font-sans">
<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex flex-col">

        <!-- Brand -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-700/60">
            <div class="flex items-center gap-3">
                @if($setting->company_logo)
                <img src="{{ asset($setting->company_logo) }}" alt="Logo" class="w-9 h-9 rounded-lg object-contain bg-white shrink-0">
                @else
                <div class="w-9 h-9 bg-indigo-500 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                @endif
                <div>
                    <h1 class="text-sm font-bold leading-tight truncate max-w-[130px]">{{ $setting->company_name ?? 'Toko Saya' }}</h1>
                    <p class="text-xs text-slate-400">POS System</p>
                </div>
            </div>
            <button @click="sidebarOpen=false" class="md:hidden text-slate-400 hover:text-white p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            @if(auth()->user()->is_admin)
            <div class="pt-4 pb-1.5 px-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Barang</span>
            </div>

            <a href="{{ route('products.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('products.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Data Barang
            </a>
            @endif

            <div class="pt-4 pb-1.5 px-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Transaksi</span>
            </div>

            <a href="{{ route('sales.create') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('sales.create') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Transaksi Baru
            </a>

            <a href="{{ route('sales.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('sales.index') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Riwayat Penjualan
            </a>

            @if(auth()->user()->is_admin)
            <div class="pt-4 pb-1.5 px-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Customer</span>
            </div>

            <a href="{{ route('customers.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('customers.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Data Customer
            </a>

            <a href="{{ route('debts.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('debts.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Hutang Customer
            </a>

            <div class="pt-4 pb-1.5 px-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Laporan</span>
            </div>

            <a href="{{ route('reports.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('reports.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Laporan Penjualan
            </a>
            @endif

            <div class="pt-4 pb-1.5 px-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Akun</span>
            </div>

            @if(auth()->user()->is_admin)
            <a href="{{ route('users.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Manajemen User
            </a>

            <a href="{{ route('backup.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('backup.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                Backup & Restore
            </a>

            <a href="{{ route('settings.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('settings.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pengaturan Toko
            </a>
            @endif

            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
               {{ request()->routeIs('profile.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Profil Akun
            </a>
        </nav>

        <!-- Logout -->
        <div class="px-3 py-3 border-t border-slate-700/60">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-red-600/20 hover:text-red-400 transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen=false"
         class="fixed inset-0 bg-black/50 z-40 md:hidden" x-transition.opacity></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Topbar -->
        <header class="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 z-30 flex items-center justify-between px-4 py-3 md:px-6">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen=true" class="md:hidden text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h2 class="text-base font-semibold text-gray-700 dark:text-slate-200">{{ $header ?? 'Dashboard' }}</h2>
            </div>
            <div class="flex items-center gap-1.5">
                {{-- Dark Mode Toggle --}}
                <div x-data="{ dark: document.documentElement.classList.contains('dark') }">
                    <button @click="
                            dark = !dark;
                            document.documentElement.classList.toggle('dark', dark);
                            localStorage.setItem('theme', dark ? 'dark' : 'light');
                        "
                        :title="dark ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'"
                        class="p-2 rounded-xl text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        {{-- Moon icon — shown in light mode --}}
                        <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        {{-- Sun icon — shown in dark mode --}}
                        <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                </div>

                <livewire:notification-bell />
                <div class="w-px h-5 bg-gray-200 mx-1 hidden sm:block"></div>

                {{-- User Dropdown --}}
                <div x-data="{ userOpen: false }" class="relative">
                    <button @click="userOpen = !userOpen" @keydown.escape.window="userOpen = false"
                            class="flex items-center gap-2 rounded-xl px-2 py-1.5 hover:bg-gray-100 transition-colors focus:outline-none">
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="hidden sm:flex items-center gap-1.5">
                            <span class="text-sm text-gray-600 font-medium">{{ auth()->user()->name }}</span>
                            @if(auth()->user()->is_admin)
                            <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded-full leading-none">ADMIN</span>
                            @else
                            <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full leading-none">KASIR</span>
                            @endif
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-400 hidden sm:block transition-transform duration-200" :class="userOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Dropdown Panel --}}
                    <div x-show="userOpen"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         @click.outside="userOpen = false"
                         class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-700 z-50 overflow-hidden"
                         style="display:none;">

                        {{-- User Info --}}
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-slate-700">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 {{ auth()->user()->is_admin ? 'bg-indigo-100' : 'bg-emerald-100' }} rounded-full flex items-center justify-center shrink-0">
                                    <span class="{{ auth()->user()->is_admin ? 'text-indigo-600' : 'text-emerald-600' }} text-sm font-bold">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                    <span class="mt-0.5 inline-block px-1.5 py-0.5 {{ auth()->user()->is_admin ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }} text-[10px] font-bold rounded-full leading-none">
                                        {{ auth()->user()->is_admin ? 'Admin' : 'Kasir' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Menu Items --}}
                        <div class="p-1.5 space-y-0.5">
                            <a href="{{ route('profile.edit') }}" @click="userOpen = false"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                <svg class="w-4 h-4 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Profil Akun
                            </a>

                            <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-slate-800 border-t border-gray-200 dark:border-slate-700 px-4 md:px-6 py-2.5 flex items-center justify-center shrink-0">
            <p class="text-xs text-gray-400">Powered by <a href="https://revdstore.app" target="_blank" class="text-indigo-500 hover:text-indigo-700 font-medium transition-colors">revdstore.app</a></p>
        </footer>
    </div>
</div>

{{-- PWA Install Prompt Banner --}}
<div
    x-data="{
        show: false,
        deferredPrompt: null,
        installing: false,
        init() {
            if (window.matchMedia('(display-mode: standalone)').matches) return;
            window.addEventListener('beforeinstallprompt', e => {
                e.preventDefault();
                this.deferredPrompt = e;
                setTimeout(() => { this.show = true; }, 3000);
            });
            window.addEventListener('appinstalled', () => {
                this.show = false;
                sessionStorage.setItem('pwa-shown', '1');
            });
        },
        async install() {
            if (!this.deferredPrompt) return;
            this.installing = true;
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            this.deferredPrompt = null;
            this.installing = false;
            this.show = false;
            sessionStorage.setItem('pwa-shown', '1');
        },
        dismiss(permanent) {
            this.show = false;
            sessionStorage.setItem('pwa-shown', '1');
            if (permanent) localStorage.setItem('pwa-dismissed', Date.now());
        }
    }"
    x-init="
        if (sessionStorage.getItem('pwa-shown')) return;
        const ts = localStorage.getItem('pwa-dismissed');
        if (ts && (Date.now() - parseInt(ts)) < 7 * 24 * 60 * 60 * 1000) return;
        init();
    "
    x-show="show"
    x-transition:enter="transition ease-out duration-400"
    x-transition:enter-start="opacity-0 translate-y-6"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-6"
    class="fixed bottom-4 left-3 right-3 md:left-auto md:right-5 md:w-[360px] z-[9998]"
    style="display:none;"
>
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">

        {{-- Header strip --}}
        <div class="bg-indigo-600 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                {{-- Logo toko atau fallback icon --}}
                @if($setting->company_logo)
                <img src="{{ asset($setting->company_logo) }}"
                     alt="{{ $setting->company_name }}"
                     class="w-9 h-9 rounded-xl object-contain bg-white/10 p-0.5 shrink-0">
                @else
                <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                @endif
                <div>
                    <p class="text-sm font-bold text-white leading-none">Pasang di HP kamu</p>
                    <p class="text-[11px] text-indigo-200 mt-0.5">Gratis · Tidak perlu Play Store</p>
                </div>
            </div>
            {{-- Tombol tutup dengan dropdown opsi --}}
            <div x-data="{ closeMenu: false }" class="relative">
                <button @click="closeMenu = !closeMenu"
                        class="text-white/60 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors"
                        title="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div x-show="closeMenu"
                     @click.outside="closeMenu = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute right-0 top-7 w-48 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-10"
                     style="display:none;">
                    <button @click="dismiss(false); closeMenu = false"
                            class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                        Sembunyikan dulu
                    </button>
                    <div class="border-t border-gray-100"></div>
                    <button @click="dismiss(true); closeMenu = false"
                            class="w-full text-left px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Jangan tampilkan lagi
                    </button>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="px-4 py-3">
            {{-- Benefit list --}}
            <ul class="space-y-2 mb-4">
                <li class="flex items-center gap-2.5 text-sm text-gray-700">
                    <span class="w-5 h-5 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    Buka langsung dari layar utama HP
                </li>
                <li class="flex items-center gap-2.5 text-sm text-gray-700">
                    <span class="w-5 h-5 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    Tampilan penuh tanpa bar browser
                </li>
                <li class="flex items-center gap-2.5 text-sm text-gray-700">
                    <span class="w-5 h-5 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    Lebih cepat &amp; bisa dipakai saat sinyal lemah
                </li>
            </ul>

            {{-- Action buttons --}}
            <div class="flex gap-2">
                <button @click="install()" :disabled="installing"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-semibold rounded-xl transition-colors">
                    <svg x-show="!installing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <svg x-show="installing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span x-text="installing ? 'Memasang...' : 'Pasang Sekarang'">Pasang Sekarang</span>
                </button>
                <button @click="dismiss()"
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">
                    Nanti
                </button>
            </div>
        </div>
    </div>
</div>

@livewireScripts

{{-- Service Worker Registration --}}
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', { scope: '/' })
            .then(reg => {
                reg.addEventListener('updatefound', () => {
                    const newWorker = reg.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: {
                                    type: 'info',
                                    title: 'Update Tersedia',
                                    message: 'Muat ulang halaman untuk mendapatkan versi terbaru.',
                                    duration: 6000
                                }
                            }));
                        }
                    });
                });
            })
            .catch(() => {});
    });
}
</script>

@if(session('toast_success') || session('message'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: @js(session('toast_success') ?? session('message')) } }));
    });
</script>
@endif
@if(session('toast_error') || session('error'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: @js(session('toast_error') ?? session('error')) } }));
    });
</script>
@endif

<!-- Toast Container -->
<div
    x-data="{
        toasts: [],
        add(toast) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, ...toast, visible: false, removing: false });
            this.$nextTick(() => {
                const t = this.toasts.find(t => t.id === id);
                if (t) t.visible = true;
            });
            setTimeout(() => this.remove(id), toast.duration ?? 4000);
        },
        remove(id) {
            const t = this.toasts.find(t => t.id === id);
            if (!t || t.removing) return;
            t.removing = true;
            setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 350);
        }
    }"
    @toast.window="add($event.detail)"
    class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 w-80 pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            :class="{
                'translate-x-0 opacity-100': toast.visible && !toast.removing,
                'translate-x-full opacity-0': !toast.visible || toast.removing
            }"
            class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl shadow-lg border transition-all duration-300 ease-out"
            :style="toast.type === 'success' ? 'background:#f0fdf4;border-color:#bbf7d0;'
                  : toast.type === 'error'   ? 'background:#fef2f2;border-color:#fecaca;'
                  : toast.type === 'warning' ? 'background:#fffbeb;border-color:#fde68a;'
                  :                            'background:#eff6ff;border-color:#bfdbfe;'"
        >
            <!-- Icon -->
            <div class="shrink-0 mt-0.5">
                <template x-if="toast.type === 'success'">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="toast.type === 'warning'">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </template>
                <template x-if="toast.type === 'info'">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
            </div>
            <!-- Message -->
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold"
                   :class="toast.type === 'success' ? 'text-green-800'
                         : toast.type === 'error'   ? 'text-red-800'
                         : toast.type === 'warning' ? 'text-yellow-800'
                         :                            'text-blue-800'"
                   x-text="toast.title ?? (toast.type === 'success' ? 'Berhasil' : toast.type === 'error' ? 'Gagal' : toast.type === 'warning' ? 'Perhatian' : 'Info')">
                </p>
                <p class="text-xs mt-0.5 leading-relaxed"
                   :class="toast.type === 'success' ? 'text-green-700'
                         : toast.type === 'error'   ? 'text-red-700'
                         : toast.type === 'warning' ? 'text-yellow-700'
                         :                            'text-blue-700'"
                   x-text="toast.message">
                </p>
            </div>
            <!-- Close -->
            <button @click="remove(toast.id)" class="shrink-0 opacity-50 hover:opacity-100 transition-opacity mt-0.5"
                    :class="toast.type === 'success' ? 'text-green-700'
                          : toast.type === 'error'   ? 'text-red-700'
                          : toast.type === 'warning' ? 'text-yellow-700'
                          :                            'text-blue-700'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>
</body>
</html>
