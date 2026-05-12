<div>
    <div class="flex flex-col sm:flex-row gap-3 mb-3">
        <input wire:model.live="search" type="text" placeholder="Cari invoice / customer..." class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        <select wire:model.live="filterCustomer" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            <option value="">Semua Customer</option>
            @foreach($customers as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterStatus" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            <option value="">Semua Status</option>
            <option value="paid">Lunas</option>
            <option value="partial">Sebagian</option>
            <option value="unpaid">Belum Bayar</option>
        </select>
        <input wire:model.live="filterDate" type="date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        <a href="{{ route('sales.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 whitespace-nowrap flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Transaksi Baru
        </a>
    </div>

    @if($filterCustomer)
    <div class="mb-3 flex items-center justify-between gap-3 bg-emerald-50 border border-emerald-200 px-4 py-2.5 rounded-lg">
        <div class="flex items-center gap-2 text-emerald-800 text-sm">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Menampilkan transaksi: <strong>{{ $customers->firstWhere('id', $filterCustomer)?->name }}</strong>
        </div>
        <a href="{{ route('sales.export-customer', ['customer_id' => $filterCustomer]) }}"
           class="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Excel Customer Ini
        </a>
    </div>
    @endif

    @php
        $sortIcon = fn($f) => $sortField === $f ? ($sortDirection === 'asc' ? '↑' : '↓') : '↕';
        $sortClass = fn($f) => $sortField === $f ? 'text-indigo-600' : 'text-gray-400';
    @endphp

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('invoice_number')">
                            <span class="flex items-center gap-1">Invoice <span class="{{ $sortClass('invoice_number') }}">{{ $sortIcon('invoice_number') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('created_at')">
                            <span class="flex items-center gap-1">Tanggal <span class="{{ $sortClass('created_at') }}">{{ $sortIcon('created_at') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-right cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('total_amount')">
                            <span class="flex items-center justify-end gap-1">Total <span class="{{ $sortClass('total_amount') }}">{{ $sortIcon('total_amount') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-center">Pembayaran</th>
                        <th class="px-4 py-3 text-center cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('status')">
                            <span class="flex items-center justify-center gap-1">Status <span class="{{ $sortClass('status') }}">{{ $sortIcon('status') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-indigo-600">{{ $sale->invoice_number }}</td>
                        <td class="px-4 py-3">{{ $sale->customer?->name ?? 'Umum' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-right font-semibold whitespace-nowrap">Rp {{ number_format($sale->total_amount,0,',','.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $sale->payment_type === 'cash' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $sale->payment_type === 'cash' ? 'Cash' : 'Tempo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sale->status === 'paid' ? 'bg-green-100 text-green-700' : ($sale->status === 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ $sale->status === 'paid' ? 'Lunas' : ($sale->status === 'partial' ? 'Sebagian' : 'Belum Bayar') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded transition-colors mr-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Invoice
                            </a>
                            <a href="{{ route('sales.invoice-excel', $sale->id) }}" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 text-xs bg-emerald-50 hover:bg-emerald-100 px-2 py-1 rounded transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Excel
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $sales->links() }}</div>
    </div>
</div>
