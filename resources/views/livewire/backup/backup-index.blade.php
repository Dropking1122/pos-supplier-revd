<div class="space-y-6">

    {{-- Peringatan hanya admin --}}
    @if(!auth()->user()->is_admin)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-sm text-red-700 font-medium">Hanya admin yang dapat mengakses fitur Backup & Restore.</p>
    </div>
    @else

    {{-- ── ROW 1: Buat Backup Manual + Jadwal Otomatis ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Backup Manual --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 bg-indigo-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-gray-800 mb-1">Backup Manual</h3>
                    <p class="text-xs text-gray-500 mb-4">Ekspor seluruh data ke file <code class="bg-gray-100 px-1 rounded">.sql</code> dan langsung terunduh ke komputer Anda.</p>
                    <a href="{{ route('backup.create') }}"
                       class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Buat Backup Sekarang
                    </a>
                </div>
            </div>
        </div>

        {{-- Jadwal Otomatis --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-start gap-4 mb-4">
                <div class="w-11 h-11 {{ $scheduleEnabled ? 'bg-green-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-5 h-5 {{ $scheduleEnabled ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-800">Jadwal Backup Otomatis</h3>
                        {{-- Toggle --}}
                        <button wire:click="$toggle('scheduleEnabled')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none
                                       {{ $scheduleEnabled ? 'bg-green-500' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                         {{ $scheduleEnabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Status: <strong class="{{ $scheduleEnabled ? 'text-green-600' : 'text-gray-400' }}">{{ $scheduleEnabled ? 'Aktif' : 'Nonaktif' }}</strong>
                        @if($scheduleEnabled)
                        &nbsp;·&nbsp; Setiap hari pukul <strong class="text-gray-700">{{ $scheduleTime }}</strong>
                        @endif
                    </p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Waktu Backup</label>
                        <input wire:model="scheduleTime" type="time"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('scheduleTime') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Simpan Selama (hari)</label>
                        <input wire:model="retentionDays" type="number" min="1" max="365"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @error('retentionDays') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                @if($scheduleLastRun)
                <div class="bg-gray-50 rounded-lg px-3 py-2 text-xs text-gray-500">
                    <span class="font-medium text-gray-600">Backup otomatis terakhir:</span> {{ $scheduleLastRun }}
                    @if($scheduleLastFile)
                    <br><span class="font-mono text-gray-400">{{ $scheduleLastFile }}</span>
                    @endif
                </div>
                @endif

                <div class="flex items-center gap-2">
                    <button wire:click="saveSchedule" wire:loading.attr="disabled"
                            class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="saveSchedule">Simpan Jadwal</span>
                        <span wire:loading wire:target="saveSchedule">Menyimpan...</span>
                    </button>
                    <button wire:click="runNow" wire:loading.attr="disabled"
                            wire:confirm="Jalankan backup otomatis sekarang?"
                            title="Jalankan backup otomatis sekarang (untuk uji coba)"
                            class="px-3 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg text-sm font-medium transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="runNow">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <span wire:loading wire:target="runNow">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                        </span>
                    </button>
                </div>
                <p class="text-xs text-gray-400">Tombol ▶ untuk uji coba backup otomatis langsung.</p>
            </div>
        </div>
    </div>

    {{-- ── Import / Restore ── --}}
    <div class="bg-white rounded-xl shadow-sm p-6" x-data="{ confirmed: false, filename: '' }">
        <div class="flex items-start gap-4">
            <div class="w-11 h-11 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-gray-800 mb-1">Import / Restore Database</h3>
                <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 mb-4 flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-xs text-amber-700 leading-relaxed">
                        <strong>Perhatian:</strong> Import akan menimpa data yang ada sesuai isi file backup. Buat backup terbaru terlebih dahulu sebelum melakukan import. Proses ini tidak dapat dibatalkan.
                    </p>
                </div>
                <form action="{{ route('backup.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Pilih File Backup (.sql) <span class="text-red-500">*</span></label>
                            <input type="file" name="sql_file" accept=".sql,.txt" required
                                   x-on:change="filename = $event.target.files[0]?.name ?? ''; confirmed = false"
                                   class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-lg cursor-pointer">
                            <p class="text-xs text-gray-400 mt-1">Format: .sql &nbsp;·&nbsp; Ukuran maks: 100 MB</p>
                        </div>
                        <div x-show="filename" x-transition class="flex items-center gap-2">
                            <input type="checkbox" id="confirm-import" x-model="confirmed" class="w-4 h-4 text-amber-600 rounded border-gray-300 focus:ring-amber-500">
                            <label for="confirm-import" class="text-xs text-gray-700">
                                Saya mengerti import akan <strong class="text-red-600">menimpa data yang ada</strong> dan tidak dapat dibatalkan.
                            </label>
                        </div>
                        <button type="submit"
                                x-bind:disabled="!confirmed || !filename"
                                x-bind:class="(confirmed && filename) ? 'bg-amber-500 hover:bg-amber-600 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                                class="inline-flex items-center gap-2 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/></svg>
                            Import Database
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Riwayat Backup ── --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Riwayat Backup</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ count($backups) }} file backup tersimpan di server</p>
            </div>
            <div class="flex items-center gap-2">
                @if(count($backups) > 0)
                <span class="text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full">
                    Total: {{ collect($backups)->sum('size') >= 1048576 ? number_format(collect($backups)->sum('size') / 1048576, 1) . ' MB' : number_format(collect($backups)->sum('size') / 1024, 1) . ' KB' }}
                </span>
                @endif
                <span class="text-xs bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-full font-medium">Manual</span>
                <span class="text-xs bg-green-50 text-green-600 px-2.5 py-1 rounded-full font-medium">Otomatis</span>
            </div>
        </div>

        @if(empty($backups))
        <div class="px-6 py-12 text-center">
            <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            </div>
            <p class="text-gray-400 text-sm font-medium">Belum ada backup</p>
            <p class="text-gray-300 text-xs mt-1">Klik "Buat Backup Sekarang" atau aktifkan jadwal otomatis</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Nama File</th>
                        <th class="px-6 py-3 text-center">Tipe</th>
                        <th class="px-6 py-3 text-center">Ukuran</th>
                        <th class="px-6 py-3 text-center">Dibuat</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($backups as $i => $backup)
                    <tr class="hover:bg-gray-50 {{ $i === 0 ? 'bg-blue-50/20' : '' }}">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 {{ $backup['is_auto'] ? 'bg-green-100' : 'bg-indigo-100' }} rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 {{ $backup['is_auto'] ? 'text-green-500' : 'text-indigo-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($backup['is_auto'])
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        @endif
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-mono text-xs text-gray-700 font-medium">{{ $backup['name'] }}</p>
                                    @if($i === 0)
                                    <span class="text-xs text-blue-500 font-medium">Terbaru</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            @if($backup['is_auto'])
                            <span class="text-xs bg-green-50 text-green-700 px-2.5 py-0.5 rounded-full font-semibold">Otomatis</span>
                            @else
                            <span class="text-xs bg-indigo-50 text-indigo-700 px-2.5 py-0.5 rounded-full font-semibold">Manual</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-2.5 py-1 rounded-full">{{ $backup['size_label'] }}</span>
                        </td>
                        <td class="px-6 py-3.5 text-center text-xs text-gray-500">{{ $backup['created_at'] }}</td>
                        <td class="px-6 py-3.5 text-center whitespace-nowrap">
                            <a href="{{ route('backup.download', $backup['name']) }}"
                               class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1.5 rounded-lg transition-colors mr-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Unduh
                            </a>
                            <form action="{{ route('backup.delete', $backup['name']) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Hapus backup ini? Tindakan tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 text-xs bg-red-50 hover:bg-red-100 px-2.5 py-1.5 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center gap-2">
            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-xs text-gray-400">
                File backup tersimpan di <code class="bg-gray-200 px-1 rounded">storage/app/backups/</code>.
                Backup lama otomatis dihapus setelah <strong>{{ $retentionDays }} hari</strong>.
                Log backup di <code class="bg-gray-200 px-1 rounded">storage/logs/backup.log</code>.
            </p>
        </div>
    </div>

    @endif
</div>
