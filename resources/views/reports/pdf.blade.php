<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
h1 { color: #4f46e5; font-size: 18px; }
.header { border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th { background: #4f46e5; color: white; padding: 8px; text-align: left; font-size: 11px; }
td { padding: 7px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
.total { font-weight: bold; background: #f3f4f6; }
.text-right { text-align: right; }
.summary { display: flex; gap: 20px; margin-bottom: 20px; }
.card { background: #f0f0ff; border: 1px solid #c7d2fe; border-radius: 6px; padding: 12px; flex: 1; }
.card h4 { font-size: 10px; color: #6366f1; text-transform: uppercase; margin-bottom: 4px; }
.card p { font-size: 16px; font-weight: bold; color: #333; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ $setting->company_name }}</h1>
    <p>{{ $setting->company_address }} | {{ $setting->company_phone }}</p>
    <p><strong>Laporan Penjualan</strong> &bull; Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
</div>

<div class="summary">
    <div class="card">
        <h4>Total Penjualan</h4>
        <p>Rp {{ number_format($totalRevenue,0,',','.') }}</p>
    </div>
    <div class="card">
        <h4>Total Profit</h4>
        <p>Rp {{ number_format($totalProfit,0,',','.') }}</p>
    </div>
    <div class="card">
        <h4>Jumlah Transaksi</h4>
        <p>{{ $sales->count() }}</p>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Invoice</th>
            <th>Tanggal</th>
            <th>Customer</th>
            <th class="text-right">Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $sale)
        <tr>
            <td>{{ $sale->invoice_number }}</td>
            <td>{{ $sale->created_at->format('d/m/Y') }}</td>
            <td>{{ $sale->customer?->name ?? 'Umum' }}</td>
            <td class="text-right">Rp {{ number_format($sale->total_amount,0,',','.') }}</td>
            <td>{{ $sale->status==='paid'?'Lunas':($sale->status==='partial'?'Sebagian':'Belum Bayar') }}</td>
        </tr>
        @endforeach
        <tr class="total">
            <td colspan="3" class="text-right"><strong>TOTAL</strong></td>
            <td class="text-right"><strong>Rp {{ number_format($totalRevenue,0,',','.') }}</strong></td>
            <td></td>
        </tr>
    </tbody>
</table>
@if($setting->invoice_footer)
<p style="margin-top:30px;font-size:11px;color:#999;font-style:italic;text-align:center;">{{ $setting->invoice_footer }}</p>
@endif
</body>
</html>
