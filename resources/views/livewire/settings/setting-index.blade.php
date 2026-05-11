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

                    <!-- Logo Upload -->
                    <div x-data="{
                        preview: null,
                        fileName: null,
                        hasNew: false,
                        handleChange(e) {
                            const file = e.target.files[0];
                            if (!file) return;
                            this.fileName = file.name;
                            this.hasNew = true;
                            const reader = new FileReader();
                            reader.onload = ev => { this.preview = ev.target.result; };
                            reader.readAsDataURL(file);
                        },
                        clearNew() {
                            this.preview = null;
                            this.fileName = null;
                            this.hasNew = false;
                            this.$refs.fileInput.value = '';
                            @this.set('logo', null);
                        }
                    }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Logo Toko</label>

                        {{-- Logo saat ini --}}
                        @if($setting->company_logo)
                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg border border-gray-200 mb-3" x-show="!hasNew">
                            <img src="/storage/{{ $setting->company_logo }}"
                                 alt="Logo saat ini"
                                 class="w-16 h-16 object-contain rounded-lg border bg-white p-1">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-700">Logo terpasang</p>
                                <p class="text-xs text-gray-400 truncate">{{ basename($setting->company_logo) }}</p>
                            </div>
                            <button type="button"
                                    wire:click="deleteLogo"
                                    wire:confirm="Yakin hapus logo toko?"
                                    class="flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors font-medium shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </div>
                        @endif

                        {{-- Preview logo baru yang dipilih --}}
                        <div x-show="hasNew" x-cloak class="flex items-center gap-4 p-3 bg-indigo-50 rounded-lg border border-indigo-200 mb-3">
                            <img :src="preview" alt="Preview" class="w-16 h-16 object-contain rounded-lg border bg-white p-1">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-indigo-700">Logo baru dipilih</p>
                                <p class="text-xs text-indigo-500 truncate" x-text="fileName"></p>
                                <p class="text-xs text-gray-400 mt-0.5">Klik "Simpan Pengaturan" untuk menyimpan</p>
                            </div>
                            <button type="button" @click="clearNew()"
                                    class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-700 bg-white hover:bg-gray-100 px-3 py-1.5 rounded-lg border transition-colors font-medium shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Batal
                            </button>
                        </div>

                        {{-- Zone upload --}}
                        <label x-show="!hasNew"
                               class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-indigo-50 hover:border-indigo-400 transition-colors group">
                            <div class="flex flex-col items-center gap-2 text-gray-400 group-hover:text-indigo-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <div class="text-center">
                                    <p class="text-sm font-medium">Klik untuk pilih logo</p>
                                    <p class="text-xs mt-0.5">PNG, JPG, GIF, WebP — maks. 2 MB</p>
                                </div>
                            </div>
                            <input x-ref="fileInput"
                                   wire:model="logo"
                                   type="file"
                                   accept="image/png,image/jpeg,image/gif,image/webp"
                                   class="hidden"
                                   @change="handleChange($event)">
                        </label>

                        {{-- Loading indicator saat Livewire upload --}}
                        <div wire:loading wire:target="logo" class="flex items-center gap-2 mt-2 text-xs text-indigo-600">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Mengunggah...
                        </div>

                        @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="flex items-center gap-2 bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-indigo-700 transition-colors"
                                wire:loading.attr="disabled" wire:loading.class="opacity-75">
                            <svg class="w-4 h-4" wire:loading.remove wire:target="save" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            <svg class="w-4 h-4 animate-spin" wire:loading wire:target="save" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
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
                    <img src="/storage/{{ $setting->company_logo }}"
                         alt="Logo"
                         class="w-24 h-24 object-contain mx-auto mb-3 rounded-xl border bg-gray-50 p-2">
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

            <!-- Info -->
            <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-indigo-700">Info</p>
                        <p class="text-xs text-indigo-600 leading-relaxed">Logo akan tampil di bagian atas setiap invoice yang dicetak.</p>
                        <p class="text-xs text-indigo-600 leading-relaxed">Nama Petugas akan muncul di setiap invoice dan struk.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
