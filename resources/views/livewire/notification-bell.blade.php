<div wire:poll.30s x-data="{ open: false }" @click.outside="open = false" class="relative">

    {{-- Bell Button --}}
    <button @click="open = !open"
            class="relative p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors"
            title="Notifikasi">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($badgeCount > 0)
        <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white
                     text-[10px] font-bold rounded-full flex items-center justify-center px-1 leading-none
                     ring-2 ring-white">
            {{ $badgeCount > 99 ? '99+' : $badgeCount }}
        </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-gray-200 z-50 overflow-hidden"
         style="display:none;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="text-sm font-bold text-gray-800">Notifikasi</span>
            </div>
            @if($badgeCount > 0)
            <span class="text-[10px] bg-red-100 text-red-600 font-bold px-2 py-0.5 rounded-full">
                {{ $badgeCount }} baru
            </span>
            @endif
        </div>

        <div class="overflow-y-auto max-h-[440px]">

            {{-- Stok Menipis Section --}}
            @if($lowStockProducts->count() > 0)
            <div>
                <div class="flex items-center justify-between px-4 pt-3 pb-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-orange-500">
                        ⚠ Stok Menipis ({{ $lowStockProducts->count() }})
                    </span>
                    <a href="{{ route('products.index') }}?filterLowStock=1"
                       class="text-[10px] text-indigo-500 hover:text-indigo-700 font-semibold"
                       @click="open=false">
                        Lihat semua →
                    </a>
                </div>
                @foreach($lowStockProducts as $product)
                <a href="{{ route('products.index') }}?filterLowStock=1"
                   class="flex items-center gap-3 px-4 py-2.5 hover:bg-orange-50 transition-colors border-l-4 border-orange-400 mx-2 mb-1 rounded-lg bg-orange-50/50"
                   @click="open=false">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $product->nama_barang }}</p>
                        <p class="text-[10px] text-gray-400">{{ $product->kode_barang }}</p>
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full shrink-0
                        {{ $product->kuantitas == 0 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                        {{ $product->kuantitas }} sisa
                    </span>
                </a>
                @endforeach
            </div>
            @endif

            {{-- Divider --}}
            @if($lowStockProducts->count() > 0 && $todaySalesCount > 0)
            <div class="my-1 border-t border-gray-100 mx-4"></div>
            @endif

            {{-- Transaksi Hari Ini Section --}}
            @if($todaySalesCount > 0)
            <div>
                <div class="flex items-center justify-between px-4 pt-3 pb-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-500">
                        ✦ Transaksi Hari Ini ({{ $todaySalesCount }})
                    </span>
                    <a href="{{ route('sales.index') }}"
                       class="text-[10px] text-indigo-500 hover:text-indigo-700 font-semibold"
                       @click="open=false">
                        Lihat semua →
                    </a>
                </div>
                @foreach($todaySales as $sale)
                <a href="{{ route('sales.invoice-customer', $sale->id) }}" target="_blank"
                   class="flex items-center gap-3 px-4 py-2.5 hover:bg-indigo-50 transition-colors mx-2 mb-1 rounded-lg">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-800 truncate font-mono">{{ $sale->invoice_number }}</p>
                        <p class="text-[10px] text-gray-400">{{ $sale->customer?->name ?? 'Umum' }} · {{ $sale->created_at->format('H:i') }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-xs font-bold text-gray-800 tabular-nums">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                        <span class="text-[10px] px-1.5 py-0.5 rounded-full font-semibold
                            {{ $sale->status === 'paid' ? 'bg-green-100 text-green-700' : ($sale->status === 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ $sale->status === 'paid' ? 'Lunas' : ($sale->status === 'partial' ? 'Sebagian' : 'Belum') }}
                        </span>
                    </div>
                </a>
                @endforeach
                @if($todaySalesCount > 5)
                <p class="text-center text-[10px] text-gray-400 pb-2">
                    dan {{ $todaySalesCount - 5 }} transaksi lainnya...
                </p>
                @endif
            </div>
            @endif

            {{-- Empty State --}}
            @if($lowStockProducts->count() === 0 && $todaySalesCount === 0)
            <div class="flex flex-col items-center gap-3 py-10 px-4 text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700">Semua baik!</p>
                    <p class="text-xs text-gray-400 mt-0.5">Stok aman, belum ada transaksi hari ini</p>
                </div>
            </div>
            @endif

        </div>

        {{-- Footer --}}
        <div class="px-4 py-2.5 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <span class="text-[10px] text-gray-400">Diperbarui otomatis setiap 30 detik</span>
            <button @click="open=false"
                    class="text-[10px] text-indigo-500 hover:text-indigo-700 font-semibold">
                Tutup
            </button>
        </div>
    </div>
</div>
