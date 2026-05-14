# Catatan Lengkap Fitur — POS Supplier Laravel

> Dokumen ini mencatat semua fitur yang tersedia di sistem POS Supplier secara lengkap dan terperinci.

---

## 1. Autentikasi & Manajemen Akun

- **Login** dengan email dan password (Laravel Breeze)
- **Logout** dengan invalidasi session
- **Lupa password** — kirim link reset via email
- **Verifikasi email** setelah registrasi
- **Ingat saya** (remember me) di login
- **Dua level akses:**
  - **Admin** — akses penuh ke semua fitur
  - **Kasir** — hanya bisa buat transaksi & lihat riwayat transaksi milik sendiri

---

## 2. Dashboard

- **Total penjualan hari ini** — jumlah uang masuk
- **Total profit hari ini** — pendapatan dikurangi modal
- **Total hutang aktif** — akumulasi sisa hutang seluruh customer
- **Stok menipis** — jumlah produk yang stoknya ≤ stok minimum
- **Transaksi hari ini** — jumlah invoice yang dibuat hari ini
- **Grafik penjualan bulanan** — bar chart pendapatan per bulan (tahun berjalan)
- **Grafik profit bulanan** — bar chart profit per bulan (tahun berjalan)
- **Tabel produk terlaris** — top produk berdasarkan total qty & pendapatan
- **Daftar stok menipis** — tabel produk yang perlu restock segera

---

## 3. Data Barang (Produk)

### Manajemen Produk
- **Tambah produk** baru dengan form modal
- **Edit produk** — semua field bisa diubah
- **Hapus produk** dengan konfirmasi modal
- **Validasi harga:**
  - Harga grosir tidak boleh lebih rendah dari modal (akan rugi)
  - Harga ecer tidak boleh lebih rendah dari modal
  - Harga ecer tidak boleh lebih rendah dari harga grosir

### Field Produk
| Field | Keterangan |
|---|---|
| Kode Barang | Kode unik (auto-check duplikat) |
| Nama Barang | Nama produk |
| Jenis Barang | Kategori bebas |
| Kuantitas | Stok saat ini |
| Modal Awal (HPP) | Harga pokok pembelian |
| Harga Grosir | Harga jual grosir |
| Harga Ecer | Harga jual ecer |
| Satuan | mis: pcs, kg, lusin |
| Stok Minimum | Trigger alert stok menipis |

### Restock
- **Tambah stok** produk lewat modal restock
- Input jumlah penambahan stok
- Stok otomatis bertambah

### Import Produk (Excel/CSV)
- Upload file `.xlsx`, `.xls`, atau `.csv`
- Template import bisa didownload
- **Update or create** — jika kode barang sudah ada, data diupdate
- Laporan import: berhasil, gagal, alasan error per baris
- Validasi: `kode_barang` dan `nama_barang` wajib ada

### Export Produk
- Export seluruh data produk ke **Excel (.xlsx)**

### Filter & Tampilan
- **Pencarian real-time** berdasarkan nama atau kode barang
- **Filter stok menipis** — tampilkan hanya produk hampir habis
- **Sortir kolom** — nama, kode, kuantitas, modal, harga grosir, harga ecer, stok minimum, total terjual, total pendapatan
- Pagination 10 item per halaman
- Badge hitungan produk stok menipis

---

## 4. Transaksi Baru (Point of Sale)

### Pencarian & Pemilihan Produk
- Kolom pencarian produk (nama / kode barang)
- Dropdown hasil pencarian real-time (min. 2 karakter)
- Hanya produk dengan stok > 0 yang muncul
- Klik produk → otomatis masuk ke keranjang
- Jika produk sudah di keranjang, qty otomatis +1

### Keranjang Belanja
- **Multi-item** — tambahkan banyak produk sekaligus
- **Pilih tipe harga** per item: Grosir atau Ecer
- **Input qty** — ketik langsung di kolom, validasi tidak melebihi stok
- **Mode input sisa stok** — alternatif input: masukkan sisa yang diinginkan, qty otomatis dihitung
- Toggle antara mode "Qty" dan mode "Sisa Stok"
- **Subtotal per item** — auto-hitung saat qty atau harga berubah
- **Hapus item** dari keranjang
- **Warning rugi** — peringatan jika harga jual di bawah modal

### Pembayaran
- **Cash/Tunai** — langsung lunas
- **Tempo/Kredit** — bayar nanti
  - Wajib pilih customer
  - Wajib isi tanggal jatuh tempo
  - Jatuh tempo tidak boleh di masa lalu

### Validasi Sebelum Simpan
- Keranjang tidak boleh kosong
- Stok cukup untuk semua item (dicek ulang saat simpan)
- Qty setiap item minimal 1
- Customer dan jatuh tempo wajib untuk pembayaran tempo

### Setelah Simpan
- **Invoice number otomatis**: `INV-YYYYMMDD-XXXXX`
- Stok produk otomatis berkurang
- Snapshot `stock_before` dicatat untuk akurasi laporan
- Jika tempo: hutang otomatis dibuat di modul hutang
- Redirect ke halaman riwayat penjualan

