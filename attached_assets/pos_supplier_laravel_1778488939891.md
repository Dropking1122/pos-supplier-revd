# POS Supplier Laravel + MySQL + Livewire

## Deskripsi
Sistem POS Supplier berbasis:
- Laravel
- MySQL
- Livewire
- TailwindCSS

Dengan fitur:
- Multi harga
- Hutang customer
- Invoice otomatis
- Export Excel & PDF
- Responsive mobile
- Setting nama perusahaan/toko
- Monitoring stock

---

# Teknologi

| Kebutuhan | Teknologi |
|---|---|
| Backend | Laravel |
| Database | MySQL |
| Frontend | Livewire |
| UI | TailwindCSS |
| Authentication | Laravel Breeze |
| Export Excel | Laravel Excel |
| Export PDF | DomPDF |
| Alert | SweetAlert |
| Responsive Table | DataTables / Livewire Table |

---

# Responsive & Mobile Friendly

## Target Tampilan
- Mobile
- Tablet
- Desktop

## Responsive Design
Menggunakan:
- TailwindCSS
- Flex/Grid Layout
- Sidebar collapse mobile
- Mobile navigation
- Card layout responsive

---

# Livewire

## Keuntungan
- Tanpa banyak reload page
- Input realtime
- Search realtime
- Filter realtime
- Responsive lebih ringan

---

# Install Livewire

```bash
composer require livewire/livewire
```

---

# Struktur Layout

```text
resources/views/
│
├── layouts/
│   ├── app.blade.php
│   └── guest.blade.php
│
├── livewire/
│   ├── products/
│   ├── sales/
│   ├── debts/
│   ├── reports/
│   └── settings/
```

---

# Setting Nama Perusahaan / Toko

## Fitur
Admin dapat mengatur:
- Nama toko
- Logo toko
- Alamat
- Nomor telepon
- Footer invoice

---

# Struktur Database Settings

```sql
CREATE TABLE settings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(255),
    company_logo VARCHAR(255),
    company_address TEXT,
    company_phone VARCHAR(50),
    invoice_footer TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

# Flow Setting

```text
Menu Pengaturan
        ↓
Edit Data Perusahaan
        ↓
Simpan
        ↓
Digunakan otomatis di:
- Dashboard
- Invoice
- PDF
- Header aplikasi
```

---

# Contoh Penggunaan Setting

## Blade

```php
{{ $setting->company_name }}
```

---

# Invoice Otomatis

## Isi Invoice
- Logo perusahaan
- Nama toko
- Customer
- Barang
- Qty
- Harga
- Total
- Status pembayaran

---

# Flow Invoice

```text
Transaksi selesai
        ↓
Generate Invoice
        ↓
Download PDF
        ↓
Print Nota
```

---

# Multi Harga

## Jenis Harga
- Harga Grosir
- Harga Ecer

---

# Struktur Products

```sql
CREATE TABLE products (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kode_barang VARCHAR(100),
    nama_barang VARCHAR(255),
    jenis_barang VARCHAR(255),
    kuantitas INT,
    modal_awal DECIMAL(12,2),
    harga_grosir DECIMAL(12,2),
    harga_ecer DECIMAL(12,2),
    harga_satuan VARCHAR(50),
    stock_minimum INT DEFAULT 5,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

# Penjualan

## Flow Penjualan

```text
Pilih Customer
        ↓
Pilih Barang
        ↓
Pilih Harga
- Grosir
- Ecer
        ↓
Input Qty
        ↓
Hitung Total Realtime
        ↓
Simpan
        ↓
Stock Berkurang
```

---

# Hutang Customer

## Fitur
- Pembayaran tempo
- Cicilan toko
- Riwayat pembayaran
- Sisa hutang
- Status lunas/belum

---

# Database Hutang

## debts

```sql
CREATE TABLE debts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(255),
    sale_id BIGINT,
    total_hutang DECIMAL(12,2),
    total_bayar DECIMAL(12,2),
    sisa_hutang DECIMAL(12,2),
    jatuh_tempo DATE,
    status ENUM('belum_lunas','lunas'),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

# Stock Notification

## Kondisi

```php
if ($product->kuantitas <= $product->stock_minimum)
```

---

## Tampilan

```text
⚠ Stock barang hampir habis
```

---

# Dashboard

## Widget Dashboard
- Total penjualan
- Total barang
- Total profit
- Hutang customer
- Barang hampir habis
- Grafik penjualan

---

# Laporan

## Jenis Laporan
- Harian
- Bulanan
- Tahunan
- Profit
- Hutang customer

---

# Export Excel

## Install

```bash
composer require maatwebsite/excel
```

---

# Export PDF

## Install

```bash
composer require barryvdh/laravel-dompdf
```

---

# Struktur Menu

```text
Dashboard
│
├── Barang
│   ├── Data Barang
│   ├── Tambah Barang
│   ├── Stock Barang
│   └── Notifikasi Stock
│
├── Penjualan
│   ├── Transaksi
│   ├── Invoice
│   └── Riwayat Penjualan
│
├── Hutang Customer
│   ├── Data Hutang
│   ├── Cicilan
│   └── Riwayat Pembayaran
│
├── Laporan
│   ├── Penjualan
│   ├── Profit
│   ├── Export Excel
│   └── Export PDF
│
├── Pengaturan
│   ├── Nama Toko
│   ├── Logo
│   ├── Alamat
│   └── Footer Invoice
│
└── User Management
```

---

# Struktur Laravel

```text
app/
├── Livewire/
│   ├── Products/
│   ├── Sales/
│   ├── Debts/
│   ├── Reports/
│   └── Settings/
│
├── Models/
│   ├── Product.php
│   ├── Sale.php
│   ├── SaleDetail.php
│   ├── Debt.php
│   ├── DebtPayment.php
│   └── Setting.php
│
├── Http/Controllers/
│   ├── InvoiceController.php
│   └── ExportController.php
```

---

# Flow Lengkap Sistem

```text
Login
    ↓
Dashboard
    ↓
Input Barang
    ↓
Stock Gudang
    ↓
Penjualan
    ↓
Pilih Harga
- Grosir
- Ecer
    ↓
Pembayaran
- Cash
- Tempo
    ↓
Jika tempo:
Masuk Hutang Customer
    ↓
Generate Invoice
    ↓
Cetak Nota
    ↓
Laporan
    ↓
Export Excel/PDF
```

---

# Package Tambahan

## Laravel Breeze

```bash
composer require laravel/breeze --dev
```

---

## TailwindCSS

```bash
npm install tailwindcss
```

---

## SweetAlert

```bash
composer require realrashid/sweet-alert
```

---

# Kelebihan Sistem

✅ Mobile Friendly  
✅ Responsive  
✅ Realtime dengan Livewire  
✅ Multi Harga  
✅ Hutang Customer  
✅ Invoice Otomatis  
✅ Export Excel & PDF  
✅ Monitoring Stock  
✅ Setting Nama Toko  
✅ Modern UI  