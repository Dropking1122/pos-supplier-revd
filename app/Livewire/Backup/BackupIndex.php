<?php
namespace App\Livewire\Backup;

use Livewire\Component;

class BackupIndex extends Component
{
    public function getBackupsProperty(): array
    {
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) {
            return [];
        }

        $files = glob($dir . '/backup_*.sql');
        if (!$files) return [];

        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

        return array_map(function ($path) {
            $name = basename($path);
            $size = filesize($path);
            $time = filemtime($path);
            return [
                'name'       => $name,
                'size'       => $size,
                'size_label' => $this->formatBytes($size),
                'created_at' => date('d/m/Y H:i:s', $time),
                'timestamp'  => $time,
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