---

## 5. Riwayat Penjualan

### Tampilan Tabel
- Kolom: Invoice, Customer, Kasir, Total, Status, Tipe Bayar, Tanggal
- **Sortir** semua kolom utama
- Pagination 15 item per halaman

### Filter
- Cari berdasarkan **nomor invoice** atau **nama customer**
- Filter **status**: Lunas / Sebagian / Belum Lunas
- Filter **tanggal** transaksi
- Filter **customer** (dropdown)
- Filter **kasir** (khusus admin — dropdown semua kasir)

### Aksi per Baris
- **Invoice** (biru) → buka invoice ringkas untuk customer (tanpa modal/profit)
- **Laporan** (abu) → buka laporan internal detail (ada modal, profit, stok)
- **Hapus** (merah) → hapus transaksi dengan konfirmasi
  - Stok produk otomatis dikembalikan
  - Hutang & cicilan terkait ikut dihapus
  - Kasir hanya bisa hapus transaksi miliknya sendiri

### Export
- Export data customer ke **Excel**
- Export data customer ke **PDF**

---

## 6. Invoice & Laporan Transaksi

### Invoice Customer (Ringkas)
- Judul: **INVOICE**
- Cocok untuk dicetak dan diberikan ke customer
- Berisi: nama toko, alamat, telp, nomor invoice, tanggal, customer, daftar barang (nama, qty, harga, subtotal), total
- **Tanpa** informasi modal, profit, atau stok (privasi pemilik)
- Format cetak A5

### Laporan Internal (Lengkap)
- Judul: **LAPORAN PENJUALAN**
- Hanya untuk pemilik/admin
- Berisi semua yang ada di invoice customer PLUS:
  - Harga beli (modal/HPP) per item
  - Keuntungan per item
  - Margin profit (%)
  - Stok awal (sebelum transaksi)
  - Sisa stok (sesudah transaksi)
  - Total modal transaksi
  - Total profit transaksi

### Export Excel per Transaksi
- Download Excel untuk satu transaksi tertentu
- Format lengkap dengan header berwarna (laporan internal)
- Include semua kolom: nama barang, kode, tipe harga, modal, harga jual, stok awal, sisa stok, total modal, total jual, keuntungan

---

## 7. Data Customer

- **Tambah customer** baru
- **Edit customer** — nama, telepon, alamat
- **Hapus customer** dengan konfirmasi
- **Pencarian real-time** — nama atau nomor telepon
- **Sortir kolom** — nama, telepon, alamat, tanggal daftar
- Pagination 10 item per halaman
- Melihat histori transaksi & hutang terkait

---

## 8. Hutang Customer

### Tampilan
- Tabel semua hutang aktif dan lunas
- Kolom: Customer, Invoice, Total Hutang, Total Bayar, Sisa, Jatuh Tempo, Status
- Badge status: **Lunas** (hijau) / **Belum Lunas** (merah)

### Filter & Sort
- Cari berdasarkan nama customer
- Filter status: Lunas / Belum Lunas
- Sortir: tanggal, total hutang, sisa hutang, jatuh tempo, status

### Cicilan Pembayaran
- Tombol **Bayar** pada setiap hutang yang belum lunas
- Input jumlah pembayaran (default: sisa hutang penuh)
- Input tanggal pembayaran
- Input catatan (opsional)
- Overpayment otomatis dikap ke sisa hutang
- Setelah bayar:
  - `total_bayar` bertambah
  - `sisa_hutang` berkurang
  - Status otomatis jadi **Lunas** jika sisa = 0
  - Status penjualan terkait juga diupdate (paid/partial)

---

## 9. Laporan Penjualan

### Filter Periode
- **Harian** — pilih tanggal
- **Bulanan** — pilih bulan & tahun
- **Tahunan** — pilih tahun

### Tabel Laporan Penjualan
- Daftar semua transaksi dalam periode yang dipilih
- Kolom: Invoice, Customer, Total, Profit, Status, Tanggal
- Total pendapatan periode
- Total profit periode

### Export Laporan Penjualan
- Export ke **Excel (.xlsx)** — semua detail dengan styling profesional
- Export ke **PDF** — layout cetak siap pakai

### Laporan Stok Harian
- Tab terpisah dalam halaman laporan
- Pilih tanggal untuk melihat kondisi stok
- Tabel semua produk dengan:
  - Stok awal (stok saat ini + yang terjual hari itu)
  - Total terjual hari itu
  - Sisa stok
  - Pendapatan dari produk tersebut
  - Keuntungan dari produk tersebut
- Summary: total produk, total terjual, tidak terjual, total pendapatan, total keuntungan, stok menipis
- **Pencarian** produk dalam tabel stok
- Export stok harian ke **Excel**

---

## 10. Manajemen User

> Hanya admin yang dapat mengakses

- **Tambah user** baru (nama, email, password, role)
- **Edit user** — nama, email, ubah password (opsional)
- **Hapus user** (tidak bisa hapus akun sendiri)
- **Assign role**: Admin atau Kasir (toggle)
- Admin tidak bisa mencabut status admin dari akun sendiri
- **Pencarian** berdasarkan nama atau email
- Tampil jumlah transaksi per user
- Password baru minimal 8 karakter + konfirmasi

