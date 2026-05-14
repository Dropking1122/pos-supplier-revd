# AGENT_CONTEXT — POS Supplier Laravel

> Baca file ini sebelum mulai bekerja. Berisi semua yang perlu diketahui tanpa harus baca seluruh kodebase.

---

## Gambaran Aplikasi

**POS Supplier** adalah sistem Point of Sale berbasis web untuk manajemen penjualan, stok, customer, dan hutang. Dibangun dengan Laravel 13 + Livewire 3 + TailwindCSS 4.

**Login dev:** `admin@pos.com` / `password`

---

## Stack Teknologi

| Layer | Teknologi |
|---|---|
| Backend | PHP 8.4, Laravel 13 |
| Frontend | Livewire 3, Blade, Alpine.js, TailwindCSS 4 |
| Database | MySQL 8.0 (WAJIB — jangan ganti ke SQLite) |
| Auth | Laravel Breeze (Livewire/Volt) — built-in, tidak pakai layanan eksternal |
| PDF | barryvdh/laravel-dompdf |
| Excel | phpoffice/phpspreadsheet + maatwebsite/excel |
| Notif | realrashid/sweet-alert |

---

## Cara Menjalankan

Workflow "Start application" menjalankan `bash start.sh` yang:
1. Install PHP/Node deps jika belum ada
2. Start MySQL (socket: `/home/runner/mysql-run/mysql.sock`, password: `pos_password`)
3. Setup/patch `.env`
4. `php artisan migrate --force && db:seed --force`
5. `php artisan serve --host=0.0.0.0 --port=5000`

**Port:** 5000 (dev), 80 (external proxy)

---

## Struktur Database

```
users            — id, name, email, password, is_admin (boolean)
customers        — id, name, phone, address
products         — id, kode_barang, nama_barang, jenis_barang, kuantitas,
                   modal_awal, harga_grosir, harga_ecer, harga_satuan, stock_minimum
sales            — id, invoice_number, customer_id, user_id, total_amount,
                   amount_paid, payment_type (cash/tempo), status (paid/partial/unpaid),
                   due_date, notes
sale_details     — id, sale_id, product_id, price_type (grosir/ecer), unit_price,
                   quantity, stock_before, subtotal
debts            — id, customer_id, sale_id, total_hutang, total_bayar,
                   sisa_hutang, jatuh_tempo, status (lunas/belum_lunas)
debt_payments    — id, debt_id, amount, payment_date, notes
settings         — id, company_name, company_logo, company_address,
                   company_phone, invoice_footer, petugas
```

**Query MySQL** — selalu pakai `MONTH()`, `YEAR()`, `DATE()`, bukan `strftime()`.

---

## Routes & Controller Map

### Semua user terautentikasi (`auth`, `verified`)
| URL | Handler |
|---|---|
| `GET /` | redirect → dashboard |
| `GET /dashboard` | view `dashboard` → `livewire:dashboard` |
| `GET /profile` | view `profile` |
| `GET /sales` | view → `livewire:sales.sale-list` |
| `GET /sales/create` | view → `livewire:sales.sale-create` |
| `GET /sales/{id}/invoice` | `InvoiceController@show` (laporan internal) |
| `GET /sales/{id}/invoice-customer` | `InvoiceController@showCustomer` (invoice ringkas) |
| `GET /sales/{id}/invoice-excel` | `ExportController@invoiceExcel` |
| `GET /sales/export-customer` | `ExportController@customerExcel` |
| `GET /sales/export-customer-pdf` | `ExportController@customerPdf` |
| `POST /logout` | inline closure |

### Admin only (`auth`, `verified`, `admin` middleware)
| URL | Handler |
|---|---|
| `GET /products` | `livewire:products.product-list` |
| `GET /customers` | `livewire:customers.customer-list` |
| `GET /debts` | `livewire:debts.debt-list` |
| `GET /reports` | `livewire:reports.report-index` |
| `GET /users` | `livewire:users.user-index` |
| `GET /settings` | `livewire:settings.setting-index` |
| `GET /backup` | `livewire:backup.backup-index` |
| `GET /backup/create` | `BackupController@create` |
| `GET /backup/download/{filename}` | `BackupController@download` |
| `POST /backup/import` | `BackupController@import` |
| `DELETE /backup/delete/{filename}` | `BackupController@delete` |
| `GET /reports/export` | `ExportController@excel` |
| `GET /reports/pdf` | `ExportController@pdf` |
| `GET /reports/stock-export` | `ExportController@stockExcel` |
| `GET /products/export` | `ExportController@productExport` |

---

## Livewire Components

| Component | File | Fungsi |
|---|---|---|
| `dashboard` | `app/Livewire/Dashboard.php` | Stats, grafik, stok menipis |
| `sales.sale-create` | `app/Livewire/Sales/SaleCreate.php` | Interface POS, buat transaksi |
| `sales.sale-list` | `app/Livewire/Sales/SaleList.php` | Riwayat & hapus transaksi |
| `products.product-list` | `app/Livewire/Products/ProductList.php` | CRUD produk, import Excel, restock |
| `customers.customer-list` | `app/Livewire/Customers/CustomerList.php` | CRUD customer |
| `debts.debt-list` | `app/Livewire/Debts/DebtList.php` | Hutang & cicilan pembayaran |
| `reports.report-index` | `app/Livewire/Reports/ReportIndex.php` | Laporan harian/bulanan/tahunan + stok |
| `users.user-index` | `app/Livewire/Users/UserIndex.php` | Manajemen user & role |
| `settings.setting-index` | `app/Livewire/Settings/SettingIndex.php` | Pengaturan toko, logo, reset DB |
| `backup.backup-index` | `app/Livewire/Backup/BackupIndex.php` | Backup jadwal otomatis |
| `notification-bell` | `app/Livewire/NotificationBell.php` | Alert stok menipis di navbar |

