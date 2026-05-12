<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackupDatabase extends Command
{
    protected $signature   = 'app:backup-database {--retention= : Jumlah hari simpan backup (override settings)}';
    protected $description = 'Buat backup database otomatis dan hapus backup lama';

    private function backupDir(): string
    {
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    public function handle(): int
    {
        $settings      = $this->loadSettings();
        $retentionDays = (int) ($this->option('retention') ?? $settings['retention_days'] ?? 7);

        $this->info('[' . now()->format('Y-m-d H:i:s') . '] Memulai backup database otomatis...');

        try {
            $filename = $this->createBackup();
            $this->info("Backup berhasil dibuat: {$filename}");
        } catch (\Throwable $e) {
            $this->error("Backup GAGAL: " . $e->getMessage());
            $this->logResult('GAGAL: ' . $e->getMessage());
            return self::FAILURE;
        }

        // Hapus backup lama
        $deleted = $this->pruneOldBackups($retentionDays);
        if ($deleted > 0) {
            $this->info("Backup lama dihapus: {$deleted} file (> {$retentionDays} hari)");
        }

        $this->logResult('OK: ' . $filename);

        // Update last_run di settings
        $settings['last_run']  = now()->format('Y-m-d H:i:s');
        $settings['last_file'] = $filename;
        $this->saveSettings($settings);

        $this->info('Backup selesai.');
        return self::SUCCESS;
    }

    private function createBackup(): string
    {
        $pdo    = DB::connection()->getPdo();
        $dbName = config('database.connections.mysql.database');
        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        $sql  = "-- =============================================\n";
        $sql .= "-- POS Supplier - Backup Otomatis\n";
        $sql .= "-- Dibuat: " . now()->format('d/m/Y H:i:s') . "\n";
        $sql .= "-- Database: {$dbName}\n";
        $sql .= "-- =============================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET NAMES utf8mb4;\n\n";

        foreach ($tables as $table) {
            $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $createKey  = array_key_exists('Create Table', $createStmt) ? 'Create Table' : 'Create View';

            $sql .= "-- Tabel: `{$table}`\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createStmt[$createKey] . ";\n\n";

            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $columns = array_keys($rows[0]);
                $colList = '`' . implode('`, `', $columns) . '`';

                foreach (array_chunk($rows, 500) as $chunk) {
                    $sql .= "INSERT INTO `{$table}` ({$colList}) VALUES\n";
                    $values = [];
                    foreach ($chunk as $row) {
                        $vals = array_map(function ($v) use ($pdo) {
                            if ($v === null) return 'NULL';
                            if (is_numeric($v)) return $v;
                            return $pdo->quote($v);
                        }, array_values($row));
                        $values[] = '  (' . implode(', ', $vals) . ')';
                    }
                    $sql .= implode(",\n", $values) . ";\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $filename = 'backup_auto_' . now()->format('Y-m-d_H-i-s') . '.sql';
        file_put_contents($this->backupDir() . '/' . $filename, $sql);

        return $filename;
    }

    private function pruneOldBackups(int $retentionDays): int
    {
        $dir   = $this->backupDir();
        $files = glob($dir . '/backup_*.sql');
        if (!$files) return 0;

        $cutoff  = now()->subDays($retentionDays)->getTimestamp();
        $deleted = 0;

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
                $deleted++;
            }
        }

        return $deleted;
    }

    private function logResult(string $message): void
    {
        $logFile = storage_path('logs/backup.log');
        $line    = '[' . now()->format('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        file_put_contents($logFile, $line, FILE_APPEND);
    }

    private function loadSettings(): array
    {
        $file = storage_path('app/backup_schedule.json');
        if (!file_exists($file)) return [];
        return json_decode(file_get_contents($file), true) ?? [];
    }

    private function saveSettings(array $settings): void
    {
        file_put_contents(
            storage_path('app/backup_schedule.json'),
            json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
