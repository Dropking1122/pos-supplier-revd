<div>
    <!-- Summary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-indigo-500">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Barang</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalProduk }}</p>
            <p class="text-xs text-gray-400 mt-1">jenis produk</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Terjual</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalTerjual,0,',','.') }}</p>
            <p class="text-xs text-gray-400 mt-1">unit keseluruhan</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Pendapatan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalPendapatan,0,',','.') }}</p>
            <p class="text-xs text-gray-400 mt-1">dari semua penjualan</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-500">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Stok Rendah</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $lowStockCount }}</p>
            <p class="text-xs text-gray-400 mt-1">produk perlu restock</p>
        </div>
    </div>

    <!-- Header Table -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
        <div class="relative w-full sm:w-72">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input wire:model.live="search" type="text" placeholder="Cari barang..." class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>
        <button wire:click="openCreate" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 flex items-center gap-2 whitespace-nowrap transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Barang
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Nama Barang</th>
                        <th class="px-4 py-3 text-left">Jenis</th>
                        <th class="px-4 py-3 text-right">Stok</th>
                        <th class="px-4 py-3 text-right">Terjual</th>
                        <th class="px-4 py-3 text-right">Pendapatan</th>
                        <th class="px-4 py-3 text-right">Harga Ecer</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition-colors {{ $product->isLowStock() ? 'bg-red-50 hover:bg-red-100' : '' }}">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $product->kode_barang }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $product->nama_barang }}</div>
                            @if($product->isLowStock())
                                <span class="inline-flex items-center gap-1 text-xs text-red-500 mt-0.5">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                                    Stok Rendah
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $product->jenis_barang ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-semibold {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-800' }}">{{ $product->kuantitas }}</span>
                            <span class="text-xs text-gray-400 ml-1">{{ $product->harga_satuan }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($product->total_terjual > 0)
                                <span class="inline-flex items-center gap-1 font-semibold text-green-700">
                                    {{ number_format($product->total_terjual,0,',','.') }}
                                    <span class="text-xs font-normal text-gray-400">unit</span>
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($product->total_pendapatan > 0)
                                <span class="text-gray-700 font-medium">Rp {{ number_format($product->total_pendapatan,0,',','.') }}</span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-gray-700">Rp {{ number_format($product->harga_ecer,0,',','.') }}</td>
                        <td class="px-4 py-3 text-center">
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

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">{{ $editId ? 'Edit Barang' : 'Tambah Barang' }}</h3>
                <button wire:click="$set('showModal',false)" class="text-gray-400 hover:text-gray-600 w-7 h-7 flex items-center justify-center rounded-md hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="p-6 space-y-4">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modal Awal (Rp)</label>
                    <input wire:model="modal_awal" type="number" min="0" step="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Grosir (Rp)</label>
                        <input wire:model="harga_grosir" type="number" min="0" step="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Ecer (Rp)</label>
                        <input wire:model="harga_ecer" type="number" min="0" step="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Minimum</label>
                    <input wire:model="stock_minimum" type="number" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <p class="text-xs text-gray-400 mt-1">Peringatan stok rendah muncul saat stok ≤ nilai ini</p>
                </div>
                <div class="flex justify-end gap-3 pt-2 border-t">
                    <button type="button" wire:click="$set('showModal',false)" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
