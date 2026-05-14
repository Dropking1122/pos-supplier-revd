<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/Livewire-3-FB70A9?style=for-the-badge&logo=livewire&logoColor=white"/>
  <img src="https://img.shields.io/badge/TailwindCSS-4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white"/>
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
</p>

<h1 align="center">🏪 POS Supplier</h1>

<p align="center">
  Sistem Point of Sale berbasis web untuk supplier & toko.<br/>
  Dibangun dengan <strong>Laravel 13</strong>, <strong>Livewire 3</strong>, dan <strong>TailwindCSS 4</strong>.<br/><br/>
  Multi-harga grosir/ecer · Hutang customer · Invoice otomatis · Laporan Excel & PDF · Backup database
</p>

---

## ✨ Fitur Utama

| Modul | Fitur |
|---|---|
| 🛒 **Transaksi POS** | Multi-item, pilih harga grosir/ecer per produk, pembayaran cash & tempo |
| 📦 **Data Barang** | CRUD produk, import Excel/CSV, restock, alert stok menipis |
| 👥 **Customer** | Data customer, histori transaksi |
| 💳 **Hutang** | Tracking hutang, cicilan bertahap, status otomatis lunas/belum |
| 📊 **Laporan** | Filter harian/bulanan/tahunan, profit, stok harian |
| 🧾 **Invoice** | Invoice ringkas untuk customer + Laporan internal (modal & profit) |
| 📤 **Export** | Excel & PDF untuk laporan, invoice, stok, dan data produk |
| 👤 **Manajemen User** | Multi-user dengan dua role: Admin & Kasir |
| ⚙️ **Pengaturan** | Nama toko, logo, alamat, telepon, footer invoice |
| 💾 **Backup** | Backup manual & terjadwal otomatis, restore via upload SQL |
| 🔔 **Notifikasi** | Bell alert stok menipis & transaksi hari ini di navbar |

---

## 🔐 Role & Hak Akses

| Fitur | Kasir | Admin |
|---|:---:|:---:|
| Buat transaksi baru | ✅ | ✅ |
| Invoice customer (ringkas) | ✅ | ✅ |
| Lihat & hapus transaksi sendiri | ✅ | ✅ |
| Kelola semua transaksi kasir lain | ❌ | ✅ |
| Laporan internal (modal & profit) | ❌ | ✅ |
| Data Barang (CRUD, import, export) | ❌ | ✅ |
| Data Customer & Hutang | ❌ | ✅ |
| Laporan Penjualan & Stok | ❌ | ✅ |
| Manajemen User | ❌ | ✅ |
| Pengaturan Toko & Backup | ❌ | ✅ |

**Akun default (development):**
```
Email    : admin@pos.com
Password : password
```

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | PHP 8.4, Laravel 13 |
| Frontend | Livewire 3, Alpine.js, TailwindCSS 4 |
| Database | MySQL 8.0 |
| Auth | Laravel Breeze (Livewire/Volt stack) |
| PDF | barryvdh/laravel-dompdf |
| Excel | phpoffice/phpspreadsheet + maatwebsite/excel |
| Notifikasi UI | realrashid/sweet-alert |

---

## 🚀 Instalasi

### Prasyarat

- PHP >= 8.3
- MySQL 8.0
- Composer
- Node.js & NPM

### Langkah

```bash
# 1. Clone repositori
git clone https://github.com/username/pos-supplier.git
cd pos-supplier

# 2. Install dependensi
composer install
npm install && npm run build

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Buat database
mysql -u root -p -e "CREATE DATABASE pos_supplier CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Isi konfigurasi database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_supplier
DB_USERNAME=root
DB_PASSWORD=your_password
```

```bash
# 5. Migrasi & seeder
php artisan migrate --seed
php artisan storage:link

# 6. Jalankan server
php artisan serve
```

Buka **http://localhost:8000** dan login dengan `admin@pos.com` / `password`.

---

## 📁 Struktur Direktori Penting

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── InvoiceController.php     # Invoice customer & laporan internal
│   │   ├── ExportController.php      # Semua export Excel & PDF
│   │   └── BackupController.php      # Backup & restore database
│   └── Middleware/
│       └── IsAdmin.php               # Guard role admin
├── Livewire/
│   ├── Dashboard.php                 # Stats, grafik, produk terlaris
│   ├── Sales/
│   │   ├── SaleCreate.php            # Interface POS
│   │   └── SaleList.php              # Riwayat penjualan
│   ├── Products/ProductList.php      # CRUD, import, restock
│   ├── Customers/CustomerList.php    # Data customer
│   ├── Debts/DebtList.php            # Hutang & cicilan
│   ├── Reports/ReportIndex.php       # Laporan & stok harian
│   ├── Users/UserIndex.php           # Manajemen user
│   ├── Settings/SettingIndex.php     # Pengaturan toko
│   ├── Backup/BackupIndex.php        # Backup terjadwal
│   └── NotificationBell.php          # Alert notifikasi navbar
└── Models/
    ├── User, Customer, Product
    ├── Sale, SaleDetail
    ├── Debt, DebtPayment
    └── Setting

