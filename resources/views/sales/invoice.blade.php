<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice {{ $sale->invoice_number }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 12px; color: #1e293b; background: #f8fafc; }
.page { max-width: 1050px; margin: 0 auto; padding: 32px; background: #fff; }

/* Header */
.header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 3px solid #4f46e5; }
.company-logo { height: 48px; margin-bottom: 8px; display: block; }
.company-name { font-size: 20px; font-weight: 800; color: #4f46e5; letter-spacing: -0.3px; }
.company-info { font-size: 11px; color: #64748b; margin-top: 3px; }
.invoice-badge { font-size: 26px; font-weight: 900; color: #4f46e5; text-align: right; letter-spacing: 2px; }
.invoice-meta { font-size: 11px; color: #64748b; text-align: right; margin-top: 3px; }
.invoice-number { font-size: 13px; font-weight: 700; color: #334155; text-align: right; }

/* Bill To */
.meta-row { display: flex; gap: 20px; margin-bottom: 24px; }
.meta-box { background: #f1f5f9; border-radius: 8px; padding: 12px 16px; flex: 1; }
.meta-box-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; font-weight: 700; margin-bottom: 5px; }
.meta-box-value { font-size: 13px; font-weight: 700; color: #1e293b; }
.meta-box-sub { font-size: 11px; color: #64748b; margin-top: 2px; }

/* Status badge */
.badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.badge-paid { background: #dcfce7; color: #15803d; }
.badge-unpaid { background: #fee2e2; color: #b91c1c; }
.badge-partial { background: #fef9c3; color: #92400e; }

/* Section title */
.section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 10px; padding-left: 2px; }

/* Tables */
table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
thead tr { background: #4f46e5; }
th { color: #fff; padding: 9px 10px; text-align: left; font-size: 10.5px; font-weight: 700; letter-spacing: 0.3px; white-space: nowrap; }
th.r { text-align: right; }
td { padding: 8px 10px; font-size: 11.5px; color: #334155; border-bottom: 1px solid #e2e8f0; }
td.r { text-align: right; }
tbody tr:nth-child(even) td { background: #f8fafc; }
tbody tr:hover td { background: #eff6ff; }

/* Summary table */
.summary-table { margin-left: auto; width: 280px; border-collapse: collapse; margin-bottom: 24px; }
.summary-table td { padding: 6px 10px; font-size: 12px; border: none; }
.summary-table tr.total-row td { font-size: 14px; font-weight: 800; color: #4f46e5; border-top: 2px solid #4f46e5; padding-top: 8px; }
.summary-table tr.profit-row td { font-size: 12px; font-weight: 700; color: #15803d; }

/* Payment Info */
.payment-card { display: flex; justify-content: space-between; align-items: center; background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; }
.payment-card .left { font-size: 12px; line-height: 1.8; }
.payment-card .right { text-align: right; font-size: 12px; }

/* Notes */
.notes-card { background: #fafafa; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; font-size: 11.5px; color: #64748b; }

/* Divider */
.section-divider { border: none; border-top: 1px dashed #cbd5e1; margin: 24px 0; }

/* Footer */
.footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 28px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
.footer-note { font-size: 11px; color: #94a3b8; font-style: italic; max-width: 400px; }
.signature { text-align: center; font-size: 11.5px; color: #64748b; }
.signature-line { border-bottom: 1px solid #334155; width: 140px; margin: 44px auto 6px; }

/* Print */
@media print {
    body { background: #fff; }
    .no-print { display: none !important; }
    .page { padding: 20px; box-shadow: none; }
    body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
    @page { margin: 15mm; size: A4 landscape; }
}

/* Profit highlight */
.profit-positive { color: #15803d; font-weight: 700; }
.profit-zero { color: #64748b; }
.profit-negative { color: #b91c1c; font-weight: 700; }
.stock-low { color: #b91c1c; font-weight: 700; }
</style>
</head>
<body>
<div class="page">

    {{-- Tombol Aksi --}}
    <div class="no-print" style="display:flex;gap:8px;margin-bottom:24px;">
        <button onclick="window.print()" style="background:#4f46e5;color:#fff;padding:9px 22px;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;">
            🖨 Cetak Invoice
        </button>
        <button onclick="window.close()" style="background:#f1f5f9;color:#334155;padding:9px 22px;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:600;">
            ✕ Tutup
        </button>
    </div>

    {{-- Header --}}
    <div class="header">
        <div>
            @if($setting->company_logo)
                <img src="{{ asset($setting->company_logo) }}" alt="Logo" class="company-logo">
            @endif
            <div class="company-name">{{ $setting->company_name }}</div>
            <div class="company-info">{{ $setting->company_address }}</div>
            <div class="company-info">{{ $setting->company_phone }}</div>
        </div>
        <div>
            <div class="invoice-badge">INVOICE</div>
            <div class="invoice-number" style="margin-top:4px;"># {{ $sale->invoice_number }}</div>
            <div class="invoice-meta">Tanggal: {{ $sale->created_at->format('d M Y, H:i') }}</div>
            @if($sale->due_date)
                <div class="invoice-meta">Jatuh Tempo: {{ $sale->due_date->format('d M Y') }}</div>
            @endif
        </div>
    </div>

    {{-- Meta Row --}}
    <div class="meta-row">
        @if($sale->customer)
        <div class="meta-box">
            <div class="meta-box-label">Tagihan Kepada</div>
            <div class="meta-box-value">{{ $sale->customer->name }}</div>
            @if($sale->customer->phone)
                <div class="meta-box-sub">📞 {{ $sale->customer->phone }}</div>
            @endif
            @if($sale->customer->address)
                <div class="meta-box-sub">📍 {{ $sale->customer->address }}</div>
            @endif
        </div>
        @endif
        <div class="meta-box">
            <div class="meta-box-label">Pembayaran</div>
            <div class="meta-box-value">{{ $sale->payment_type === 'cash' ? 'Cash / Tunai' : 'Tempo / Kredit' }}</div>
            <div class="meta-box-sub" style="margin-top:4px;">
                <span class="badge {{ $sale->status==='paid' ? 'badge-paid' : ($sale->status==='partial' ? 'badge-partial' : 'badge-unpaid') }}">
                    {{ $sale->status==='paid' ? '✓ LUNAS' : ($sale->status==='partial' ? '◑ SEBAGIAN' : '✕ BELUM BAYAR') }}
                </span>
            </div>
        </div>
        @if($sale->payment_type === 'tempo')
        <div class="meta-box">
            <div class="meta-box-label">Info Tempo</div>
            <div class="meta-box-sub">Total: <strong>Rp {{ number_format($sale->total_amount,0,',','.') }}</strong></div>
            <div class="meta-box-sub">Dibayar: <strong>Rp {{ number_format($sale->amount_paid,0,',','.') }}</strong></div>
            <div class="meta-box-sub" style="color:#b91c1c;font-weight:700;">Sisa: Rp {{ number_format($sale->total_amount - $sale->amount_paid,0,',','.') }}</div>
        </div>
        @endif
        <div class="meta-box" style="text-align:right;">
            <div class="meta-box-label">Total Transaksi</div>
            <div style="font-size:22px;font-weight:900;color:#4f46e5;">Rp {{ number_format($sale->total_amount,0,',','.') }}</div>
            <div class="meta-box-sub">{{ $sale->details->count() }} item produk</div>
        </div>
    </div>

    {{-- Rincian Produk --}}
    <div class="section-title">Rincian Item Produk</div>

    @php
        $totalModal = 0;
        $totalPendapatan = 0;
        $totalKeuntungan = 0;
    @endphp

    <div style="overflow-x:auto;">
    <table>
        <thead>
            <tr>
                <th style="width:28px;">No</th>
                <th>Nama Produk</th>
                <th class="r">Stock Awal</th>
                <th class="r">Terjual</th>
                <th class="r">Sisa Stock</th>
                <th class="r">Harga Modal</th>
                <th class="r">Harga Beli</th>
                <th class="r">Harga Jual</th>
                <th class="r">Total Pendapatan</th>
                <th class="r">Keuntungan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->details as $i => $detail)
            @php
                $product     = $detail->product;
                $stockAwal   = $product->kuantitas + $detail->quantity;
                $sisaStock   = $product->kuantitas;
                $hargaModal  = (float) $product->modal_awal;
                $hargaBeli   = $detail->price_type === 'grosir'
                                ? (float) $product->harga_grosir
                                : (float) $product->harga_ecer;
                $hargaJual   = (float) $detail->unit_price;
                $qty         = $detail->quantity;
                $pendapatan  = (float) $detail->subtotal;
                $keuntungan  = ($hargaJual - $hargaModal) * $qty;

                $totalModal      += $hargaModal * $qty;
                $totalPendapatan += $pendapatan;
                $totalKeuntungan += $keuntungan;
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    <div style="font-weight:600;">{{ $product->nama_barang }}</div>
                    <div style="font-size:10px;color:#94a3b8;">{{ $product->kode_barang }} &bull; {{ ucfirst($detail->price_type) }}</div>
                </td>
                <td class="r">{{ number_format($stockAwal, 0, ',', '.') }}</td>
                <td class="r" style="font-weight:600;">{{ number_format($qty, 0, ',', '.') }}</td>
                <td class="r {{ $sisaStock <= $product->stock_minimum ? 'stock-low' : '' }}">
                    {{ number_format($sisaStock, 0, ',', '.') }}
                    @if($sisaStock <= $product->stock_minimum)
                        <span style="font-size:9px;background:#fee2e2;color:#b91c1c;padding:1px 5px;border-radius:4px;margin-left:2px;">LOW</span>
                    @endif
                </td>
                <td class="r" style="color:#64748b;">Rp {{ number_format($hargaModal, 0, ',', '.') }}</td>
                <td class="r" style="color:#64748b;">Rp {{ number_format($hargaBeli, 0, ',', '.') }}</td>
                <td class="r" style="font-weight:600;">Rp {{ number_format($hargaJual, 0, ',', '.') }}</td>
                <td class="r" style="font-weight:700;">Rp {{ number_format($pendapatan, 0, ',', '.') }}</td>
                <td class="r {{ $keuntungan > 0 ? 'profit-positive' : ($keuntungan < 0 ? 'profit-negative' : 'profit-zero') }}">
                    {{ $keuntungan >= 0 ? '' : '-' }}Rp {{ number_format(abs($keuntungan), 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#f1f5f9;">
                <td colspan="7" style="font-weight:700;font-size:12px;color:#334155;text-align:right;border-top:2px solid #4f46e5;padding-top:10px;">TOTAL</td>
                <td></td>
                <td class="r" style="font-weight:800;font-size:13px;color:#4f46e5;border-top:2px solid #4f46e5;padding-top:10px;">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </td>
                <td class="r" style="font-weight:800;font-size:13px;border-top:2px solid #4f46e5;padding-top:10px;
                    color:{{ $totalKeuntungan >= 0 ? '#15803d' : '#b91c1c' }}">
                    {{ $totalKeuntungan >= 0 ? '' : '-' }}Rp {{ number_format(abs($totalKeuntungan), 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
    </div>

    {{-- Ringkasan Keuangan --}}
    <div style="display:flex;justify-content:flex-end;margin-bottom:24px;">
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;min-width:280px;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin-bottom:10px;">Ringkasan Keuangan</div>
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:12px;">
                <span style="color:#64748b;">Total Modal</span>
                <span style="font-weight:600;">Rp {{ number_format($totalModal, 0, ',', '.') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:12px;">
                <span style="color:#64748b;">Total Pendapatan</span>
                <span style="font-weight:600;color:#4f46e5;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
            </div>
            <div style="border-top:1px solid #e2e8f0;margin:8px 0;"></div>
            <div style="display:flex;justify-content:space-between;font-size:14px;">
                <span style="font-weight:700;">Total Keuntungan</span>
                <span style="font-weight:800;color:{{ $totalKeuntungan >= 0 ? '#15803d' : '#b91c1c' }}">
                    {{ $totalKeuntungan >= 0 ? '+' : '-' }}Rp {{ number_format(abs($totalKeuntungan), 0, ',', '.') }}
                </span>
            </div>
            @if($totalPendapatan > 0)
            <div style="display:flex;justify-content:space-between;margin-top:4px;font-size:11px;">
                <span style="color:#94a3b8;">Margin</span>
                <span style="color:#94a3b8;">{{ number_format(($totalKeuntungan / $totalPendapatan) * 100, 1) }}%</span>
            </div>
            @endif
        </div>
    </div>

    @if($sale->notes)
    <div class="notes-card">
        <strong style="font-size:10px;text-transform:uppercase;letter-spacing:0.5px;color:#94a3b8;">Catatan</strong><br>
        <span style="margin-top:4px;display:block;">{{ $sale->notes }}</span>
    </div>
    @endif

    {{-- Footer --}}
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
