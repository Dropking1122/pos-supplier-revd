<div>
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        <input wire:model.live="search" type="text" placeholder="Cari customer..." class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        <select wire:model.live="filterStatus" class="border border-gray-300 rounded-lg pl-3 pr-9 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none appearance-none bg-white" style="background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 8px center;background-size:16px;">
            <option value="">Semua Status</option>
            <option value="belum_lunas">Belum Lunas</option>
            <option value="lunas">Lunas</option>
        </select>
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
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Invoice</th>
                        <th class="px-4 py-3 text-right cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('total_hutang')">
                            <span class="flex items-center justify-end gap-1">Total Hutang <span class="{{ $sortClass('total_hutang') }}">{{ $sortIcon('total_hutang') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-right cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('total_bayar')">
                            <span class="flex items-center justify-end gap-1">Sudah Bayar <span class="{{ $sortClass('total_bayar') }}">{{ $sortIcon('total_bayar') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-right cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('sisa_hutang')">
                            <span class="flex items-center justify-end gap-1">Sisa Hutang <span class="{{ $sortClass('sisa_hutang') }}">{{ $sortIcon('sisa_hutang') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-center cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('jatuh_tempo')">
                            <span class="flex items-center justify-center gap-1">Jatuh Tempo <span class="{{ $sortClass('jatuh_tempo') }}">{{ $sortIcon('jatuh_tempo') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-center cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('status')">
                            <span class="flex items-center justify-center gap-1">Status <span class="{{ $sortClass('status') }}">{{ $sortIcon('status') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($debts as $debt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $debt->customer->name }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-indigo-600">{{ $debt->sale?->invoice_number ?? '-' }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">Rp {{ number_format($debt->total_hutang,0,',','.') }}</td>
                        <td class="px-4 py-3 text-right text-green-600 whitespace-nowrap">Rp {{ number_format($debt->total_bayar,0,',','.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-red-600 whitespace-nowrap">Rp {{ number_format($debt->sisa_hutang,0,',','.') }}</td>
                        <td class="px-4 py-3 text-center {{ $debt->jatuh_tempo && $debt->jatuh_tempo->isPast() && $debt->status === 'belum_lunas' ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                            {{ $debt->jatuh_tempo?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $debt->status === 'lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $debt->status === 'lunas' ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($debt->status === 'belum_lunas')
                            <button wire:click="openPay({{ $debt->id }})" class="text-xs bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700">💰 Bayar</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada data hutang</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $debts->links() }}</div>
    </div>

    @if($showPayModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">Catat Pembayaran</h3>
                <button wire:click="$set('showPayModal',false)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <form wire:submit="savePayment" class="p-6 space-y-4">
                @php $activeDebt = $payDebtId ? \App\Models\Debt::find($payDebtId) : null; @endphp
                @if($activeDebt)
                <div class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-2.5 text-sm flex justify-between items-center">
                    <span class="text-blue-700">Sisa hutang saat ini</span>
                    <span class="font-bold text-blue-800">Rp {{ number_format($activeDebt->sisa_hutang,0,',','.') }}</span>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Bayar (Rp) *</label>
                    <input wire:model="payAmount" type="number" min="1" step="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @if($activeDebt && (float)$payAmount > $activeDebt->sisa_hutang)
                    <p class="text-xs text-amber-600 mt-1">Nominal melebihi sisa hutang. Akan dicatat sebagai pelunasan penuh (Rp {{ number_format($activeDebt->sisa_hutang,0,',','.') }}).</p>
                    @endif
                    @error('payAmount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar *</label>
                    <input wire:model="payDate" type="date" style="color-scheme: light" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none text-gray-800">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea wire:model="payNotes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('showPayModal',false)" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
