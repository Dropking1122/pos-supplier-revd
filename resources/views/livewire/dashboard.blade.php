<div>
    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-indigo-500">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Penjualan</p>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalSales,0,',','.') }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Profit</p>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalProfit,0,',','.') }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Hutang</p>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalDebt,0,',','.') }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Barang</p>
            <p class="text-2xl font-bold text-gray-800">{{ $totalProducts }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Sales -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-5">
            <h3 class="font-semibold text-gray-700 mb-4">Transaksi Terbaru</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">Invoice</th><th class="pb-2">Customer</th><th class="pb-2">Total</th><th class="pb-2">Status</th></tr></thead>
                    <tbody>
                        @forelse($recentSales as $sale)
                        <tr class="border-b last:border-0 hover:bg-gray-50">
                            <td class="py-2 font-mono text-xs">{{ $sale->invoice_number }}</td>
                            <td class="py-2">{{ $sale->customer?->name ?? 'Umum' }}</td>
                            <td class="py-2">Rp {{ number_format($sale->total_amount,0,',','.') }}</td>
                            <td class="py-2">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sale->status === 'paid' ? 'bg-green-100 text-green-700' : ($sale->status === 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-4 text-center text-gray-400">Belum ada transaksi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="font-semibold text-gray-700 mb-4">⚠️ Stok Hampir Habis</h3>
            @forelse($lowStockProducts as $product)
            <div class="flex justify-between items-center py-2 border-b last:border-0">
                <div>
                    <p class="text-sm font-medium">{{ $product->nama_barang }}</p>
                    <p class="text-xs text-gray-400">{{ $product->kode_barang }}</p>
                </div>
                <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-bold">{{ $product->kuantitas }}</span>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">Semua stok aman ✅</p>
            @endforelse
        </div>
    </div>
</div>
