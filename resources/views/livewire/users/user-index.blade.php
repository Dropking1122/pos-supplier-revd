<div>
    <div class="flex flex-col sm:flex-row gap-3 mb-4 items-center justify-between">
        <input wire:model.live="search" type="text" placeholder="Cari nama / email..."
               class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        @if(auth()->user()->is_admin)
        <button wire:click="openCreate"
                class="flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah User
        </button>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-center">Total Transaksi</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center shrink-0">
                                    <span class="text-indigo-600 text-xs font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                    @if($user->id === auth()->id())
                                    <span class="text-xs text-indigo-500 font-medium">(Anda)</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold">
                                {{ $user->sales_count }} transaksi
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Aktif</span>
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            @if(auth()->user()->is_admin)
                            <button wire:click="openEdit({{ $user->id }})"
                                    class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded transition-colors mr-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </button>
                            @if($user->id !== auth()->id())
                            <button wire:click="confirmDelete({{ $user->id }})"
                                    class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 text-xs bg-red-50 hover:bg-red-100 px-2 py-1 rounded transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                            @endif
                            @else
                            <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada user</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $users->links() }}</div>
    </div>

    {{-- Create / Edit Modal --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-bold text-gray-800">{{ $editId ? 'Edit User' : 'Tambah User Baru' }}</h3>
                    <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input wire:model="name" type="text" placeholder="Nama kasir / admin"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input wire:model="email" type="email" placeholder="email@toko.com"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password {{ $editId ? '(kosongkan jika tidak diubah)' : '*' }}
                        </label>
                        <input wire:model="password" type="password" placeholder="Min. 8 karakter"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                        <input wire:model="password_confirmation" type="password" placeholder="Ulangi password"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button wire:click="$set('showModal', false)"
                            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="save" wire:loading.attr="disabled"
                            class="flex-1 px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 disabled:opacity-60 transition-colors">
                        <span wire:loading.remove wire:target="save">{{ $editId ? 'Simpan Perubahan' : 'Buat User' }}</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Modal --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Hapus User?</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $deleteName }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 mb-5">
                    Data transaksi yang sudah dilakukan user ini tidak akan terhapus. Nama petugas di riwayat transaksi akan kosong.
                </p>
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="deleteUser" wire:loading.attr="disabled"
                            class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-xl text-sm font-semibold hover:bg-red-700 disabled:opacity-60 transition-colors">
                        <span wire:loading.remove wire:target="deleteUser">Ya, Hapus</span>
                        <span wire:loading wire:target="deleteUser">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
