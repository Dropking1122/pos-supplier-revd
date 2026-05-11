<div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-5">Informasi Toko</h3>
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko *</label>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Invoice</label>
                        <textarea wire:model="invoice_footer" rows="2" placeholder="Terima kasih telah berbelanja!" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo Toko</label>
                        <input wire:model="logo" type="file" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @error('logo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-indigo-700">
                            💾 Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Preview Toko</h3>
                <div class="text-center">
                    @if($setting->company_logo)
                    <img src="{{ Storage::url($setting->company_logo) }}" alt="Logo" class="w-24 h-24 object-contain mx-auto mb-3 rounded-lg border">
                    @else
                    <div class="w-24 h-24 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-3 text-3xl">🏪</div>
                    @endif
                    <h4 class="font-bold text-gray-800 text-lg">{{ $setting->company_name }}</h4>
                    <p class="text-sm text-gray-500 mt-1">{{ $setting->company_phone }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $setting->company_address }}</p>
                    @if($setting->invoice_footer)
                    <p class="text-xs text-gray-400 mt-3 italic border-t pt-3">{{ $setting->invoice_footer }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
