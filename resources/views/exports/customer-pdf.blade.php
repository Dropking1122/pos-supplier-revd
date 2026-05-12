<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #1e293b; background: #fff; }

.header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 12px; margin-bottom: 14px; border-bottom: 3px solid #4f46e5; }
.company-name { font-size: 15px; font-weight: bold; color: #4f46e5; }
.company-sub { font-size: 9px; color: #64748b; margin-top: 3px; line-height: 1.6; }
.badge { background: #4f46e5; color: #fff; font-size: 11px; font-weight: bold; padding: 4px 12px; border-radius: 16px; display: inline-block; }
.meta-right { text-align: right; font-size: 9px; color: #64748b; margin-top: 6px; line-height: 1.6; }

.customer-box { background: #f1f5f9; border-radius: 8px; padding: 10px 14px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: flex-start; }
.customer-name { font-size: 13px; font-weight: bold; color: #1e293b; }
.customer-sub { font-size: 9px; color: #64748b; margin-top: 2px; }

.summary { display: flex; gap: 10px; margin-bottom: 14px; }
.card { flex: 1; border-radius: 6px; padding: 8px 10px; border: 1px solid #e2e8f0; }
.card-label { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 3px; }
.card-value { font-size: 13px; font-weight: bold; color: #1e293b; }
.card-blue .card-value { color: #1d4ed8; }
.card-green .card-value { color: #15803d; }
.card-amber .card-value { color: #b45309; }

.section-title { font-size: 10px; font-weight: bold; color: #374151; margin-bottom: 6px; padding-bottom: 3px; border-bottom: 1px solid #e5e7eb; }

table { width: 100%; border-collapse: collapse; margin-bottom: 14px; border: 1px solid #cbd5e1; }
.sale-header td { background: #4338ca; color: #fff; font-size: 9px; font-weight: bold; padding: 6px 8px; border: 1px solid #3730a3; }
.col-header th { background: #e36c09; color: #fff; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; padding: 5px 7px; text-align: left; border: 1px solid #c2560a; }
.col-header th.r { text-align: right; }
.col-header th.c { text-align: center; }
td { padding: 5px 7px; font-size: 9px; border: 1px solid #e2e8f0; color: #334155; }
td.r { text-align: right; }
td.c { text-align: center; }
.odd { background: #ffffff; }
.even { background: #f8fafc; }
.subtotal-row td { background: #eef2ff; font-weight: bold; border: 1px solid #c7d2fe; font-size: 9px; }
.grand-row td { background: #4f46e5; color: #fff; font-weight: bold; font-size: 10px; border: 1px solid #4338ca; }

.footer { margin-top: 16px; padding-top: 10px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
.footer-note { font-size: 8.5px; color: #94a3b8; font-style: italic; }
.signature { text-align: center; font-size: 9px; color: #64748b; }
.signature-line { border-bottom: 1px solid #334155; width: 100px; margin: 34px auto 4px; }
</style>
</head>
<body>

<div class="header">
    <div>
        <div class="company-name">{{ $setting->company_name }}</div>
        <div class="company-sub">{{ $setting->company_address }}</div>
        <div class="company-sub">{{ $setting->company_phone }}</div>
    </div>
    <div style="text-align:right;">
        <div class="badge">REKAP CUSTOMER</div>
        <div class="meta-right">Dicetak: {{ now()->isoFormat('D MMMM Y, HH:mm') }}</div>
    </div>
</div>

<div class="customer-box">
    <div>
        <div class="customer-name">{{ $customer->name }}</div>
        @if($customer->phone)<div class="customer-sub">Telepon: {{ $customer->phone }}</div>@endif
        @if($customer->address)<div class="customer-sub">Alamat: {{ $customer->address }}</div>@endif
    </div>
    <div style="text-align:right;">
        <div class="customer-sub">Total transaksi: <strong>{{ $sales->count() }}</strong></div>
    </div>
</div>

<div class="summary">
    <div class="card card-blue">
        <div class="card-label">Total Penjualan</div>
        <div class="card-value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
    </div>
    <div class="card card-amber">
        <div class="card-label">Total Modal (HPP)</div>
        <div class="card-value">Rp {{ number_format($grandModal, 0, ',', '.') }}</div>
    </div>
    <div class="card card-green">
        <div class="card-label">Keuntungan Bersih</div>
        <div class="card-value">Rp {{ number_format($grandProfit, 0, ',', '.') }}</div>
    </div>
</div>

<div class="section-title">Detail Transaksi</div>

@forelse($sales as $saleIdx => $sale)
@php
    $saleModal = 0;
    $saleJual  = 0;
@endphp
<table>
    <tr class="sale-header">
        <td colspan="4">#{{ $saleIdx + 1 }} &nbsp; {{ $sale->invoice_number }} &nbsp;&nbsp; {{ $sale->created_at->format('d/m/Y H:i') }}</td>
        <td colspan="3" style="text-align:right;">
            {{ $sale->payment_type === 'cash' ? 'Cash' : 'Tempo' }} &nbsp;|&nbsp;
            {{ $sale->status === 'paid' ? 'LUNAS' : ($sale->status === 'partial' ? 'SEBAGIAN' : 'BELUM BAYAR') }}
        </td>
    </tr>
    <tr class="col-header">
        <th style="width:30%;">Nama Barang</th>
        <th class="c" style="width:8%;">Tipe</th>
        <th class="r" style="width:13%;">Harga Beli</th>
        <th class="r" style="width:13%;">Harga Jual</th>
        <th class="c" style="width:7%;">Qty</th>
        <th class="r" style="width:14%;">Total Modal</th>
        <th class="r" style="width:15%;">Total Jual</th>
    </tr>
    @foreach($sale->details as $i => $detail)
    @php
        $product   = $detail->product;
        $qty       = (int) $detail->quantity;
        $hargaBeli = (float) ($product->modal_awal ?? 0);
        $hargaJual = (float) $detail->unit_price;
        $tModal    = $hargaBeli * $qty;
        $tJual     = (float) $detail->subtotal;
        $saleModal += $tModal;
        $saleJual  += $tJual;
        $rowClass = $i % 2 === 0 ? 'odd' : 'even';
    @endphp
    <tr class="{{ $rowClass }}">
        <td>
            {{ $product->nama_barang }}
            <span style="color:#94a3b8;"> — {{ $product->kode_barang }}</span>
        </td>
        <td class="c">{{ ucfirst($detail->price_type) }}</td>
        <td class="r" style="color:#64748b;">Rp {{ number_format($hargaBeli, 0, ',', '.') }}</td>
        <td class="r" style="font-weight:600;">Rp {{ number_format($hargaJual, 0, ',', '.') }}</td>
        <td class="c" style="font-weight:700;color:#4f46e5;">{{ $qty }}</td>
        <td class="r" style="color:#64748b;">Rp {{ number_format($tModal, 0, ',', '.') }}</td>
        <td class="r" style="font-weight:700;">Rp {{ number_format($tJual, 0, ',', '.') }}</td>
    </tr>
    @endforeach
    @php $saleProfit = $saleJual - $saleModal; @endphp
    <tr class="subtotal-row">
        <td colspan="5" style="text-align:right;">Subtotal ({{ $sale->details->count() }} item):</td>
        <td class="r">Rp {{ number_format($saleModal, 0, ',', '.') }}</td>
        <td class="r">Rp {{ number_format($saleJual, 0, ',', '.') }}</td>
    </tr>
</table>
@empty
<p style="text-align:center;color:#94a3b8;padding:20px 0;">Belum ada transaksi untuk customer ini.</p>
@endforelse

<table>
    <tr class="grand-row">
        <td colspan="5" style="text-align:right;">GRAND TOTAL ({{ $sales->count() }} transaksi):</td>
        <td class="r">Rp {{ number_format($grandModal, 0, ',', '.') }}</td>
        <td class="r">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
    </tr>
</table>

<div class="footer">
    <div class="footer-note">{{ $setting->invoice_footer }}</div>
    <div class="signature">
        <div class="signature-line"></div>
        <div style="font-weight:600;">{{ $setting->petugas ?? 'Petugas' }}</div>
        <div style="font-size:8px;color:#94a3b8;margin-top:2px;">Tanda Tangan</div>
    </div>
</div>

</body>
</html>
