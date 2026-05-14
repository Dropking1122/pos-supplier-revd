<div>
    @if(!$isAdmin)
    <div class="mb-4 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-2.5 rounded-lg">
        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        <span>Anda login sebagai <strong>Kasir</strong>. Data yang ditampilkan hanya milik Anda.</span>
    </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 {{ $isAdmin ? 'md:grid-cols-4' : 'md:grid-cols-2' }} gap-3 md:gap-4 mb-6">

        <a href="{{ route('sales.index') }}" class="bg-white rounded-xl shadow-sm border-l-4 border-indigo-500 p-3 sm:p-4 hover:shadow-md hover:border-indigo-600 transition-all cursor-pointer group">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs text-gray-500 uppercase font-semibold tracking-wide">{{ $isAdmin ? 'Total Penjualan' : 'Penjualan Saya' }}</p>
                    <p class="text-sm sm:text-base md:text-xl font-bold text-gray-800 mt-0.5 tabular-nums break-all">Rp&nbsp;{{ number_format($totalSales,0,',','.') }}</p>
                </div>
                <div class="w-9 h-9 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-indigo-100 transition-colors">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-[10px] text-indigo-400 mt-1 font-medium hidden sm:block">Lihat riwayat →</p>
        </a>

        <div class="bg-white rounded-xl shadow-sm border-l-4 border-green-500 p-3 sm:p-4">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs text-gray-500 uppercase font-semibold tracking-wide">{{ $isAdmin ? 'Total Profit' : 'Profit Saya' }}</p>
                    <p class="text-sm sm:text-base md:text-xl font-bold text-gray-800 mt-0.5 tabular-nums break-all">Rp&nbsp;{{ number_format($totalProfit,0,',','.') }}</p>
                </div>
                <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            @if($isAdmin)
            <p class="text-[10px] text-green-400 mt-1 font-medium hidden sm:block">Lihat laporan →</p>
            @endif
        </div>

        @if($isAdmin)
        <a href="{{ route('debts.index') }}" class="bg-white rounded-xl shadow-sm border-l-4 border-yellow-500 p-3 sm:p-4 hover:shadow-md hover:border-yellow-600 transition-all cursor-pointer group">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Hutang</p>
                    <p class="text-sm sm:text-base md:text-xl font-bold text-gray-800 mt-0.5 tabular-nums break-all">Rp&nbsp;{{ number_format($totalDebt,0,',','.') }}</p>
                </div>
                <div class="w-9 h-9 bg-yellow-50 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-yellow-100 transition-colors">
                    <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
            </div>
            <p class="text-[10px] text-yellow-400 mt-1 font-medium hidden sm:block">Lihat hutang →</p>
        </a>

        <a href="{{ route('products.index') }}" class="bg-white rounded-xl shadow-sm border-l-4 border-blue-500 p-3 sm:p-4 hover:shadow-md hover:border-blue-600 transition-all cursor-pointer group">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Barang</p>
                    <p class="text-sm sm:text-base md:text-xl font-bold text-gray-800 mt-0.5 tabular-nums">{{ $totalProducts }}</p>
                    <p class="text-[10px] sm:text-xs text-gray-400 mt-0.5 hidden sm:block">jenis produk</p>
                </div>
                <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-blue-100 transition-colors">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <p class="text-[10px] text-blue-400 mt-1 font-medium hidden sm:block">Lihat produk →</p>
        </a>
        @endif
    </div>

    <!-- Chart: Penjualan 7 Hari Terakhir -->
    <div class="bg-white rounded-xl shadow-sm p-4 sm:p-5 mb-4 md:mb-6">
        <div class="flex items-center justify-between gap-2 mb-4">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-700 text-sm sm:text-base">Grafik Penjualan</h3>
                    <p class="text-[10px] text-gray-400">7 hari terakhir{{ !$isAdmin ? ' (milik saya)' : '' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 text-[10px] text-gray-500">
                <span class="flex items-center gap-1">
                    <span class="inline-block w-3 h-3 rounded-sm bg-indigo-500"></span> Penjualan
                </span>
                <span class="flex items-center gap-1">
                    <span class="inline-block w-3 h-3 rounded-sm bg-emerald-400"></span> Profit
                </span>
            </div>
        </div>
        <div class="relative" style="height:220px;">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Transaksi Terbaru -->
    <div class="bg-white rounded-xl shadow-sm p-4 sm:p-5 mb-4 md:mb-6">
        <div class="flex items-center justify-between gap-2 mb-4">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h3 class="font-semibold text-gray-700 text-sm sm:text-base">{{ $isAdmin ? 'Transaksi Terbaru' : 'Transaksi Saya Terbaru' }}</h3>
            </div>
            <a href="{{ route('sales.index') }}" class="text-xs text-indigo-500 hover:text-indigo-700 font-medium whitespace-nowrap">Lihat semua →</a>
        </div>
        <div class="overflow-x-auto -mx-4 sm:mx-0">
            <table class="w-full text-xs sm:text-sm min-w-[400px] sm:min-w-0">
                <thead>
                    <tr class="text-left text-[10px] sm:text-xs text-gray-500 uppercase border-b border-gray-100">
                        <th class="pb-2 font-semibold px-4 sm:px-0">Invoice</th>
                        <th class="pb-2 font-semibold">Customer</th>
                        <th class="pb-2 font-semibold">Tanggal</th>
                        <th class="pb-2 font-semibold text-right">Total</th>
                        <th class="pb-2 font-semibold text-right pr-4 sm:pr-0">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSales as $sale)
                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors">
                        <td class="py-2.5 font-mono text-[10px] sm:text-xs text-indigo-600 font-semibold px-4 sm:px-0">{{ $sale->invoice_number }}</td>
                        <td class="py-2.5 text-gray-700 max-w-[100px] sm:max-w-none truncate">{{ $sale->customer?->name ?? 'Umum' }}</td>
                        <td class="py-2.5 text-gray-400 text-[10px] whitespace-nowrap">{{ $sale->created_at->locale('id')->isoFormat('D MMM, HH:mm') }}</td>
                        <td class="py-2.5 font-semibold text-gray-800 text-right tabular-nums whitespace-nowrap">Rp {{ number_format($sale->total_amount,0,',','.') }}</td>
                        <td class="py-2.5 text-right pr-4 sm:pr-0">
                            <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold
                                {{ $sale->status === 'paid' ? 'bg-green-100 text-green-700' : ($sale->status === 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ $sale->status === 'paid' ? 'Lunas' : ($sale->status === 'partial' ? 'Sebagian' : 'Belum') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400 text-sm px-4 sm:px-0">Belum ada transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($isAdmin)
    <!-- Top 5 Produk Terlaris -->
    <div class="bg-white rounded-xl shadow-sm p-4 sm:p-5">
        <div class="flex items-center gap-2 mb-4 sm:mb-5">
            <div class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <h3 class="font-semibold text-gray-700 text-sm sm:text-base">Top 5 Produk Terlaris</h3>
        </div>
        @if($topProducts->count())
            @php $maxTerjual = $topProducts->first()->total_terjual ?: 1; @endphp
            <div class="block md:hidden space-y-3">
                @foreach($topProducts as $i => $tp)
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shrink-0
                        {{ $i === 0 ? 'bg-amber-400 text-white' : ($i === 1 ? 'bg-gray-300 text-gray-700' : ($i === 2 ? 'bg-orange-300 text-white' : 'bg-gray-100 text-gray-500')) }}">
                        {{ $i + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $tp->nama_barang }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-1 mt-1.5">
                            <div class="h-1 rounded-full {{ $i === 0 ? 'bg-amber-400' : 'bg-indigo-400' }}"
                                 style="width: {{ ($tp->total_terjual / $maxTerjual) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-gray-800 tabular-nums">{{ number_format($tp->total_terjual,0,',','.') }}</p>
                        <p class="text-[10px] text-green-600 tabular-nums">Rp {{ number_format($tp->total_pendapatan,0,',','.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="hidden md:grid md:grid-cols-5 gap-4">
                @foreach($topProducts as $i => $tp)
                <div class="flex flex-col items-center text-center bg-gray-50 rounded-xl p-4">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold mb-2
                        {{ $i === 0 ? 'bg-amber-400 text-white' : ($i === 1 ? 'bg-gray-300 text-gray-700' : ($i === 2 ? 'bg-orange-300 text-white' : 'bg-gray-100 text-gray-500')) }}">
                        {{ $i + 1 }}
                    </div>
                    <p class="text-xs font-semibold text-gray-700 leading-tight mb-1">{{ $tp->nama_barang }}</p>
                    <p class="text-xs text-gray-400 mb-2">{{ $tp->kode_barang }}</p>
                    <p class="text-lg font-bold text-gray-800 tabular-nums">{{ number_format($tp->total_terjual,0,',','.') }}</p>
                    <p class="text-xs text-gray-400">unit terjual</p>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                        <div class="h-1.5 rounded-full {{ $i === 0 ? 'bg-amber-400' : 'bg-indigo-400' }}"
                             style="width: {{ ($tp->total_terjual / $maxTerjual) * 100 }}%"></div>
                    </div>
                    <p class="text-xs text-green-600 font-medium mt-2 tabular-nums">Rp {{ number_format($tp->total_pendapatan,0,',','.') }}</p>
                </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center gap-2 py-8 text-gray-400">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <p class="text-sm">Belum ada data penjualan</p>
            </div>
        @endif
    </div>
    @endif

    @script
    <script>
        (function() {
            const labels  = @json($chartLabels);
            const sales   = @json($chartSales);
            const profits = @json($chartProfit);

            const ctx = document.getElementById('salesChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Penjualan (Rp)',
                            data: sales,
                            backgroundColor: 'rgba(99, 102, 241, 0.85)',
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            borderSkipped: false,
                            order: 2,
                        },
                        {
                            label: 'Profit (Rp)',
                            data: profits,
                            type: 'line',
                            borderColor: 'rgba(52, 211, 153, 1)',
                            backgroundColor: 'rgba(52, 211, 153, 0.12)',
                            borderWidth: 2.5,
                            pointBackgroundColor: 'rgba(52, 211, 153, 1)',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4,
                            order: 1,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => {
                                    const v = ctx.parsed.y;
                                    return ' ' + ctx.dataset.label.replace(' (Rp)','') + ': Rp ' +
                                        new Intl.NumberFormat('id-ID').format(v);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#9ca3af' }
                        },
                        y: {
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: {
                                font: { size: 11 },
                                color: '#9ca3af',
                                callback: v => 'Rp ' + new Intl.NumberFormat('id-ID',{notation:'compact',maximumFractionDigits:1}).format(v)
                            }
                        }
                    }
                }
            });
        })();
    </script>
    @endscript
</div>
