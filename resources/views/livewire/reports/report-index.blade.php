<div>
    <!-- Filter & Export Bar -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-5">
        <div class="flex flex-wrap gap-3 items-end justify-between">
            <div class="flex flex-wrap gap-3 items-end">
                <!-- Jenis Laporan -->
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Laporan</label>
                    <select wire:model.live="filterType"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white">
                        <option value="daily">Harian</option>
                        <option value="monthly">Bulanan</option>
                        <option value="yearly">Tahunan</option>
                    </select>
                </div>

                <!-- Date filter -->
                @if($filterType === 'daily')
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal</label>
                    <input wire:model.live="filterDate" type="date"
                           class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                @elseif($filterType === 'monthly')
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                    <input wire:model.live="filterMonth" type="month"
                           class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                @else
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                    <input wire:model.live="filterYear" type="number" min="2020" max="2099"
                           class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-28 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                @endif
            </div>

            <!-- Export Buttons -->
            <div class="flex gap-2">
                <a href="{{ route('reports.export', ['type'=>$filterType,'date'=>$filterDate,'month'=>$filterMonth,'year'=>$filterYear]) }}"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel / CSV
                </a>
                <a href="{{ route('reports.pdf', ['type'=>$filterType,'date'=>$filterDate,'month'=>$filterMonth,'year'=>$filterYear]) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    @php
        $countPaid    = $sales->where('status','paid')->count();
        $countPartial = $sales->where('status','partial')->count();
        $countUnpaid  = $sales->where('status','unpaid')->count();
        $totalUnpaid  = $sales->sum(fn($s) => $s->total_amount - $s->amount_paid);
        $margin       = $totalRevenue > 0 ? round($totalProfit / $totalRevenue * 100, 1) : 0;
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Penjualan</p>
            <p class="text-xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalRevenue,0,',','.') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $sales->count() }} transaksi</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Profit</p>
            <p class="text-xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalProfit,0,',','.') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Margin {{ $margin }}%</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-amber-400">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Transaksi Lunas</p>
            <p class="text-xl font-bold text-gray-800 mt-1">{{ $countPaid }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $countPartial }} sebagian bayar</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-400">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Piutang</p>
            <p class="text-xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalUnpaid,0,',','.') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $countUnpaid }} belum bayar</p>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">Rincian Transaksi</h3>
            @if($sales->count())
            <span class="text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full">{{ $sales->count() }} data</span>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-center w-10">No</th>
                        <th class="px-4 py-3 text-left">Invoice</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-center">Tipe</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Profit</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sales as $i => $sale)
                    @php
                        $profit = $sale->details->sum(fn($d) => $d->subtotal - ($d->quantity * ($d->product->modal_awal ?? 0)));
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-center text-xs text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 font-semibold">{{ $sale->invoice_number }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">
                            <div>{{ $sale->created_at->format('d/m/Y') }}</div>
                            <div class="text-gray-400">{{ $sale->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="{{ $sale->customer ? 'text-gray-700' : 'text-gray-400 italic' }}">
                                {{ $sale->customer?->name ?? 'Umum' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $sale->payment_type === 'cash' ? 'bg-sky-100 text-sky-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ $sale->payment_type === 'cash' ? 'Cash' : 'Tempo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800 whitespace-nowrap">
                            Rp {{ number_format($sale->total_amount,0,',','.') }}
                        </td>
                        <td class="px-4 py-3 text-right text-green-600 font-semibold whitespace-nowrap">
                            Rp {{ number_format($profit,0,',','.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $sale->status==='paid'    ? 'bg-green-100 text-green-700'
                                : ($sale->status==='partial' ? 'bg-yellow-100 text-yellow-700'
                                :                              'bg-red-100 text-red-700') }}">
                                {{ $sale->status==='paid' ? 'Lunas' : ($sale->status==='partial' ? 'Sebagian' : 'Belum Bayar') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-gray-400 text-sm">Tidak ada data transaksi pada periode ini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($sales->count())
                <tfoot class="bg-indigo-50 border-t-2 border-indigo-200">
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right text-sm font-bold text-gray-700">
                            TOTAL ({{ $sales->count() }} transaksi)
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900 whitespace-nowrap">
                            Rp {{ number_format($totalRevenue,0,',','.') }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-700 whitespace-nowrap">
                            Rp {{ number_format($totalProfit,0,',','.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
