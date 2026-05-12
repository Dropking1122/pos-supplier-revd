<?php
namespace App\Livewire\Backup;

use Livewire\Component;

class BackupIndex extends Component
{
    public bool   $scheduleEnabled   = false;
    public string $scheduleTime      = '00:00';
    public int    $retentionDays     = 7;
    public string $scheduleLastRun   = '';
    public string $scheduleLastFile  = '';
    public bool   $scheduleSaved     = false;

    private function settingsPath(): string
    {
        return storage_path('app/backup_schedule.json');
    }

    public function mount(): void
    {
        $this->loadScheduleSettings();
    }

    private function loadScheduleSettings(): void
    {
        $file = $this->settingsPath();
        if (!file_exists($file)) return;

        $cfg = json_decode(file_get_contents($file), true) ?? [];

        $this->scheduleEnabled  = (bool) ($cfg['enabled']        ?? false);
        $this->scheduleTime     = $cfg['time']                    ?? '00:00';
        $this->retentionDays    = (int)  ($cfg['retention_days'] ?? 7);
        $this->scheduleLastRun  = $cfg['last_run']                ?? '';
        $this->scheduleLastFile = $cfg['last_file']               ?? '';
    }

    public function saveSchedule(): void
    {
        if (!auth()->user()->is_admin) {
            $this->dispatch('toast', type: 'error', message: 'Hanya admin yang dapat mengubah jadwal backup.');
            return;
        }

        $this->validate([
            'scheduleTime'   => 'required|date_format:H:i',
            'retentionDays'  => 'required|integer|min:1|max:365',
        ], [
            'scheduleTime.required'    => 'Waktu backup wajib diisi.',
            'scheduleTime.date_format' => 'Format waktu harus HH:MM.',
            'retentionDays.min'        => 'Minimum retensi 1 hari.',
            'retentionDays.max'        => 'Maksimum retensi 365 hari.',
        ]);

        $existing = [];
        $file     = $this->settingsPath();
        if (file_exists($file)) {
            $existing = json_decode(file_get_contents($file), true) ?? [];
        }

        $cfg = array_merge($existing, [
            'enabled'        => $this->scheduleEnabled,
            'time'           => $this->scheduleTime,
            'retention_days' => $this->retentionDays,
        ]);

        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }

        file_put_contents($file, json_encode($cfg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->scheduleSaved = true;
        $msg = $this->scheduleEnabled
            ? "Jadwal backup aktif setiap hari pukul {$this->scheduleTime}."
            : "Jadwal backup dinonaktifkan.";

        $this->dispatch('toast', type: 'success', message: $msg);
    }

    public function runNow(): void
    {
        if (!auth()->user()->is_admin) {
            $this->dispatch('toast', type: 'error', message: 'Hanya admin yang dapat menjalankan backup.');
            return;
        }
        \Artisan::call('app:backup-database');
        $this->loadScheduleSettings();
        $this->dispatch('toast', type: 'success', message: 'Backup otomatis berhasil dijalankan.');
    }

    public function getBackupsProperty(): array
    {
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) return [];

        $files = glob($dir . '/backup_*.sql');
        if (!$files) return [];

        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

        return array_map(function ($path) {
            $name  = basename($path);
            $size  = filesize($path);
            $time  = filemtime($path);
            $isAuto = str_starts_with($name, 'backup_auto_');
            return [
                'name'       => $name,
                'size'       => $size,
                'size_label' => $this->formatBytes($size),
                'created_at' => date('d/m/Y H:i:s', $time),
                'timestamp'  => $time,
                'is_auto'    => $isAuto,
            ];
        }, $files);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function render()
    {
        return view('livewire.backup.backup-index', [
            'backups' => $this->backups,
        ]);
    }
}
