<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Items -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Product Search -->
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-700 mb-3">Cari Barang</h3>
                <div class="relative">
                    <input wire:model.live="productSearch" type="text" placeholder="Ketik nama atau kode barang..." class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @if($showProductSearch && count($productResults))
                    <div class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg z-10 mt-1 max-h-64 overflow-y-auto">
                        @foreach($productResults as $p)
                        <button wire:click="selectProduct({{ $p['id'] }})" class="w-full text-left px-4 py-3 hover:bg-indigo-50 border-b last:border-0 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-sm">{{ $p['nama_barang'] }}</p>
                                <p class="text-xs text-gray-400">{{ $p['kode_barang'] }} &bull; Stok: {{ $p['kuantitas'] }}</p>
                            </div>
                            <div class="text-right text-xs text-gray-500">
                                <p>Ecer: Rp {{ number_format($p['harga_ecer'],0,',','.') }}</p>
                                <p>Grosir: Rp {{ number_format($p['harga_grosir'],0,',','.') }}</p>
                            </div>
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Barang</th>
                                <th class="px-4 py-3 text-center">Harga</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($items as $i => $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium">{{ $item['nama_barang'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $item['kode_barang'] }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <select wire:model="items.{{ $i }}.price_type" wire:change="updatePriceType({{ $i }})" class="border border-gray-300 rounded px-2 py-1 text-xs">
                                        <option value="ecer">Ecer</option>
                                        <option value="grosir">Grosir</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Rp {{ number_format($item['unit_price'],0,',','.') }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input wire:model="items.{{ $i }}.quantity" wire:change="recalcItem({{ $i }})" type="number" min="1" max="{{ $item['stok'] }}" class="w-16 border border-gray-300 rounded px-2 py-1 text-xs text-center">
                                    <p class="text-[10px] mt-0.5 {{ $item['quantity'] >= $item['stok'] ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                        Sisa: {{ $item['stok'] - $item['quantity'] }} / {{ $item['stok'] }}
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($item['subtotal'],0,',','.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <button wire:click="removeItem({{ $i }})" class="text-red-400 hover:text-red-600">✕</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada barang dipilih</td></tr>
                            @endforelse
                        </tbody>
                        @if(count($items))
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 font-semibold text-right">TOTAL</td>
                                <td class="px-4 py-3 font-bold text-lg text-indigo-600 text-right">Rp {{ number_format($this->getTotal(),0,',','.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Order Info -->
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm p-5 space-y-4">
                <h3 class="font-semibold text-gray-700">Detail Transaksi</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                    <select wire:model="customer_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">-- Customer Umum --</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pembayaran</label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model="payment_type" type="radio" value="cash" class="text-indigo-600"> Cash
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input wire:model="payment_type" type="radio" value="tempo" class="text-indigo-600"> Tempo
                        </label>
                    </div>
                </div>
                @if($payment_type === 'tempo')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jatuh Tempo</label>
                    <input wire:model="due_date" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea wire:model="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                </div>
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-4">
                        <span class="font-semibold text-gray-700">Total Bayar</span>
                        <span class="text-2xl font-bold text-indigo-600">Rp {{ number_format($this->getTotal(),0,',','.') }}</span>
                    </div>
                    <button wire:click="save" wire:loading.attr="disabled" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 disabled:opacity-50">
                        <span wire:loading.remove>💾 Simpan Transaksi</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