resources/views/
├── layouts/app.blade.php             # Layout utama + sidebar
├── livewire/                         # Semua view komponen Livewire
└── sales/
    ├── invoice.blade.php             # Laporan internal (modal + profit)
    └── invoice-customer.blade.php    # Invoice ringkas untuk customer
```

---

## 🗄️ Skema Database

```
users          — id, name, email, password, is_admin
customers      — id, name, phone, address
products       — id, kode_barang, nama_barang, jenis_barang, kuantitas,
                 modal_awal, harga_grosir, harga_ecer, harga_satuan, stock_minimum
sales          — id, invoice_number, customer_id, user_id, total_amount,
                 amount_paid, payment_type (cash/tempo), status (paid/partial/unpaid),
                 due_date, notes
sale_details   — id, sale_id, product_id, price_type (grosir/ecer), unit_price,
                 quantity, stock_before, subtotal
debts          — id, customer_id, sale_id, total_hutang, total_bayar,
                 sisa_hutang, jatuh_tempo, status (lunas/belum_lunas)
debt_payments  — id, debt_id, amount, payment_date, notes
settings       — id, company_name, company_logo, company_address,
                 company_phone, invoice_footer, petugas
```

---

## 📑 Route Penting

### Semua user terautentikasi
| Method | URL | Fungsi |
|---|---|---|
| GET | `/dashboard` | Dashboard + statistik |
| GET | `/sales` | Riwayat penjualan |
| GET | `/sales/create` | Buat transaksi baru |
| GET | `/sales/{id}/invoice` | Laporan internal (modal & profit) |
| GET | `/sales/{id}/invoice-customer` | Invoice ringkas untuk customer |
| GET | `/sales/{id}/invoice-excel` | Download Excel per transaksi |

### Admin only
| Method | URL | Fungsi |
|---|---|---|
| GET | `/products` | Data barang |
| GET | `/customers` | Data customer |
| GET | `/debts` | Hutang customer |
| GET | `/reports` | Laporan penjualan & stok |
| GET | `/reports/export` | Export Excel laporan |
| GET | `/reports/pdf` | Export PDF laporan |
| GET | `/reports/stock-export` | Export Excel stok harian |
| GET | `/users` | Manajemen user |
| GET | `/settings` | Pengaturan toko |
| GET | `/backup` | Manajemen backup |
| POST | `/backup/import` | Restore database dari file SQL |

---

## 📤 Fitur Export

| Data | Format | Keterangan |
|---|---|---|
| Invoice per transaksi | Excel | Detail produk + modal + profit |
| Invoice customer | Cetak/PDF | Ringkas tanpa modal/profit |
| Laporan penjualan | Excel + PDF | Filter harian/bulanan/tahunan |
| Stok harian | Excel | Semua produk per tanggal |
| Katalog produk | Excel | Seluruh data produk |
| Backup database | SQL | Seluruh isi database |

---

## 💾 Backup & Restore

```bash
# Backup manual via Artisan
php artisan app:backup-database
```

Atau lewat UI: **Admin → Backup → Backup Sekarang**

- File tersimpan di `storage/app/backups/`
- Format nama: `backup_YYYY-MM-DD_HH-ii-ss.sql`
- Backup otomatis: atur waktu & retensi (hari) lewat halaman Backup
- Restore: upload file SQL di halaman Backup → Import

---

## 🔒 Keamanan

- **CSRF Protection** — aktif di semua form
- **Route Middleware** — `auth`, `verified`, `admin` per level
- **Livewire Action Guard** — setiap action sensitif pakai `abort_unless()` server-side
- **Sort Field Whitelist** — kolom sort divalidasi, mencegah column injection
- **Upload Validation** — gambar dicek binary (`getimagesizefromstring`), SQL dicek MIME via `finfo`
- **Path Traversal Protection** — nama file backup divalidasi dengan regex
- **Password Hashing** — bcrypt 12 rounds
- **Session Invalidation** — saat logout session di-invalidate sepenuhnya

---

## 🤝 Kontribusi

1. Fork repositori ini
2. Buat branch: `git checkout -b fitur/nama-fitur`
3. Commit: `git commit -m 'Tambah fitur X'`
4. Push: `git push origin fitur/nama-fitur`
5. Buat Pull Request

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

<p align="center">
  Dibuat dengan ❤️ menggunakan Laravel + Livewire
</p>
