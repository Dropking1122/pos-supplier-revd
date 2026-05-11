<div>
    <!-- Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-5 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Laporan</label>
            <select wire:model.live="filterType" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="daily">Harian</option>
                <option value="monthly">Bulanan</option>
                <option value="yearly">Tahunan</option>
            </select>
        </div>
        @if($filterType === 'daily')
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal</label>
            <input wire:model.live="filterDate" type="date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>
        @elseif($filterType === 'monthly')
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
            <input wire:model.live="filterMonth" type="month" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>
        @else
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
            <input wire:model.live="filterYear" type="number" min="2020" max="2099" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-28 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>
        @endif
        <a href="{{ route('reports.export', ['type'=>$filterType,'date'=>$filterDate,'month'=>$filterMonth,'year'=>$filterYear]) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
            📥 Export Excel
        </a>
        <a href="{{ route('reports.pdf', ['type'=>$filterType,'date'=>$filterDate,'month'=>$filterMonth,'year'=>$filterYear]) }}" target="_blank" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">
            📄 Export PDF
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Penjualan</p>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalRevenue,0,',','.') }}</p>
            <p class="text-xs text-gray-400">{{ $sales->count() }} transaksi</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Profit</p>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalProfit,0,',','.') }}</p>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Invoice</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Profit</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600">{{ $sale->invoice_number }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $sale->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $sale->customer?->name ?? 'Umum' }}</td>
                        <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($sale->total_amount,0,',','.') }}</td>
                        <td class="px-4 py-3 text-right text-green-600">
                            Rp {{ number_format($sale->details->sum(fn($d)=>$d->subtotal-($d->quantity*($d->product->modal_awal??0))),0,',','.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sale->status==='paid'?'bg-green-100 text-green-700':($sale->status==='partial'?'bg-yellow-100 text-yellow-700':'bg-red-100 text-red-700') }}">
                                {{ $sale->status==='paid'?'Lunas':($sale->status==='partial'?'Sebagian':'Belum Bayar') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada data untuk periode ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
