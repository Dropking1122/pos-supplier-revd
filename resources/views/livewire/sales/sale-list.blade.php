<div>
    {{-- Filter Bar --}}
    <div class="flex flex-col gap-2 mb-3">
        {{-- Row 1: Search + Tombol Baru --}}
        <div class="flex gap-2">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                <input wire:model.live="search" type="text" placeholder="Cari invoice / customer..."
                       class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <a href="{{ route('sales.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 whitespace-nowrap flex items-center gap-1.5 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Transaksi Baru
            </a>
        </div>

        {{-- Row 2: Filters --}}
        <div class="flex flex-wrap gap-2">
            @php $selectClass = 'border border-gray-300 rounded-lg pl-3 pr-8 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none appearance-none bg-white'; $selectStyle = 'background-image:url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'%3E%3Cpath stroke=\'%236b7280\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6 8l4 4 4-4\'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 8px center;background-size:16px;'; @endphp

            {{-- Filter Customer --}}
            <select wire:model.live="filterCustomer" class="{{ $selectClass }}" style="{{ $selectStyle }}">
                <option value="">Semua Customer</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>

            {{-- Filter Kasir (Admin only) --}}
            @if($isAdmin)
            <select wire:model.live="filterKasir" class="{{ $selectClass }} {{ $filterKasir ? 'border-violet-400 bg-violet-50 text-violet-700' : '' }}" style="{{ $selectStyle }}">
                <option value="">Semua Kasir</option>
                @foreach($kasirList as $k)
                    <option value="{{ $k->id }}">{{ $k->name }} {{ $k->is_admin ? '(Admin)' : '(Kasir)' }}</option>
                @endforeach
            </select>
            @endif

            {{-- Filter Status --}}
            <select wire:model.live="filterStatus" class="{{ $selectClass }}" style="{{ $selectStyle }}">
                <option value="">Semua Status</option>
                <option value="paid">Lunas</option>
                <option value="partial">Sebagian</option>
                <option value="unpaid">Belum Bayar</option>
            </select>

            {{-- Filter Tanggal --}}
            <input wire:model.live="filterDate" type="date" style="color-scheme: light; {{ $selectStyle }}"
                   class="{{ $selectClass }} text-gray-800 {{ $filterDate ? 'border-indigo-400 bg-indigo-50' : '' }}">

            {{-- Reset Filters --}}
            @if($filterCustomer || $filterKasir || $filterStatus || $filterDate || $search)
            <button wire:click="$set('filterCustomer',''); $set('filterKasir',''); $set('filterStatus',''); $set('filterDate',''); $set('search','')"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-600 border border-gray-200 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Reset Filter
            </button>
            @endif
        </div>

        {{-- Active filter info banners --}}
        @if($filterKasir && $isAdmin)
        @php $activeKasir = $kasirList->firstWhere('id', $filterKasir); @endphp
        <div class="flex items-center gap-2 bg-violet-50 border border-violet-200 px-4 py-2.5 rounded-lg text-sm text-violet-800">
            <svg class="w-4 h-4 text-violet-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Menampilkan transaksi oleh kasir: <strong class="ml-1">{{ $activeKasir?->name }}</strong>
            <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $activeKasir?->is_admin ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">
                {{ $activeKasir?->is_admin ? 'Admin' : 'Kasir' }}
            </span>
            <span class="ml-auto text-violet-500 text-xs">{{ $sales->total() }} transaksi</span>
        </div>
        @endif

        @if($filterCustomer)
        <div class="flex items-center justify-between gap-3 bg-emerald-50 border border-emerald-200 px-4 py-2.5 rounded-lg">
            <div class="flex items-center gap-2 text-emerald-800 text-sm">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Transaksi: <strong class="ml-1">{{ $customers->firstWhere('id', $filterCustomer)?->name }}</strong>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('sales.export-customer', ['customer_id' => $filterCustomer]) }}" target="_blank"
                   class="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export Excel
                </a>
                <a href="{{ route('sales.export-customer-pdf', ['customer_id' => $filterCustomer]) }}" target="_blank"
                   class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Export PDF
                </a>
            </div>
        </div>
        @endif
    </div>

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
                        <th class="px-4 py-3 text-left">Operator</th>
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
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->user?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('sales.invoice-customer', $sale->id) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-sky-700 bg-sky-100 hover:bg-sky-200 border border-sky-200 text-xs font-semibold px-2.5 py-1.5 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Invoice
                                </a>
                                @if($isAdmin)
                                <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-xs font-semibold px-2.5 py-1.5 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Laporan
                                </a>
                                @endif
                                @if($isAdmin || $sale->user_id === auth()->id())
                                <button wire:click="confirmDelete({{ $sale->id }})"
                                        class="inline-flex items-center gap-1.5 text-red-700 bg-red-100 hover:bg-red-200 border border-red-200 text-xs font-semibold px-2.5 py-1.5 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Belum ada transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $sales->links() }}</div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Hapus Transaksi?</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Invoice: <span class="font-mono font-semibold text-red-600">{{ $deleteSaleInvoice }}</span></p>
                    </div>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 mb-5 text-sm text-amber-800 space-y-1">
                    <p class="font-semibold flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                        Tindakan ini tidak dapat dibatalkan!
                    </p>
                    <ul class="list-disc list-inside space-y-0.5 text-amber-700 text-xs mt-1">
                        <li>Stok setiap produk dalam transaksi ini akan dikembalikan</li>
                        <li>Data hutang & cicilan terkait akan ikut terhapus</li>
                        <li>Total profit & laporan akan otomatis berubah</li>
                    </ul>
                </div>
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="deleteSale" wire:loading.attr="disabled"
                            class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-xl text-sm font-semibold hover:bg-red-700 disabled:opacity-60 transition-colors flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="deleteSale">
                            <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Ya, Hapus Transaksi
                        </span>
                        <span wire:loading wire:target="deleteSale">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
