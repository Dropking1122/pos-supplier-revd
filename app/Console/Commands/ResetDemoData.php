<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetDemoData extends Command
{
    protected $signature   = 'demo:reset {--keep-products : Pertahankan data produk} {--yes : Skip konfirmasi}';
    protected $description = 'Reset semua data transaksi (kecuali user & pengaturan) untuk demo / testing';

    public function handle()
    {
        if (!$this->option('yes')) {
            $this->newLine();
            $this->line('  <fg=red;options=bold>⚠  PERINGATAN: Tindakan ini tidak dapat dibatalkan!</>');
            $this->newLine();
            $this->line('  Data yang akan <fg=red>DIHAPUS</>:');
            $this->line('    • Semua transaksi penjualan & detailnya');
            $this->line('    • Semua data hutang & cicilan');
            $this->line('    • Semua data customer');
            if (!$this->option('keep-products')) {
                $this->line('    • Semua data produk');
            }
            $this->newLine();
            $this->line('  Data yang <fg=green>DIPERTAHANKAN</>:');
            $this->line('    • Akun user (login tidak berubah)');
            $this->line('    • Pengaturan toko (nama, logo, dll)');
            if ($this->option('keep-products')) {
                $this->line('    • Data produk (--keep-products)');
            }
            $this->newLine();

            if (!$this->confirm('  Lanjutkan reset database?', false)) {
                $this->info('  Reset dibatalkan.');
                return 0;
            }
        }

        $this->newLine();
        $this->output->write('  Menghapus data...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('debt_payments')->truncate();
        DB::table('debts')->truncate();
        DB::table('sale_details')->truncate();
        DB::table('sales')->truncate();
        DB::table('customers')->truncate();

        if (!$this->option('keep-products')) {
            DB::table('products')->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->line(' <fg=green>✓</>');
        $this->newLine();
        $this->line('  <fg=green;options=bold>Reset berhasil!</>');
        $this->line('  Login masih aktif — tidak ada data transaksi atau customer.');
        $this->newLine();

        return 0;
    }
}
