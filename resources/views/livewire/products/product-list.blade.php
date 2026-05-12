<div>
    <!-- Summary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">

        <div class="bg-white rounded-xl shadow-sm border-l-4 border-indigo-500 p-3 sm:p-4">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Barang</p>
                    <p class="text-sm sm:text-lg md:text-2xl font-bold text-gray-800 mt-0.5 tabular-nums">{{ $totalProduk }}</p>
                    <p class="text-[10px] sm:text-xs text-gray-400 hidden sm:block">jenis produk</p>
                </div>
                <div class="w-9 h-9 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-l-4 border-green-500 p-3 sm:p-4">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Terjual</p>
                    <p class="text-sm sm:text-lg md:text-2xl font-bold text-gray-800 mt-0.5 tabular-nums">{{ number_format($totalTerjual,0,',','.') }}</p>
                    <p class="text-[10px] sm:text-xs text-gray-400 hidden sm:block">unit keseluruhan</p>
                </div>
                <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-l-4 border-blue-500 p-3 sm:p-4">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs text-gray-500 uppercase font-semibold tracking-wide">Pendapatan</p>
                    <p class="text-sm sm:text-base md:text-lg font-bold text-gray-800 mt-0.5 tabular-nums break-all">Rp&nbsp;{{ number_format($totalPendapatan,0,',','.') }}</p>
                    <p class="text-[10px] sm:text-xs text-gray-400 hidden sm:block">dari semua penjualan</p>
                </div>
                <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-l-4 border-red-500 p-3 sm:p-4">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs text-gray-500 uppercase font-semibold tracking-wide">Stok Rendah</p>
                    <p class="text-sm sm:text-lg md:text-2xl font-bold text-gray-800 mt-0.5 tabular-nums">{{ $lowStockCount }}</p>
                    <p class="text-[10px] sm:text-xs text-gray-400 hidden sm:block">produk perlu restock</p>
                </div>
                <div class="w-9 h-9 bg-red-50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Table -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-72">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input wire:model.live="search" type="text" placeholder="Cari barang..." class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            @if($filterLowStock)
            <button wire:click="$set('filterLowStock', false)" class="flex items-center gap-1.5 bg-red-100 text-red-700 border border-red-300 px-3 py-2 rounded-lg text-xs font-semibold hover:bg-red-200 whitespace-nowrap transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Stok Rendah
            </button>
            @endif
        </div>
        <button wire:click="openCreate" class="w-full sm:w-auto bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 flex items-center justify-center gap-2 whitespace-nowrap transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Barang
        </button>
    </div>

    @if($filterLowStock)
    <div class="mb-3 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-2.5 rounded-lg text-sm">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        Menampilkan produk dengan stok rendah (stok ≤ batas minimum)
    </div>
    @endif

    <!-- Sort helper macro -->
    @php
        $sortIcon = function($field) use ($sortField, $sortDirection) {
            if ($sortField !== $field) return '↕';
            return $sortDirection === 'asc' ? '↑' : '↓';
        };
        $sortClass = fn($field) => $sortField === $field ? 'text-indigo-600' : 'text-gray-400';
    @endphp

    <!-- Table (desktop) -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden hidden sm:block">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('kode_barang')">
                            <span class="flex items-center gap-1">Kode <span class="{{ $sortClass('kode_barang') }}">{{ $sortIcon('kode_barang') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-left cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('nama_barang')">
                            <span class="flex items-center gap-1">Nama Barang <span class="{{ $sortClass('nama_barang') }}">{{ $sortIcon('nama_barang') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-left hidden md:table-cell cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('jenis_barang')">
                            <span class="flex items-center gap-1">Jenis <span class="{{ $sortClass('jenis_barang') }}">{{ $sortIcon('jenis_barang') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-right cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('kuantitas')">
                            <span class="flex items-center justify-end gap-1">Stok <span class="{{ $sortClass('kuantitas') }}">{{ $sortIcon('kuantitas') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-right hidden lg:table-cell cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('total_terjual')">
                            <span class="flex items-center justify-end gap-1">Terjual <span class="{{ $sortClass('total_terjual') }}">{{ $sortIcon('total_terjual') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-right hidden lg:table-cell cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('total_pendapatan')">
                            <span class="flex items-center justify-end gap-1">Pendapatan <span class="{{ $sortClass('total_pendapatan') }}">{{ $sortIcon('total_pendapatan') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-right cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('harga_ecer')">
                            <span class="flex items-center justify-end gap-1">Harga Ecer <span class="{{ $sortClass('harga_ecer') }}">{{ $sortIcon('harga_ecer') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition-colors {{ $product->isLowStock() ? 'bg-red-50 hover:bg-red-100' : '' }}">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500 whitespace-nowrap">{{ $product->kode_barang }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $product->nama_barang }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $product->jenis_barang ?? '-' }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <span class="font-semibold {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-800' }}">{{ $product->kuantitas }}</span>
                            <span class="text-xs text-gray-400 ml-0.5">{{ $product->harga_satuan }}</span>
                        </td>
                        <td class="px-4 py-3 text-right hidden lg:table-cell">
                            @if($product->total_terjual > 0)
                                <span class="font-semibold text-green-700 tabular-nums">{{ number_format($product->total_terjual,0,',','.') }}</span>
                                <span class="text-xs text-gray-400 ml-0.5">unit</span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right hidden lg:table-cell whitespace-nowrap">
                            @if($product->total_pendapatan > 0)
                                <span class="text-gray-700 font-medium tabular-nums">Rp {{ number_format($product->total_pendapatan,0,',','.') }}</span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-gray-700 whitespace-nowrap tabular-nums">Rp {{ number_format($product->harga_ecer,0,',','.') }}</td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <button wire:click="openRestock({{ $product->id }})" class="inline-flex items-center justify-center w-7 h-7 rounded-md text-green-600 hover:bg-green-50 transition-colors mr-1" title="Restock">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                            <button wire:click="openEdit({{ $product->id }})" class="inline-flex items-center justify-center w-7 h-7 rounded-md text-indigo-600 hover:bg-indigo-50 transition-colors mr-1" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button wire:click="delete({{ $product->id }})" wire:confirm="Yakin hapus barang ini?" class="inline-flex items-center justify-center w-7 h-7 rounded-md text-red-500 hover:bg-red-50 transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-400">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span class="text-sm">Tidak ada barang ditemukan</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">{{ $products->links() }}</div>
    </div>

    <!-- Card list (mobile only) -->
    <div class="block sm:hidden space-y-3">
        @forelse($products as $product)
        <div class="bg-white rounded-xl shadow-sm p-4 {{ $product->isLowStock() ? 'border-l-4 border-red-400' : '' }}">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-semibold text-gray-800 text-sm">{{ $product->nama_barang }}</p>
                    </div>
                    <p class="text-[11px] text-gray-400 font-mono mt-0.5">{{ $product->kode_barang }}{{ $product->jenis_barang ? ' · '.$product->jenis_barang : '' }}</p>
                </div>
                <div class="flex gap-1.5 shrink-0">
                    <button wire:click="openRestock({{ $product->id }})" class="w-8 h-8 flex items-center justify-center rounded-lg text-green-600 bg-green-50 hover:bg-green-100 transition-colors" title="Restock">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                    <button wire:click="openEdit({{ $product->id }})" class="w-8 h-8 flex items-center justify-center rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button wire:click="delete({{ $product->id }})" wire:confirm="Yakin hapus barang ini?" class="w-8 h-8 flex items-center justify-center rounded-lg text-red-500 bg-red-50 hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                <div class="bg-gray-50 rounded-lg py-1.5 px-2">
                    <p class="text-[10px] text-gray-400 uppercase font-semibold">Stok</p>
                    <p class="text-sm font-bold {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-800' }} tabular-nums">{{ $product->kuantitas }}</p>
                    <p class="text-[10px] text-gray-400">{{ $product->harga_satuan ?: '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg py-1.5 px-2">
                    <p class="text-[10px] text-gray-400 uppercase font-semibold">Terjual</p>
                    <p class="text-sm font-bold text-green-700 tabular-nums">{{ $product->total_terjual > 0 ? number_format($product->total_terjual,0,',','.') : '—' }}</p>
                    <p class="text-[10px] text-gray-400">unit</p>
                </div>
                <div class="bg-gray-50 rounded-lg py-1.5 px-2">
                    <p class="text-[10px] text-gray-400 uppercase font-semibold">Harga Ecer</p>
                    <p class="text-[11px] font-bold text-gray-800 tabular-nums">Rp {{ number_format($product->harga_ecer,0,',','.') }}</p>
                    <p class="text-[10px] text-gray-400">per unit</p>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm p-10 text-center">
            <div class="flex flex-col items-center gap-2 text-gray-400">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span class="text-sm">Tidak ada barang ditemukan</span>
            </div>
        </div>
        @endforelse
        <div class="bg-white rounded-xl shadow-sm px-4 py-3">{{ $products->links() }}</div>
    </div>

    <!-- Restock Modal -->
    @if($showRestockModal)
    <div class="fixed inset-0 bg-black/50 flex items-end sm:items-center justify-center z-50 p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-2xl w-full sm:max-w-sm">
            <div class="flex justify-between items-center px-5 py-4 border-b">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Restock Barang</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $restockNama }}</p>
                </div>
                <button wire:click="$set('showRestockModal',false)" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="restock" class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tambah Stok <span class="text-red-500">*</span></label>
                    <input wire:model="restockJumlah" type="number" min="1" autofocus class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none" placeholder="Masukkan jumlah...">
                    @error('restockJumlah') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" wire:click="$set('showRestockModal',false)" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition-colors">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 transition-colors">Simpan Restock</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-end sm:items-center justify-center z-50 p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-2xl w-full sm:max-w-lg max-h-[92vh] overflow-y-auto">
            <div class="flex justify-between items-center px-5 py-4 border-b sticky top-0 bg-white rounded-t-2xl sm:rounded-t-xl z-10">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">{{ $editId ? 'Edit Barang' : 'Tambah Barang' }}</h3>
                <button wire:click="$set('showModal',false)" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Barang <span class="text-red-500">*</span></label>
                        <input wire:model="kode_barang" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('kode_barang') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                        <input wire:model="jenis_barang" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                    <input wire:model="nama_barang" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('nama_barang') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kuantitas <span class="text-red-500">*</span></label>
                        <input wire:model="kuantitas" type="number" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                        <input wire:model="harga_satuan" type="text" placeholder="pcs, kg, box..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modal Awal (Rp) <span class="text-red-500">*</span></label>
                    <input wire:model.live.debounce.300ms="modal_awal" type="number" min="0" step="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('modal_awal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Grosir (Rp)</label>
                        <input wire:model.live.debounce.300ms="harga_grosir" type="number" min="0" step="100"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:outline-none
                                      {{ $errors->has('harga_grosir') ? 'border-red-400 focus:ring-red-400' : 'border-gray-300 focus:ring-indigo-500' }}">
                        @error('harga_grosir') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @php $marginGrosir = (float)$harga_grosir - (float)$modal_awal; @endphp
                        @if((float)$harga_grosir > 0 && (float)$modal_awal > 0)
                            <p class="text-xs mt-0.5 {{ $marginGrosir >= 0 ? 'text-green-600' : 'text-red-500 font-semibold' }}">
                                Margin: {{ $marginGrosir >= 0 ? '+' : '' }}Rp {{ number_format(abs($marginGrosir),0,',','.') }}
                                ({{ number_format(($marginGrosir / (float)$modal_awal) * 100, 1) }}%)
                            </p>
                        @elseif((float)$modal_awal > 0)
                            <p class="text-xs mt-0.5 text-gray-400">Isi harga untuk melihat margin</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Ecer (Rp)</label>
                        <input wire:model.live.debounce.300ms="harga_ecer" type="number" min="0" step="100"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:outline-none
                                      {{ $errors->has('harga_ecer') ? 'border-red-400 focus:ring-red-400' : 'border-gray-300 focus:ring-indigo-500' }}">
                        @error('harga_ecer') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        @php $marginEcer = (float)$harga_ecer - (float)$modal_awal; @endphp
                        @if((float)$harga_ecer > 0 && (float)$modal_awal > 0)
                            <p class="text-xs mt-0.5 {{ $marginEcer >= 0 ? 'text-green-600' : 'text-red-500 font-semibold' }}">
                                Margin: {{ $marginEcer >= 0 ? '+' : '' }}Rp {{ number_format(abs($marginEcer),0,',','.') }}
                                ({{ number_format(($marginEcer / (float)$modal_awal) * 100, 1) }}%)
                            </p>
                        @elseif((float)$modal_awal > 0)
                            <p class="text-xs mt-0.5 text-gray-400">Isi harga untuk melihat margin</p>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Minimum</label>
                    <input wire:model="stock_minimum" type="number" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <p class="text-xs text-gray-400 mt-1">Peringatan stok rendah muncul saat stok ≤ nilai ini</p>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t">
                    <button type="button" wire:click="$set('showModal',false)" class="flex-1 sm:flex-none px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition-colors">Batal</button>
                    <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
