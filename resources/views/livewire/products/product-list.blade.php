<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">
        <input wire:model.live="search" type="text" placeholder="Cari barang..." class="w-full sm:w-72 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        <button wire:click="openCreate" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 flex items-center gap-2 whitespace-nowrap">
            ➕ Tambah Barang
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
                        <th class="px-4 py-3 text-right">Harga Grosir</th>
                        <th class="px-4 py-3 text-right">Harga Ecer</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 {{ $product->isLowStock() ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $product->kode_barang }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $product->nama_barang }}
                            @if($product->isLowStock())
                                <span class="ml-1 text-xs text-red-500">⚠️ Stok Rendah</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $product->jenis_barang ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-semibold {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-800' }}">{{ $product->kuantitas }}</span>
                            <span class="text-xs text-gray-400 ml-1">{{ $product->harga_satuan }}</span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-700">Rp {{ number_format($product->harga_grosir,0,',','.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">Rp {{ number_format($product->harga_ecer,0,',','.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="openEdit({{ $product->id }})" class="text-indigo-600 hover:text-indigo-800 mr-2">✏️</button>
                            <button wire:click="delete({{ $product->id }})" wire:confirm="Yakin hapus barang ini?" class="text-red-500 hover:text-red-700">🗑️</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Tidak ada barang ditemukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $products->links() }}</div>
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">{{ $editId ? 'Edit Barang' : 'Tambah Barang' }}</h3>
                <button wire:click="$set('showModal',false)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <form wire:submit="save" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Barang *</label>
                        <input wire:model="kode_barang" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('kode_barang') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                        <input wire:model="jenis_barang" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang *</label>
                    <input wire:model="nama_barang" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('nama_barang') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kuantitas *</label>
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
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showModal',false)" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
