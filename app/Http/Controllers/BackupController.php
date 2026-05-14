<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    private function backupDir(): string
    {
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    private function guardAdmin()
    {
        if (!auth()->user()?->is_admin) {
            abort(403, 'Hanya admin yang dapat mengakses fitur backup.');
        }
    }

    public function create()
    {
        $this->guardAdmin();

        $pdo    = DB::connection()->getPdo();
        $dbName = config('database.connections.mysql.database');

        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        $sql  = "-- =============================================\n";
        $sql .= "-- POS Supplier - Database Backup\n";
        $sql .= "-- Dibuat: " . now()->format('d/m/Y H:i:s') . "\n";
        $sql .= "-- Database: {$dbName}\n";
        $sql .= "-- =============================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET NAMES utf8mb4;\n\n";

        foreach ($tables as $table) {
            $sql .= "-- -------------------------------------------\n";
            $sql .= "-- Tabel: `{$table}`\n";
            $sql .= "-- -------------------------------------------\n";

            $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $createKey  = array_key_exists('Create Table', $createStmt) ? 'Create Table' : 'Create View';

            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createStmt[$createKey] . ";\n\n";

            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $columns = array_keys($rows[0]);
                $colList = '`' . implode('`, `', $columns) . '`';

                $chunkSize = 500;
                foreach (array_chunk($rows, $chunkSize) as $chunk) {
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

        $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path     = $this->backupDir() . '/' . $filename;

        file_put_contents($path, $sql);

        return response()->download($path, $filename, [
            'Content-Type'        => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function download(string $filename)
    {
        $this->guardAdmin();

        $filename = basename($filename);
        if (!preg_match('/^backup_[\d_-]+\.sql$/', $filename)) {
            abort(404);
        }

        $path = $this->backupDir() . '/' . $filename;
        if (!file_exists($path)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }

    public function import(Request $request)
    {
        $this->guardAdmin();

        $request->validate([
            'sql_file' => 'required|file|max:102400',
        ], [
            'sql_file.required' => 'File SQL wajib dipilih.',
            'sql_file.max'      => 'Ukuran file maksimal 100 MB.',
        ]);

        $file      = $request->file('sql_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['sql', 'txt'])) {
            return back()->with('toast_error', 'Hanya file .sql yang diizinkan untuk diimport.');
        }

        // Validasi MIME type secara server-side
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file->getPathname());
        $allowedMimes = ['text/plain', 'application/sql', 'application/x-sql', 'application/octet-stream'];
        if (!in_array($mime, $allowedMimes, true)) {
            return back()->with('toast_error', 'Tipe file tidak valid. Hanya file SQL teks yang diizinkan.');
        }

        $sqlContent = file_get_contents($file->getPathname());

        if (empty(trim($sqlContent))) {
            return back()->with('toast_error', 'File SQL kosong atau tidak valid.');
        }

        try {
            DB::unprepared($sqlContent);
            return back()->with('toast_success', 'Database berhasil diimport dari file "' . $file->getClientOriginalName() . '".');
        } catch (\Throwable $e) {
            return back()->with('toast_error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function delete(string $filename)
    {
        $this->guardAdmin();

        $filename = basename($filename);
        if (!preg_match('/^backup_[\d_-]+\.sql$/', $filename)) {
            abort(404);
        }

        $path = $this->backupDir() . '/' . $filename;
        if (file_exists($path)) {
            unlink($path);
        }

        return back()->with('toast_success', 'Backup "' . $filename . '" berhasil dihapus.');
    }
}
