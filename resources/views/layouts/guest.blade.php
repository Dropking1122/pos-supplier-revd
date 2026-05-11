<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php $setting = \App\Models\Setting::getSettings(); @endphp

    <title>Masuk — {{ $setting->company_name ?? config('app.name') }}</title>
    <meta name="description" content="Masuk ke sistem Point of Sale {{ $setting->company_name ?? config('app.name') }}. Kelola penjualan, stok, dan laporan bisnis Anda.">
    <meta name="robots" content="noindex, nofollow">
    <meta property="og:title" content="Login — {{ $setting->company_name ?? config('app.name') }}">
    <meta property="og:description" content="Sistem POS untuk mengelola penjualan dan stok toko.">
    <meta property="og:type" content="website">

    @if($setting->company_logo)
    <link rel="icon" href="{{ asset($setting->company_logo) }}">
    <link rel="apple-touch-icon" href="{{ asset($setting->company_logo) }}">
    @else
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234f46e5'><path d='M13 10V3L4 14h7v7l9-11h-7z'/></svg>">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">

<div class="min-h-screen flex">

    <!-- Left Panel — Branding (hidden on mobile) -->
    <div class="hidden lg:flex lg:w-[55%] xl:w-[60%] relative bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-700 flex-col items-center justify-center p-12 overflow-hidden">

        <!-- Background decorative circles -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/5 rounded-full"></div>
            <div class="absolute top-1/2 -right-32 w-80 h-80 bg-white/5 rounded-full"></div>
            <div class="absolute -bottom-20 left-1/3 w-64 h-64 bg-white/5 rounded-full"></div>
            <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-white/10 rounded-full"></div>
        </div>

        <!-- Grid pattern overlay -->
        <div class="absolute inset-0" style="background-image: url(\"data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")"></div>

        <!-- Content -->
        <div class="relative z-10 text-center max-w-lg">
            <!-- Logo -->
            <div class="mb-8 flex justify-center">
                @if($setting->company_logo)
                <div class="w-24 h-24 bg-white/15 backdrop-blur-sm rounded-2xl p-3 border border-white/20 shadow-xl">
                    <img src="{{ asset($setting->company_logo) }}"
                         alt="{{ $setting->company_name }}"
                         class="w-full h-full object-contain">
                </div>
                @else
                <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20 shadow-xl">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                @endif
            </div>

            <!-- Company Name -->
            <h1 class="text-3xl xl:text-4xl font-bold text-white leading-tight mb-3">
                {{ $setting->company_name ?? config('app.name') }}
            </h1>

            @if($setting->company_address || $setting->company_phone)
            <p class="text-indigo-200 text-sm leading-relaxed mb-8">
                @if($setting->company_address){{ $setting->company_address }}@endif
                @if($setting->company_address && $setting->company_phone) · @endif
                @if($setting->company_phone){{ $setting->company_phone }}@endif
            </p>
            @else
            <p class="text-indigo-200 text-sm mb-8">Sistem Point of Sale & Manajemen Toko</p>
            @endif

            <!-- Feature highlights -->
            <div class="grid grid-cols-1 gap-3 text-left">
                @foreach([
                    ['icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'text'=>'Laporan penjualan harian, bulanan, dan tahunan'],
                    ['icon'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'text'=>'Manajemen stok produk dengan peringatan otomatis'],
                    ['icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'text'=>'Kelola hutang customer dan pembayaran tempo'],
                ] as $f)
                <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm rounded-xl px-4 py-3 border border-white/10">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/></svg>
                    </div>
                    <p class="text-sm text-indigo-100">{{ $f['text'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Footer brand -->
        <div class="relative z-10 mt-12 text-center">
            <p class="text-indigo-300/60 text-xs">Powered by <a href="https://revdstore.app" target="_blank" class="text-indigo-200/80 hover:text-white transition-colors">revdstore.app</a></p>
        </div>
    </div>

    <!-- Right Panel — Login Form -->
    <div class="flex-1 flex flex-col items-center justify-center px-5 sm:px-8 py-10 bg-gray-50 overflow-y-auto">
        <div class="w-full max-w-sm">

            <!-- Mobile logo (hidden on desktop) -->
            <div class="flex flex-col items-center mb-8 lg:hidden">
                @if($setting->company_logo)
                <div class="w-16 h-16 bg-white rounded-xl shadow-sm border border-gray-200 p-2 mb-3">
                    <img src="{{ asset($setting->company_logo) }}"
                         alt="{{ $setting->company_name }}"
                         class="w-full h-full object-contain">
                </div>
                @else
                <div class="w-14 h-14 bg-indigo-600 rounded-xl flex items-center justify-center mb-3 shadow-md">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                @endif
                <h2 class="text-lg font-bold text-gray-800">{{ $setting->company_name ?? config('app.name') }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">Sistem Point of Sale</p>
            </div>

            <!-- Form header -->
            <div class="mb-7">
                <h2 class="text-2xl font-bold text-gray-900">Selamat datang!</h2>
                <p class="text-gray-500 text-sm mt-1.5">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <!-- The slot (form fields from Volt component) -->
            {{ $slot }}

            <!-- Footer mobile -->
            <p class="mt-8 text-center text-xs text-gray-400 lg:hidden">
                Powered by <a href="https://revdstore.app" target="_blank" class="text-indigo-500 hover:text-indigo-700">revdstore.app</a>
            </p>
        </div>
    </div>

</div>

@livewireScripts
</body>
</html>
