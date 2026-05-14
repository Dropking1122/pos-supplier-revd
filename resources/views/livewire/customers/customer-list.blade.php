<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-5">
        <input wire:model.live="search" type="text" placeholder="Cari customer..." class="w-full sm:w-72 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        <button wire:click="openCreate" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 whitespace-nowrap">➕ Tambah Customer</button>
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
                        <th class="px-4 py-3 text-left cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('name')">
                            <span class="flex items-center gap-1">Nama <span class="{{ $sortClass('name') }}">{{ $sortIcon('name') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-left cursor-pointer hover:bg-gray-100 select-none" wire:click="sort('phone')">
                            <span class="flex items-center gap-1">Telepon <span class="{{ $sortClass('phone') }}">{{ $sortIcon('phone') }}</span></span>
                        </th>
                        <th class="px-4 py-3 text-left">Alamat</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $customer->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $customer->phone ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $customer->address ?? '-' }}</td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <button wire:click="openEdit({{ $customer->id }})"
                                        class="inline-flex items-center gap-1.5 text-indigo-700 bg-indigo-100 hover:bg-indigo-200 border border-indigo-200 text-xs font-semibold px-2.5 py-1.5 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </button>
                                <button wire:click="delete({{ $customer->id }})" wire:confirm="Hapus customer ini?"
                                        class="inline-flex items-center gap-1.5 text-red-700 bg-red-100 hover:bg-red-200 border border-red-200 text-xs font-semibold px-2.5 py-1.5 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Belum ada customer</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $customers->links() }}</div>
    </div>
    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">{{ $editId ? 'Edit Customer' : 'Tambah Customer' }}</h3>
                <button wire:click="$set('showModal',false)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <form wire:submit="save" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                    <input wire:model="name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                    <input wire:model="phone" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea wire:model="address" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('showModal',false)" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
