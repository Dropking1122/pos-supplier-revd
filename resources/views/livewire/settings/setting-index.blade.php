<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-5">Informasi Toko</h3>
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko <span class="text-red-500">*</span></label>
                        <input wire:model="company_name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('company_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input wire:model="company_phone" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea wire:model="company_address" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Petugas</label>
                        <input wire:model="petugas" type="text" placeholder="Nama kasir / petugas toko" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <p class="text-xs text-gray-400 mt-1">Tampil di invoice dan struk penjualan</p>
                        @error('petugas') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Invoice</label>
                        <textarea wire:model="invoice_footer" rows="2" placeholder="Terima kasih telah berbelanja!" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo Toko</label>
                        <input wire:model="logo" type="file" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('logo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="flex items-center gap-2 bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            <!-- Preview Toko -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Preview Toko</h3>
                <div class="text-center">
                    @if($setting->company_logo)
                    <img src="{{ Storage::url($setting->company_logo) }}" alt="Logo" class="w-24 h-24 object-contain mx-auto mb-3 rounded-lg border">
                    @else
                    <div class="w-16 h-16 bg-indigo-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    @endif
                    <h4 class="font-bold text-gray-800 text-lg">{{ $setting->company_name }}</h4>
                    <p class="text-sm text-gray-500 mt-1">{{ $setting->company_phone }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $setting->company_address }}</p>
                    @if($setting->petugas)
                    <p class="text-xs text-indigo-600 mt-2 font-medium">Petugas: {{ $setting->petugas }}</p>
                    @endif
                    @if($setting->invoice_footer)
                    <p class="text-xs text-gray-400 mt-3 italic border-t pt-3">{{ $setting->invoice_footer }}</p>
                    @endif
                </div>
            </div>

            <!-- Info Card -->
            <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-indigo-700 mb-1">Info</p>
                        <p class="text-xs text-indigo-600 leading-relaxed">Nama Petugas akan otomatis muncul di setiap invoice dan struk yang dicetak.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