---

## 11. Pengaturan Toko

> Hanya admin yang dapat mengakses

### Info Toko
- Nama toko / perusahaan
- Alamat toko
- Nomor telepon
- Footer teks invoice (tampil di bagian bawah setiap invoice)
- Nama petugas / kasir default

### Logo Toko
- Upload logo via drag-and-drop atau klik
- Preview logo sebelum simpan
- Validasi: hanya file gambar valid (JPEG, PNG, GIF, WebP)
- Validasi konten binary — mencegah upload file berbahaya
- Hapus logo yang sudah ada

### Reset Database
- Pilihan reset selektif per tabel:
  - Data Penjualan & Detail
  - Data Hutang & Cicilan
  - Data Customer
  - Data Produk
  - Data User (kecuali yang sedang login)
  - Pengaturan (reset ke default)
- Wajib ketik **RESET** untuk konfirmasi
- Minimal pilih 1 tabel sebelum bisa reset

---

## 12. Backup Database

> Hanya admin yang dapat mengakses

### Backup Manual
- Tombol **Backup Sekarang** — generate file SQL langsung
- File backup langsung bisa didownload
- Format nama file: `backup_YYYY-MM-DD_HH-ii-ss.sql`

### Backup Terjadwal (Otomatis)
- Aktifkan/nonaktifkan jadwal backup
- Pilih jam pelaksanaan (format HH:MM)
- Retensi otomatis: hapus backup lama (konfigurasi hari)
- Tombol **Jalankan Sekarang** — backup manual via scheduler
- Info terakhir backup: waktu & nama file

### Daftar Backup
- Tabel semua file backup yang tersimpan
- Info: nama file, ukuran, tanggal dibuat, tipe (manual/auto)
- **Download** file backup individual
- **Hapus** file backup individual

### Import / Restore
- Upload file SQL (`.sql`) untuk restore database
- Validasi ekstensi + validasi MIME type server-side
- Maksimal ukuran file: 100 MB
- Eksekusi SQL langsung ke database

---

## 13. Notifikasi Bell (Navbar)

- Bell icon di navbar dengan badge angka
- Badge = jumlah stok menipis + transaksi hari ini (max 99)
- Klik bell → dropdown dengan 2 tab:
  - **Stok Menipis** — daftar produk dengan stok ≤ minimum
  - **Transaksi Hari Ini** — 5 transaksi terbaru hari ini + total count
- Klik bell otomatis reset badge (tandai sudah dilihat)

---

## 14. Keamanan

- **CSRF protection** di semua form (Laravel default)
- **Route middleware** — `auth`, `verified`, `admin` per level
- **Admin check di Livewire** — setiap action sensitif ada `requireAdmin()` + `abort_unless()`
- **Sort field whitelist** — tidak bisa inject kolom database sembarangan
- **Validasi upload:**
  - Gambar: dicek binary dengan `getimagesizefromstring()`
  - SQL: dicek MIME type server-side dengan `finfo`
- **Backup download** — hanya file dengan format nama `backup_*.sql` yang bisa didownload (path traversal protection)
- **Password hashing** dengan `bcrypt` (12 rounds)
- **Session invalidasi** saat logout
- **Soft delete protection** — user tidak bisa hapus akun sendiri

---

## 15. UI/UX

- **Bahasa Indonesia** di seluruh interface
- **Responsive / Mobile-friendly** — bisa dipakai di HP, tablet, desktop
- **Sidebar navigasi** dengan collapse di mobile
- **Real-time** tanpa reload page (Livewire)
- **SweetAlert / Toast notifications** untuk semua feedback aksi
- **Modal konfirmasi** untuk semua aksi hapus/destruktif
- **Loading state** pada tombol simpan
- **Color-scheme fix** untuk date picker di mobile
- **Select-on-focus** di semua input angka
- **Placeholder "0"** di semua field numerik (mencegah leading zero)

---

## Ringkasan Fitur per Role

| Fitur | Kasir | Admin |
|---|:---:|:---:|
| Buat transaksi baru | ✅ | ✅ |
| Lihat riwayat transaksi milik sendiri | ✅ | ✅ |
| Hapus transaksi milik sendiri | ✅ | ✅ |
| Invoice customer | ✅ | ✅ |
| Lihat semua transaksi kasir lain | ❌ | ✅ |
| Hapus transaksi kasir lain | ❌ | ✅ |
| Laporan internal (modal/profit) | ❌ | ✅ |
| Data Barang (CRUD, import, export) | ❌ | ✅ |
| Data Customer | ❌ | ✅ |
| Hutang Customer | ❌ | ✅ |
| Laporan Penjualan & Stok | ❌ | ✅ |
| Manajemen User | ❌ | ✅ |
| Pengaturan Toko | ❌ | ✅ |
| Backup Database | ❌ | ✅ |
| Dashboard & Notifikasi | ✅ | ✅ |
