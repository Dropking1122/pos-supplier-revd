# POS Supplier Laravel

A full-featured Point of Sale Supplier system built with Laravel 13, Livewire, and TailwindCSS.

## Tech Stack
- **Backend**: Laravel 13 (PHP 8.4)
- **Frontend**: Livewire 3, TailwindCSS 4, Alpine.js
- **Database**: SQLite
- **Auth**: Laravel Breeze (Livewire stack)
- **PDF**: DomPDF
- **Export**: CSV export via PHP streams

## Login Credentials (development seed)
- Email: `admin@pos.com`
- Password: `password`

## Features
- Dashboard with stats (total sales, profit, debt, low stock alerts)
- Product management with multi-price (grosir/ecer), stock monitoring
- Customer management
- Sales transactions (cash & tempo/credit)
- Customer debt tracking with installment payments
- Invoice generation (printable HTML)
- Sales reports (daily/monthly/yearly) with CSV and PDF export
- Company settings (name, address, phone, logo, invoice footer)

## Menu Structure
- Dashboard
- Data Barang (Products)
- Transaksi Baru (New Sale)
- Riwayat Penjualan (Sales History)
- Data Customer
- Hutang Customer (Debt Management)
- Laporan (Reports)
- Pengaturan (Settings)

## User Preferences
- Indonesian language UI
- SQLite database for simplicity
- Port 5000 for development server
