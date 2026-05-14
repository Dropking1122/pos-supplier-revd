<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice {{ $sale->invoice_number }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 13px; color: #1e293b; background: #f8fafc; }
.page { max-width: 780px; margin: 0 auto; padding: 32px; background: #fff; }

.header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 3px solid #4f46e5; }
.company-logo { height: 44px; margin-bottom: 6px; display: block; }
.company-name { font-size: 20px; font-weight: 800; color: #4f46e5; }
.company-info { font-size: 11px; color: #64748b; margin-top: 2px; }
.invoice-badge { font-size: 26px; font-weight: 900; color: #4f46e5; text-align: right; letter-spacing: 3px; }
.invoice-number { font-size: 13px; font-weight: 700; color: #334155; text-align: right; margin-top: 3px; }
.invoice-meta { font-size: 11px; color: #64748b; text-align: right; margin-top: 2px; }

.info-row { display: flex; gap: 14px; margin-bottom: 20px; }
.info-box { background: #f1f5f9; border-radius: 8px; padding: 10px 14px; flex: 1; }
.info-label { font-size: 9.5px; text-transform: uppercase; letter-spacing: .8px; color: #94a3b8; font-weight: 700; margin-bottom: 3px; }
.info-value { font-size: 13px; font-weight: 700; color: #1e293b; }
.info-sub { font-size: 11px; color: #64748b; margin-top: 2px; }
.badge { display: inline-flex; align-items: center; padding: 2px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.badge-paid    { background: #dcfce7; color: #15803d; }
.badge-unpaid  { background: #fee2e2; color: #b91c1c; }
.badge-partial { background: #fef9c3; color: #92400e; }

table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
thead tr { background: #4f46e5; }
th { color: #fff; padding: 8px 10px; text-align: left; font-size: 10.5px; font-weight: 700; letter-spacing: .3px; border: 1px solid #4338ca; }
th.r { text-align: right; }
th.c { text-align: center; }
td { padding: 8px 10px; font-size: 12px; color: #334155; border: 1px solid #e2e8f0; vertical-align: middle; }
td.r { text-align: right; }
td.c { text-align: center; }
tbody tr:nth-child(even) td { background: #f8fafc; }

tfoot tr td { border-top: 2px solid #4f46e5; background: #f1f5f9 !important; font-weight: 700; padding: 8px 10px; }
.grand-row td { background: #4f46e5 !important; color: #fff; font-size: 14px; font-weight: 800; padding: 10px 10px; }

.summary { margin-left: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; min-width: 260px; margin-bottom: 20px; background: #f8fafc; }
.sum-row { display: flex; justify-content: space-between; font-size: 12px; padding: 3px 0; }
.sum-row.total { font-size: 14px; font-weight: 800; padding-top: 8px; border-top: 1px solid #e2e8f0; margin-top: 4px; color: #4f46e5; }

.footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; }
.footer-note { font-size: 11px; color: #94a3b8; font-style: italic; max-width: 380px; }
.signature { text-align: center; font-size: 11.5px; color: #64748b; }
.signature-line { border-bottom: 1px solid #334155; width: 130px; margin: 42px auto 6px; }

@media print {
    body { background: #fff; }
    .no-print { display: none !important; }
    .page { padding: 16px; max-width: 100%; }
    body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
    @page { margin: 10mm; size: A5 portrait; }
}
</style>
</head>
<body>
<div class="page">

    <div class="no-print" style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
        <button onclick="window.print()"
            style="background:#4f46e5;color:#fff;padding:9px 22px;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;">
            🖨 Cetak Invoice
        </button>
        <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank"
            style="background:#64748b;color:#fff;padding:9px 22px;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:6px;">
            📋 Laporan Internal
        </a>
        <button onclick="window.close()"
            style="background:#f1f5f9;color:#334155;padding:9px 22px;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:600;">
            ✕ Tutup
        </button>
    </div>

    <div class="header">
        <div>
            @if($setting->company_logo)
                <img src="{{ asset($setting->company_logo) }}" alt="Logo" class="company-logo">
            @endif
            <div class="company-name">{{ $setting->company_name }}</div>
            @if($setting->company_address)
                <div class="company-info">{{ $setting->company_address }}</div>
            @endif
            @if($setting->company_phone)
                <div class="company-info">{{ $setting->company_phone }}</div>
            @endif
        </div>
        <div>
            <div class="invoice-badge">INVOICE</div>
            <div class="invoice-number"># {{ $sale->invoice_number }}</div>
            <div class="invoice-meta">{{ $sale->created_at->locale('id')->isoFormat('D MMM Y, HH:mm') }}</div>
            @if($sale->due_date)
                <div class="invoice-meta">Jatuh Tempo: {{ $sale->due_date->locale('id')->isoFormat('D MMM Y') }}</div>
            @endif
        </div>
    </div>

    <div class="info-row">
        @if($sale->customer)
        <div class="info-box">
            <div class="info-label">Kepada</div>
            <div class="info-value">{{ $sale->customer->name }}</div>
            @if($sale->customer->phone)
                <div class="info-sub">{{ $sale->customer->phone }}</div>
            @endif
            @if($sale->customer->address)
                <div class="info-sub">{{ $sale->customer->address }}</div>
            @endif
        </div>
        @endif

        <div class="info-box">
            <div class="info-label">Pembayaran</div>
            <div class="info-value">{{ $sale->payment_type === 'cash' ? 'Cash / Tunai' : 'Tempo / Kredit' }}</div>
            <div class="info-sub" style="margin-top:5px;">
                <span class="badge {{ $sale->status==='paid' ? 'badge-paid' : ($sale->status==='partial' ? 'badge-partial' : 'badge-unpaid') }}">
                    {{ $sale->status==='paid' ? '✓ LUNAS' : ($sale->status==='partial' ? '◑ SEBAGIAN' : '✕ BELUM BAYAR') }}
                </span>
            </div>
        </div>

        @if($sale->payment_type === 'tempo')
        <div class="info-box">
            <div class="info-label">Info Hutang</div>
            <div class="info-sub">Total: <strong>Rp {{ number_format($sale->total_amount,0,',','.') }}</strong></div>
            <div class="info-sub">Dibayar: <strong>Rp {{ number_format($sale->amount_paid,0,',','.') }}</strong></div>
            <div class="info-sub" style="color:#b91c1c;font-weight:700;margin-top:3px;">
                Sisa: Rp {{ number_format($sale->total_amount - $sale->amount_paid,0,',','.') }}
            </div>
        </div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:26px;" class="c">No</th>
                <th>Nama Barang</th>
                <th class="c">Qty</th>
                <th class="r">Harga Satuan</th>
                <th class="r">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->details as $i => $detail)
            @php
                $qty       = (int) $detail->quantity;
                $hargaJual = (float) $detail->unit_price;
                $subtotal  = (float) $detail->subtotal;
            @endphp
            <tr>
                <td class="c">{{ $i + 1 }}</td>
                <td>
                    <div style="font-weight:600;">{{ $detail->product->nama_barang }}</div>
                    <div style="font-size:10px;color:#94a3b8;margin-top:1px;">
                        {{ $detail->product->kode_barang }}
                        &bull; <span style="text-transform:capitalize;">{{ $detail->price_type }}</span>
                    </div>
                </td>
                <td class="c" style="font-weight:700;color:#4f46e5;">{{ number_format($qty,0,',','.') }}</td>
                <td class="r">Rp {{ number_format($hargaJual,0,',','.') }}</td>
                <td class="r" style="font-weight:700;">Rp {{ number_format($subtotal,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-row">
                <td colspan="4" class="r" style="letter-spacing:.5px;">TOTAL</td>
                <td class="r">Rp {{ number_format($sale->total_amount,0,',','.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="display:flex;justify-content:flex-end;">
        <div class="summary">
            <div class="sum-row">
                <span style="color:#64748b;">Subtotal ({{ $sale->details->count() }} item)</span>
                <span>Rp {{ number_format($sale->total_amount,0,',','.') }}</span>
            </div>
            @if($sale->payment_type === 'tempo')
            <div class="sum-row">
                <span style="color:#64748b;">Sudah Dibayar</span>
                <span style="color:#15803d;">Rp {{ number_format($sale->amount_paid,0,',','.') }}</span>
            </div>
            <div class="sum-row total">
                <span>Sisa Tagihan</span>
                <span style="color:#b91c1c;">Rp {{ number_format($sale->total_amount - $sale->amount_paid,0,',','.') }}</span>
            </div>
            @else
            <div class="sum-row total">
                <span>Total Tagihan</span>
                <span>Rp {{ number_format($sale->total_amount,0,',','.') }}</span>
            </div>
            @endif
        </div>
    </div>

    @if($sale->notes)
    <div style="background:#fafafa;border:1px solid #e2e8f0;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:11.5px;color:#64748b;">
        <strong style="font-size:10px;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;">Catatan: </strong>
        {{ $sale->notes }}
    </div>
    @endif

    <div class="footer">
        <div class="footer-note">{{ $setting->invoice_footer }}</div>
        <div class="signature">
            <div class="signature-line"></div>
            <div style="font-weight:600;">{{ $setting->petugas ?? 'Petugas' }}</div>
            <div style="font-size:10px;color:#94a3b8;margin-top:2px;">Tanda Tangan</div>
        </div>
    </div>

</div>
</body>
</html>
