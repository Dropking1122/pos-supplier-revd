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
                <div class="flex items-center justify-between px-4 pt-3 pb-1">
                    <span class="text-xs text-gray-500 font-medium">{{ count($items) }} barang dipilih</span>
                    <button wire:click="toggleInputMode" type="button"
                        class="flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-full border font-semibold transition-colors
                               {{ $inputMode === 'sisa' ? 'bg-amber-100 border-amber-300 text-amber-700' : 'bg-gray-100 border-gray-300 text-gray-600 hover:bg-gray-200' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        {{ $inputMode === 'qty' ? 'Mode: Input Qty Jual' : 'Mode: Input Sisa Stok' }}
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Barang</th>
                                <th class="px-4 py-3 text-center">Harga</th>
                                <th class="px-4 py-3 text-center">
                                    @if($inputMode === 'qty') Qty Jual @else Sisa Stok @endif
                                </th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($items as $i => $item)
                            @php $isLoss = (float)$item['unit_price'] < (float)$item['modal_awal']; @endphp
                            <tr class="{{ $isLoss ? 'bg-red-50' : '' }}">
                                <td class="px-4 py-3">
                                    <p class="font-medium">{{ $item['nama_barang'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $item['kode_barang'] }}</p>
                                    @if($isLoss)
                                    <p class="text-[10px] text-red-600 font-semibold mt-0.5 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                                        Harga jual di bawah modal! (rugi Rp {{ number_format((float)$item['modal_awal'] - (float)$item['unit_price'],0,',','.') }}/unit)
                                    </p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <select wire:model="items.{{ $i }}.price_type" wire:change="updatePriceType({{ $i }})" class="border border-gray-300 rounded pl-2 pr-7 py-1 text-xs appearance-none bg-white" style="background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 4px center;background-size:14px;">
                                        <option value="ecer">Ecer</option>
                                        <option value="grosir">Grosir</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Rp {{ number_format($item['unit_price'],0,',','.') }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($inputMode === 'qty')
                                        <input wire:model.live.debounce.300ms="items.{{ $i }}.quantity" type="number" min="1" max="{{ $item['stok'] }}" inputmode="numeric"
                                               x-on:focus="setTimeout(() => $el.scrollIntoView({behavior:'smooth',block:'center'}), 350)"
                                               class="w-16 border border-gray-300 rounded px-2 py-1 text-xs text-center">
                                        <p class="text-[10px] mt-0.5 {{ $item['quantity'] >= $item['stok'] ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                            Sisa: {{ $item['stok'] - $item['quantity'] }} / {{ $item['stok'] }}
                                        </p>
                                    @else
                                        <input wire:model.live.debounce.300ms="items.{{ $i }}.sisa_input" type="number" min="0" max="{{ $item['stok'] - 1 }}" inputmode="numeric"
                                               x-on:focus="setTimeout(() => $el.scrollIntoView({behavior:'smooth',block:'center'}), 350)"
                                               class="w-16 border border-amber-300 bg-amber-50 rounded px-2 py-1 text-xs text-center focus:ring-1 focus:ring-amber-400">
                                        <p class="text-[10px] mt-0.5 text-amber-600 font-semibold">
                                            Jual: {{ $item['quantity'] }} / {{ $item['stok'] }}
                                        </p>
                                    @endif
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Customer
                        @if($payment_type === 'tempo') <span class="text-red-500">*</span> @endif
                    </label>
                    <select wire:model="customer_id"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:outline-none
                                   {{ $payment_type === 'tempo' && !$customer_id ? 'border-orange-300 focus:ring-orange-400' : 'border-gray-300 focus:ring-indigo-500' }}">
                        <option value="">-- Customer Umum --</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @if($payment_type === 'tempo' && !$customer_id)
                    <p class="text-xs text-orange-500 mt-0.5">Customer wajib dipilih untuk pembayaran tempo.</p>
                    @endif
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jatuh Tempo <span class="text-red-500">*</span></label>
                    <input wire:model="due_date" type="date" min="{{ now()->format('Y-m-d') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:outline-none
                                  {{ !$due_date ? 'border-orange-300 focus:ring-orange-400' : 'border-gray-300 focus:ring-indigo-500' }}">
                    @if(!$due_date)
                    <p class="text-xs text-orange-500 mt-0.5">Tanggal jatuh tempo wajib diisi.</p>
                    @endif
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
