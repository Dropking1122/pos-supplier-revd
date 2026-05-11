<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice {{ $sale->invoice_number }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 13px; color: #333; }
.container { max-width: 750px; margin: 0 auto; padding: 30px; }
.header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; }
.company-name { font-size: 22px; font-weight: bold; color: #4f46e5; }
.company-info { font-size: 12px; color: #666; margin-top: 5px; }
.invoice-label { font-size: 28px; font-weight: bold; color: #4f46e5; text-align: right; }
.invoice-meta { font-size: 12px; color: #666; text-align: right; }
.bill-to { margin-bottom: 25px; }
.bill-to h4 { font-size: 11px; text-transform: uppercase; color: #999; margin-bottom: 5px; }
.bill-to p { font-size: 14px; font-weight: 600; }
table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
th { background: #4f46e5; color: white; padding: 10px 12px; text-align: left; font-size: 12px; }
td { padding: 9px 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
tr:hover td { background: #f9fafb; }
.text-right { text-align: right; }
.total-row td { font-weight: bold; font-size: 15px; background: #f3f4f6; border-top: 2px solid #4f46e5; }
.status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
.status-paid { background: #d1fae5; color: #065f46; }
.status-unpaid { background: #fee2e2; color: #991b1b; }
.status-partial { background: #fef3c7; color: #92400e; }
.footer-section { margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 20px; display: flex; justify-content: space-between; align-items: flex-end; }
.footer-note { font-size: 12px; color: #999; font-style: italic; }
.payment-info { background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; }
@media print { .no-print { display: none; } body { print-color-adjust: exact; } }
</style>
</head>
<body>
<div class="container">
    <!-- Print button -->
    <div class="no-print" style="margin-bottom:20px;">
        <button onclick="window.print()" style="background:#4f46e5;color:white;padding:8px 20px;border:none;border-radius:6px;cursor:pointer;font-size:14px;">🖨 Cetak Invoice</button>
        <button onclick="window.close()" style="background:#e5e7eb;color:#333;padding:8px 20px;border:none;border-radius:6px;cursor:pointer;font-size:14px;margin-left:8px;">✕ Tutup</button>
    </div>

    <!-- Header -->
    <div class="header">
        <div>
            @if($setting->company_logo)
            <img src="{{ asset($setting->company_logo) }}" alt="Logo" style="height:50px;margin-bottom:8px;">
            @endif
            <div class="company-name">{{ $setting->company_name }}</div>
            <div class="company-info">{{ $setting->company_address }}</div>
            <div class="company-info">{{ $setting->company_phone }}</div>
        </div>
        <div>
            <div class="invoice-label">INVOICE</div>
            <div class="invoice-meta"># {{ $sale->invoice_number }}</div>
            <div class="invoice-meta">Tanggal: {{ $sale->created_at->format('d/m/Y H:i') }}</div>
            @if($sale->due_date)
            <div class="invoice-meta">Jatuh Tempo: {{ $sale->due_date->format('d/m/Y') }}</div>
            @endif
        </div>
    </div>

    <!-- Bill To -->
    @if($sale->customer)
    <div class="bill-to">
        <h4>Tagihan Kepada</h4>
        <p>{{ $sale->customer->name }}</p>
        @if($sale->customer->phone)<p style="font-size:12px;color:#666;">{{ $sale->customer->phone }}</p>@endif
        @if($sale->customer->address)<p style="font-size:12px;color:#666;">{{ $sale->customer->address }}</p>@endif
    </div>
    @endif

    <!-- Items Table -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Harga Jenis</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->details as $i => $detail)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $detail->product->nama_barang }}</td>
                <td>{{ ucfirst($detail->price_type) }}</td>
                <td class="text-right">Rp {{ number_format($detail->unit_price,0,',','.') }}</td>
                <td class="text-right">{{ $detail->quantity }}</td>
                <td class="text-right">Rp {{ number_format($detail->subtotal,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right">Rp {{ number_format($sale->total_amount,0,',','.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Payment Status -->
    <div class="payment-info">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <strong>Jenis Pembayaran:</strong> {{ $sale->payment_type === 'cash' ? 'Cash' : 'Tempo' }}<br>
                <strong>Status:</strong>
                <span class="status-badge {{ $sale->status==='paid'?'status-paid':($sale->status==='partial'?'status-partial':'status-unpaid') }}">
                    {{ $sale->status==='paid'?'LUNAS':($sale->status==='partial'?'SEBAGIAN':'BELUM BAYAR') }}
                </span>
            </div>
            @if($sale->payment_type === 'tempo')
            <div style="text-align:right;">
                <div style="font-size:12px;color:#666;">Sudah Dibayar: Rp {{ number_format($sale->amount_paid,0,',','.') }}</div>
                <div style="font-size:13px;font-weight:bold;color:#dc2626;">Sisa: Rp {{ number_format($sale->total_amount - $sale->amount_paid,0,',','.') }}</div>
            </div>
            @endif
        </div>
    </div>

    @if($sale->notes)
    <div style="background:#fafafa;border:1px solid #e5e7eb;border-radius:6px;padding:12px;margin-bottom:20px;">
        <strong style="font-size:12px;">Catatan:</strong><br>
        <span style="font-size:12px;color:#666;">{{ $sale->notes }}</span>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer-section">
        <div class="footer-note">{{ $setting->invoice_footer }}</div>
        <div style="text-align:center;font-size:12px;">
            <p style="margin-bottom:40px;">_______________________</p>
            <p>Petugas</p>
        </div>
    </div>
</div>
</body>
</html>
