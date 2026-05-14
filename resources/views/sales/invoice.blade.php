<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice {{ $sale->invoice_number }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Arial', sans-serif; font-size: 12px; color: #1e293b; background: #f8fafc; }
.page { max-width: 1100px; margin: 0 auto; padding: 32px; background: #fff; }

/* ── Header ── */
.header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 18px; border-bottom: 3px solid #4f46e5; }
.company-logo { height: 46px; margin-bottom: 7px; display: block; }
.company-name { font-size: 20px; font-weight: 800; color: #4f46e5; letter-spacing: -.3px; }
.company-info { font-size: 11px; color: #64748b; margin-top: 3px; }
.invoice-badge { font-size: 28px; font-weight: 900; color: #4f46e5; text-align: right; letter-spacing: 3px; }
.invoice-number { font-size: 13px; font-weight: 700; color: #334155; text-align: right; margin-top: 4px; }
.invoice-meta { font-size: 11px; color: #64748b; text-align: right; margin-top: 2px; }

/* ── Meta cards ── */
.meta-row { display: flex; gap: 16px; margin-bottom: 22px; }
.meta-box { background: #f1f5f9; border-radius: 8px; padding: 11px 14px; flex: 1; }
.meta-label { font-size: 9.5px; text-transform: uppercase; letter-spacing: .8px; color: #94a3b8; font-weight: 700; margin-bottom: 4px; }
.meta-value { font-size: 13px; font-weight: 700; color: #1e293b; }
.meta-sub { font-size: 11px; color: #64748b; margin-top: 2px; }
.badge { display: inline-flex; align-items: center; padding: 2px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.badge-paid    { background: #dcfce7; color: #15803d; }
.badge-unpaid  { background: #fee2e2; color: #b91c1c; }
.badge-partial { background: #fef9c3; color: #92400e; }

/* ── Section title ── */
.section-title { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #64748b; margin-bottom: 8px; }

/* ── Table ── */
table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #cbd5e1; }
thead tr { background: #4f46e5; }
th { color: #fff; padding: 8px 10px; text-align: left; font-size: 10px; font-weight: 700; letter-spacing: .3px; white-space: nowrap; border: 1px solid #4338ca; }
th.r { text-align: right; }
td { padding: 7px 10px; font-size: 11.5px; color: #334155; border: 1px solid #e2e8f0; vertical-align: middle; }
td.r { text-align: right; }
tbody tr:nth-child(even) td { background: #f8fafc; }
tbody tr:hover td { background: #eff6ff; }

/* ── Totals footer ── */
tfoot td { border-top: 2px solid #4f46e5; background: #f1f5f9 !important; font-weight: 700; padding: 8px 10px; }
.grand-row td { background: #4f46e5 !important; color: #fff; font-size: 13px; font-weight: 800; padding: 9px 10px; }

/* ── Summary box ── */
.summary-box { margin-left: auto; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; min-width: 290px; margin-bottom: 20px; }
.summary-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 5px; }
.summary-row.bold { font-weight: 700; font-size: 13px; }
.summary-divider { border-top: 1px solid #e2e8f0; margin: 7px 0; }

/* ── Notes ── */
.notes-card { background: #fafafa; border: 1px solid #e2e8f0; border-radius: 8px; padding: 11px 14px; margin-bottom: 18px; font-size: 11.5px; color: #64748b; }

/* ── Footer ── */
.footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e2e8f0; }
.footer-note { font-size: 11px; color: #94a3b8; font-style: italic; max-width: 400px; }
.signature { text-align: center; font-size: 11.5px; color: #64748b; }
.signature-line { border-bottom: 1px solid #334155; width: 140px; margin: 44px auto 6px; }

/* ── Print ── */
@media print {
    body { background: #fff; }
    .no-print { display: none !important; }
    .page { padding: 0; max-width: 100%; }
    body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
    @page { margin: 15mm 14mm; size: A4 landscape; }
}
</style>
</head>
<body>
<div class="page">

    {{-- Tombol Aksi --}}
    <div class="no-print" style="display:flex;gap:8px;margin-bottom:22px;flex-wrap:wrap;align-items:center;">
        <button onclick="window.print()"
            style="background:#4f46e5;color:#fff;padding:9px 22px;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;">
            🖨 Cetak Laporan
        </button>
        <a href="{{ route('sales.invoice-customer', $sale->id) }}" target="_blank"
            style="background:#0284c7;color:#fff;padding:9px 22px;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:6px;">
            🧾 Invoice Customer
        </a>
        <a href="{{ route('sales.invoice-excel', $sale->id) }}"
            style="background:#16a34a;color:#fff;padding:9px 22px;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:6px;">
            📥 Export Excel
        </a>
        <button onclick="window.close()"
            style="background:#f1f5f9;color:#334155;padding:9px 22px;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:600;">
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
            <div class="invoice-badge">LAPORAN PENJUALAN</div>
            <div class="invoice-number">{{ $sale->invoice_number }}</div>
            <div class="invoice-meta">Tanggal: {{ $sale->created_at->locale('id')->isoFormat('D MMM Y, HH:mm') }}</div>
            @if($sale->due_date)
                <div class="invoice-meta">Jatuh Tempo: {{ $sale->due_date->locale('id')->isoFormat('D MMM Y') }}</div>
            @endif
        </div>
    </div>

    {{-- Meta Row --}}
    <div class="meta-row">
        @if($sale->customer)
        <div class="meta-box">
            <div class="meta-label">Tagihan Kepada</div>
            <div class="meta-value">{{ $sale->customer->name }}</div>
            @if($sale->customer->phone)
                <div class="meta-sub">📞 {{ $sale->customer->phone }}</div>
            @endif
            @if($sale->customer->address)
                <div class="meta-sub">📍 {{ $sale->customer->address }}</div>
            @endif
        </div>
        @endif

        <div class="meta-box">
            <div class="meta-label">Pembayaran</div>
            <div class="meta-value">{{ $sale->payment_type === 'cash' ? 'Cash / Tunai' : 'Tempo / Kredit' }}</div>
            <div class="meta-sub" style="margin-top:5px;">
                <span class="badge {{ $sale->status==='paid' ? 'badge-paid' : ($sale->status==='partial' ? 'badge-partial' : 'badge-unpaid') }}">
                    {{ $sale->status==='paid' ? '✓ LUNAS' : ($sale->status==='partial' ? '◑ SEBAGIAN' : '✕ BELUM BAYAR') }}
                </span>
            </div>
        </div>

        @if($sale->payment_type === 'tempo')
        <div class="meta-box">
            <div class="meta-label">Info Tempo</div>
            <div class="meta-sub">Total tagihan: <strong>Rp {{ number_format($sale->total_amount,0,',','.') }}</strong></div>
            <div class="meta-sub">Sudah dibayar: <strong>Rp {{ number_format($sale->amount_paid,0,',','.') }}</strong></div>
            <div class="meta-sub" style="color:#b91c1c;font-weight:700;margin-top:3px;">
                Sisa hutang: Rp {{ number_format($sale->total_amount - $sale->amount_paid,0,',','.') }}
            </div>
        </div>
        @endif

        <div class="meta-box" style="text-align:right;">
            <div class="meta-label">Total Penjualan</div>
            <div style="font-size:24px;font-weight:900;color:#4f46e5;">
                Rp {{ number_format($sale->total_amount,0,',','.') }}
            </div>
            <div class="meta-sub">{{ $sale->details->count() }} item produk</div>
        </div>
    </div>

    {{-- ── Tabel Rincian Produk ── --}}
    <div class="section-title">Rincian Item Produk</div>

    @php
        $totalModal      = 0;
        $totalJual       = 0;
        $totalKeuntungan = 0;
    @endphp

    <div style="overflow-x:auto;">
    <table>
        <thead>
            <tr>
                <th style="width:26px;">No</th>
                <th>Nama Barang</th>
                <th class="r">Harga Beli<br><span style="font-weight:400;opacity:.8;">(Modal)</span></th>
                <th class="r">Harga Jual<br><span style="font-weight:400;opacity:.8;">(Satuan)</span></th>
                <th class="r">Stock<br>Awal</th>
                <th class="r">Stock<br>Terjual</th>
                <th class="r">Sisa<br>Stock</th>
                <th class="r">Jumlah<br>Total Modal</th>
                <th class="r">Jumlah<br>Total Jual</th>
                <th class="r">Keuntungan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->details as $i => $detail)
            @php
                $product    = $detail->product;
                $qty        = (int) $detail->quantity;

                // Gunakan stock_before jika ada (snapshot saat transaksi),
                // fallback ke estimasi dari stok saat ini untuk data lama
                $stockAwal  = $detail->stock_before !== null
                                ? (int) $detail->stock_before
                                : ($product->kuantitas + $qty);
                $stockSisa  = $stockAwal - $qty;

                $hargaBeli  = (float) $product->modal_awal;
                $hargaJual  = (float) $detail->unit_price;

                $totalModalItem = $hargaBeli * $qty;
                $totalJualItem  = (float) $detail->subtotal;
                $keuntungan     = $totalJualItem - $totalModalItem;

                $totalModal      += $totalModalItem;
                $totalJual       += $totalJualItem;
                $totalKeuntungan += $keuntungan;

                $lowStock = $stockSisa <= $product->stock_minimum;
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    <div style="font-weight:600;color:#1e293b;">{{ $product->nama_barang }}</div>
                    <div style="font-size:10px;color:#94a3b8;margin-top:1px;">
                        {{ $product->kode_barang }}
                        &bull;
                        <span style="text-transform:capitalize;">{{ $detail->price_type }}</span>
                    </div>
                </td>
                <td class="r" style="color:#64748b;">
                    Rp {{ number_format($hargaBeli, 0, ',', '.') }}
                </td>
                <td class="r" style="font-weight:600;">
                    Rp {{ number_format($hargaJual, 0, ',', '.') }}
                </td>
                <td class="r" style="color:#475569;">
                    {{ number_format($stockAwal, 0, ',', '.') }}
                </td>
                <td class="r" style="font-weight:700;color:#4f46e5;">
                    {{ number_format($qty, 0, ',', '.') }}
                </td>
                <td class="r">
                    <span style="font-weight:600; color:#334155;">
                        {{ number_format($stockSisa, 0, ',', '.') }}
                    </span>
                </td>
                <td class="r" style="color:#64748b;">
                    Rp {{ number_format($totalModalItem, 0, ',', '.') }}
                </td>
                <td class="r" style="font-weight:700;color:#1e293b;">
                    Rp {{ number_format($totalJualItem, 0, ',', '.') }}
                </td>
                <td class="r" style="font-weight:700; color:{{ $keuntungan >= 0 ? '#15803d' : '#b91c1c' }}">
                    {{ $keuntungan >= 0 ? '' : '−' }}Rp {{ number_format(abs($keuntungan), 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            {{-- Subtotal row --}}
            <tr>
                <td colspan="7" class="r" style="color:#475569;font-size:11px;">
                    Subtotal ({{ $sale->details->count() }} item)
                </td>
                <td class="r">Rp {{ number_format($totalModal, 0, ',', '.') }}</td>
                <td class="r">Rp {{ number_format($totalJual, 0, ',', '.') }}</td>
                <td class="r" style="color:{{ $totalKeuntungan >= 0 ? '#15803d' : '#b91c1c' }}">
                    {{ $totalKeuntungan >= 0 ? '' : '−' }}Rp {{ number_format(abs($totalKeuntungan), 0, ',', '.') }}
                </td>
            </tr>
            {{-- Grand total row (highlighted) --}}
            <tr class="grand-row">
                <td colspan="8" class="r" style="letter-spacing:.5px;">
                    TOTAL PENJUALAN
                </td>
                <td class="r" style="font-size:14px;">
                    Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                </td>
                <td class="r" style="font-size:13px;">
                    {{ $totalKeuntungan >= 0 ? '+' : '−' }}Rp {{ number_format(abs($totalKeuntungan), 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
    </div>

    {{-- Ringkasan Keuangan --}}
    <div style="display:flex;justify-content:flex-end;">
        <div class="summary-box">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:10px;">
                Ringkasan Keuangan
            </div>
            <div class="summary-row">
                <span style="color:#64748b;">Total Modal (HPP)</span>
                <span>Rp {{ number_format($totalModal, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span style="color:#64748b;">Total Penjualan</span>
                <span style="color:#4f46e5;font-weight:600;">Rp {{ number_format($totalJual, 0, ',', '.') }}</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-row bold">
                <span>Keuntungan Bersih</span>
                <span style="color:{{ $totalKeuntungan >= 0 ? '#15803d' : '#b91c1c' }}">
                    {{ $totalKeuntungan >= 0 ? '+' : '−' }}Rp {{ number_format(abs($totalKeuntungan), 0, ',', '.') }}
                </span>
            </div>
            @if($totalJual > 0)
            <div class="summary-row" style="font-size:11px;margin-top:2px;">
                <span style="color:#94a3b8;">Margin</span>
                <span style="color:#94a3b8;">{{ number_format(($totalKeuntungan / $totalJual) * 100, 1) }}%</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Catatan --}}
    @if($sale->notes)
    <div class="notes-card">
        <strong style="font-size:10px;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;">Catatan</strong>
        <div style="margin-top:4px;">{{ $sale->notes }}</div>
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