---

## Models & Relasi

```
User         hasMany Sales
Customer     hasMany Sales, hasMany Debts
Product      hasMany SaleDetails (via 'saleDetails')
Sale         belongsTo User, belongsTo Customer, hasMany SaleDetails, hasOne Debt
SaleDetail   belongsTo Sale, belongsTo Product
Debt         belongsTo Customer, belongsTo Sale, hasMany DebtPayments
DebtPayment  belongsTo Debt
Setting      — singleton, akses via Setting::getSettings()
```

---

## Middleware

| Alias | Class | Fungsi |
|---|---|---|
| `auth` | Laravel built-in | Wajib login |
| `verified` | Laravel built-in | Email terverifikasi |
| `admin` | `App\Http\Middleware\IsAdmin` | Cek `user->is_admin === true`, abort 403 jika bukan |

> `admin` middleware didaftarkan di `bootstrap/app.php`.

---

## Controllers

| File | Fungsi |
|---|---|
| `app/Http/Controllers/InvoiceController.php` | `show()` laporan internal (modal, profit), `showCustomer()` invoice ringkas |
| `app/Http/Controllers/ExportController.php` | Semua export Excel & PDF (invoice, laporan, stok, produk) |
| `app/Http/Controllers/BackupController.php` | Backup/download/import/delete file SQL — admin only via `guardAdmin()` |

---

## Pola Keamanan yang Diterapkan

1. **sortField whitelist** — Semua komponen dengan sort (`ProductList`, `SaleList`, `CustomerList`, `DebtList`) punya `protected array $allowedSortFields` yang divalidasi sebelum `orderBy()`. Jangan tambah sort tanpa whitelist.

2. **Admin check di Livewire** — Komponen sensitif (`SettingIndex`, `UserIndex`) punya method `requireAdmin()` yang memanggil `abort_unless()`. Setiap action destruktif harus panggil `$this->requireAdmin()` di awal.

3. **Validasi upload file** — Logo divalidasi dengan `getimagesizefromstring()` (cek binary, bukan hanya MIME dari client). Import SQL divalidasi MIME via `finfo`.

4. **Password hashing** — Selalu `Hash::make()`, tidak pernah plain text.

5. **CSRF** — Laravel default aktif di semua form.

---

## Konvensi Penting

- **Bahasa UI:** Indonesia
- **Database:** MySQL 8.0 — jangan pernah ganti ke SQLite
- **Query date:** pakai `MONTH()`, `YEAR()`, `DATE()` — BUKAN `strftime()`
- **Wire model quantity:** pakai `wire:model.blur` bukan `.live` (mencegah reset saat mengetik)
- **Input angka:** `placeholder="0"` + select-on-focus
- **Date picker mobile:** `style="color-scheme: light"` + class `text-gray-800`
- **Invoice customer** (`sales.invoice-customer`): ringkas untuk customer — tanpa modal/profit
- **Laporan internal** (`sales.invoice`): lengkap untuk pemilik — ada modal, profit, margin, stok

---

## File Penting yang Sering Diedit

```
app/Livewire/Sales/SaleCreate.php         — logika transaksi baru
app/Livewire/Dashboard.php                — stats dashboard (ingat pakai MONTH/YEAR)
app/Http/Controllers/ExportController.php — semua export Excel/PDF (~1000 baris)
resources/views/sales/invoice.blade.php   — tampilan laporan internal
resources/views/sales/invoice-customer.blade.php — tampilan invoice customer
resources/views/livewire/                 — semua view Livewire
routes/web.php                            — semua route (pendek, ~50 baris)
start.sh                                  — startup script (MySQL + Laravel)
```

---

## Struktur Folder

```
app/
  Console/Commands/BackupDatabase.php   — backup otomatis terjadwal
  Http/
    Controllers/                        — InvoiceController, ExportController, BackupController
    Middleware/IsAdmin.php              — cek is_admin
    Requests/Auth/                      — form request untuk auth
  Livewire/                             — semua komponen Livewire
  Models/                               — Customer, Debt, DebtPayment, Product, Sale, SaleDetail, Setting, User
config/
  database.php                          — default 'mysql' via env
database/
  migrations/                           — 14 migration file
  seeders/                              — DatabaseSeeder (admin user + setting awal)
resources/
  views/
    livewire/                           — semua view komponen Livewire
    sales/invoice.blade.php             — laporan internal
    sales/invoice-customer.blade.php    — invoice customer
    layouts/app.blade.php               — layout utama dengan sidebar
routes/
  web.php                               — semua route web
  auth.php                              — route auth (Breeze/Volt)
public/
  build/                                — asset yang sudah di-build (Vite)
  logos/                                — logo toko yang diupload
  storage/                              — symlink ke storage/app/public
start.sh                                — startup script
```

---

## Hal yang DILARANG

- ❌ Mengubah `DB_CONNECTION` ke `sqlite`
- ❌ Menggunakan `strftime()` dalam query (SQLite syntax)
- ❌ Menambah sort column tanpa whitelist di `$allowedSortFields`
- ❌ Menghapus `requireAdmin()` dari action sensitif di Livewire
- ❌ Menyimpan password tanpa `Hash::make()`
- ❌ Mengekspos `APP_KEY` atau credential database ke luar
