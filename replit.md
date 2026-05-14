# POS Supplier Laravel

A full-featured Point of Sale Supplier system built with Laravel 13, Livewire 3, and TailwindCSS 4.

## PENTING — Baca Ini Dulu

**Database WAJIB MySQL**, bukan SQLite. Jangan pernah mengubah ke SQLite.
Semua query menggunakan fungsi MySQL: `MONTH()`, `YEAR()`, `DATE()` — bukan `strftime()`.

---

## Tech Stack
- **Backend**: Laravel 13 (PHP 8.4)
- **Frontend**: Livewire 3, TailwindCSS 4, Alpine.js
- **Database**: MySQL 8.0
- **Auth**: Laravel Breeze (Livewire stack)
- **PDF**: DomPDF (`barryvdh/laravel-dompdf`)
- **Excel**: PhpSpreadsheet (`phpoffice/phpspreadsheet`)

---

## Database — MySQL 8.0

### Konfigurasi `.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_supplier
DB_USERNAME=root
DB_PASSWORD=pos_password
DB_SOCKET=/home/runner/mysql-run/mysql.sock
```

### Cara MySQL Berjalan
MySQL dijalankan otomatis lewat `start.sh` (workflow "Start application"):
- Data dir: `/home/runner/mysql-run/data`
- Socket: `/home/runner/mysql-run/mysql.sock`
- PID: `/home/runner/mysql-run/mysql.pid`
- Port: 3306
- Password root: `pos_password`

### Jika Ada Masalah Database
1. Cek workflow "Start application" sudah running
2. Restart workflow jika perlu
3. Jangan ubah `DB_CONNECTION` ke `sqlite`

---

## Login Credentials (development seed)
- Email: `admin@pos.com`
- Password: `password`

---

## Struktur Aplikasi

### Routes utama (`routes/web.php`)
Semua route dilindungi middleware `auth`:
- `GET /dashboard` — Dashboard
- `GET /products` — Data Barang
- `GET /sales` — Riwayat Penjualan
- `GET /sales/create` — Transaksi Baru
- `GET /sales/{id}/invoice` — Laporan Internal (detail: modal, profit, stok)
- `GET /sales/{id}/invoice-customer` — Invoice Customer (ringkas: tanpa modal/profit)
- `GET /sales/{id}/invoice-excel` — Export Excel per transaksi
- `GET /customers` — Data Customer
- `GET /debts` — Hutang Customer
- `GET /reports` — Laporan (Harian/Bulanan/Tahunan + Stok Harian)
- `GET /reports/export` — Export Excel Laporan Penjualan
- `GET /reports/pdf` — Export PDF Laporan Penjualan
- `GET /reports/stock-export` — Export Excel Stok Harian
- `GET /settings` — Pengaturan

### Controllers
- `app/Http/Controllers/InvoiceController.php` — `show()` laporan internal, `showCustomer()` invoice customer
- `app/Http/Controllers/ExportController.php` — semua fungsi export Excel & PDF
- `app/Http/Controllers/BackupController.php` — backup/restore database

### Livewire Components
- `app/Livewire/Dashboard.php` — gunakan `MONTH()` dan `YEAR()` bukan `strftime()`
- `app/Livewire/Products/ProductList.php`
- `app/Livewire/Sales/SaleCreate.php` — quantity input pakai `wire:model.blur`
- `app/Livewire/Sales/SaleList.php`
- `app/Livewire/Debts/DebtList.php`
- `app/Livewire/Reports/ReportIndex.php`
- `app/Livewire/Settings/SettingForm.php`

### Views Penting
- `resources/views/sales/invoice.blade.php` — Laporan Internal (judul: LAPORAN PENJUALAN)
- `resources/views/sales/invoice-customer.blade.php` — Invoice Customer (simpel, A5)
- `resources/views/livewire/sales/sale-list.blade.php`
- `resources/views/livewire/reports/report-index.blade.php`

---

## Fitur Lengkap
- Dashboard: total penjualan, profit, hutang, stok menipis, grafik bulanan
- Data Barang: multi-harga (grosir/ecer), stok minimum, alert stok rendah
- Transaksi Baru: cash & tempo/kredit, pilih harga grosir/ecer per item
- Riwayat Penjualan: filter customer/status/tanggal, sort kolom, hapus transaksi
- Hutang Customer: cicilan pembayaran, tracking sisa hutang
- **Invoice Customer** (ringkas — untuk dicetak/diberikan ke customer): nama barang, qty, harga, subtotal, total
- **Laporan Internal** (lengkap — untuk pemilik): modal/HPP, profit, margin, stok awal/sisa
- Laporan Penjualan: filter harian/bulanan/tahunan, export Excel & PDF
- Stok Harian: laporan stok semua produk per tanggal, export Excel
- Pengaturan: nama toko, alamat, telepon, logo, footer invoice, nama petugas

---

## Menu (Sidebar)
- Dashboard
- Data Barang (Products)
- Transaksi Baru (New Sale)
- Riwayat Penjualan (Sales History)
- Data Customer
- Hutang Customer (Debt Management)
- Laporan (Reports)
- Pengaturan (Settings)

---

## Tombol Aksi di Tabel Transaksi
Setiap baris di Riwayat Penjualan dan Laporan Penjualan punya **2 tombol**:
- **Invoice** (biru) → `sales.invoice-customer` — invoice ringkas untuk customer
- **Laporan** (abu) → `sales.invoice` — laporan internal detail untuk pemilik
- **Hapus** (merah) — hanya di Riwayat Penjualan

---

## Bug yang Sudah Diperbaiki
1. **Input angka leading zero** — semua field angka pakai `placeholder="0"` + select-on-focus
2. **Quantity input reset saat mengetik** — diubah ke `wire:model.blur`
3. **Date picker tidak terlihat di mobile** — semua `<input type="date/month">` pakai `style="color-scheme: light"` + class `text-gray-800`
4. **Dashboard error SQLite syntax** — `Dashboard.php` sudah pakai `MONTH()` dan `YEAR()` MySQL
5. **Label "Export CSV"** — semua sudah diubah ke "Export Excel"

---

## User Preferences
- UI bahasa Indonesia
- Database MySQL 8.0 (JANGAN ganti ke SQLite)
- Port 5000 untuk development server
- Workflow: "Start application" menjalankan `bash /home/runner/workspace/start.sh`
