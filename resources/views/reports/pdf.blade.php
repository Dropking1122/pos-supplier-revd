<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }

/* ── Header ──────────────────────────────────── */
.header { border-bottom: 3px solid #4f46e5; padding-bottom: 14px; margin-bottom: 18px; }
.header-inner { display: table; width: 100%; }
.header-left  { display: table-cell; vertical-align: top; width: 60%; }
.header-right { display: table-cell; vertical-align: top; text-align: right; }
.company-name { font-size: 18px; font-weight: bold; color: #4f46e5; line-height: 1.2; }
.company-sub  { font-size: 10px; color: #6b7280; margin-top: 4px; line-height: 1.7; }
.report-badge { background: #4f46e5; color: #fff; font-size: 13px; font-weight: bold;
                padding: 5px 16px; border-radius: 20px; display: inline-block; letter-spacing: 1px; }
.report-meta  { font-size: 10px; color: #6b7280; margin-top: 8px; line-height: 1.7; }

/* ── Summary Cards ───────────────────────────── */
.cards { display: table; width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 18px; }
.card { display: table-cell; border-radius: 8px; padding: 10px 12px; border: 1px solid #e5e7eb; width: 25%; }
.card-blue   { background: #eff6ff; border-color: #bfdbfe; }
.card-green  { background: #f0fdf4; border-color: #bbf7d0; }
.card-amber  { background: #fffbeb; border-color: #fde68a; }
.card-red    { background: #fef2f2; border-color: #fecaca; }
.card-label  { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; color: #6b7280; margin-bottom: 5px; }
.card-value  { font-size: 15px; font-weight: bold; line-height: 1; }
.card-sub    { font-size: 9px; color: #9ca3af; margin-top: 4px; }
.card-blue  .card-value { color: #1d4ed8; }
.card-green .card-value { color: #15803d; }
.card-amber .card-value { color: #b45309; }
.card-red   .card-value { color: #dc2626; }

/* ── Table ───────────────────────────────────── */
.section-title { font-size: 11px; font-weight: bold; color: #374151; margin-bottom: 8px;
                 border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
table { width: 100%; border-collapse: collapse; }
thead tr { background: #4f46e5; }
th { color: #fff; font-size: 9.5px; font-weight: bold; text-transform: uppercase; letter-spacing: .4px;
     padding: 8px 10px; text-align: left; border: 1px solid #4338ca; }
th.r { text-align: right; }
th.c { text-align: center; }
td { padding: 7px 10px; font-size: 10.5px; border: 1px solid #e5e7eb; color: #374151; vertical-align: middle; }
td.r { text-align: right; }
td.c { text-align: center; }
.row-even { background: #f9fafb; }
.row-odd  { background: #ffffff; }
.mono { font-size: 9.5px; color: #4f46e5; font-weight: 600; }
.profit-pos { color: #15803d; font-weight: 600; }
.profit-neg { color: #dc2626; font-weight: 600; }
.badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 9px; font-weight: bold; }
.badge-paid    { background: #d1fae5; color: #065f46; }
.badge-partial { background: #fef3c7; color: #92400e; }
.badge-unpaid  { background: #fee2e2; color: #991b1b; }
.type-cash  { color: #0369a1; font-weight: 600; }
.type-tempo { color: #7c3aed; font-weight: 600; }

/* ── Total Row ───────────────────────────────── */
.total-row td { background: #eef2ff; font-weight: bold; font-size: 11px;
                border-top: 2px solid #4f46e5; color: #1f2937; padding: 9px 10px; }

/* ── Footer ──────────────────────────────────── */
.footer { margin-top: 24px; border-top: 1px solid #e5e7eb; padding-top: 12px; }
.footer-inner { display: table; width: 100%; }
.footer-note  { display: table-cell; font-size: 9.5px; color: #9ca3af; font-style: italic; }
.footer-sign  { display: table-cell; text-align: center; font-size: 9.5px; color: #6b7280; width: 160px; }
.sign-line    { display: block; margin-top: 36px; border-top: 1px solid #9ca3af; padding-top: 4px; }
</style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="header-inner">
        <div class="header-left">
            @if($setting->company_logo)
                <img src="{{ public_path($setting->company_logo) }}" alt="Logo"
                     style="height:44px;margin-bottom:6px;display:block;">
            @endif
            <div class="company-name">{{ $setting->company_name }}</div>
            <div class="company-sub">
                @if($setting->company_address){{ $setting->company_address }}<br>@endif
                @if($setting->company_phone)Tel: {{ $setting->company_phone }}@endif
            </div>
        </div>
        <div class="header-right">
            <div class="report-badge">LAPORAN PENJUALAN</div>
            <div class="report-meta">
                @if($request->type === 'daily' && $request->date)
                    Periode: {{ \Carbon\Carbon::parse($request->date)->isoFormat('D MMMM Y') }}<br>
                @elseif($request->type === 'monthly' && $request->month)
                    Periode: {{ \Carbon\Carbon::createFromFormat('Y-m', $request->month)->isoFormat('MMMM Y') }}<br>
                @elseif($request->type === 'yearly' && $request->year)
                    Periode: Tahun {{ $request->year }}<br>
                @else
                    Periode: Semua Data<br>
                @endif
                Dicetak: {{ now()->isoFormat('D MMMM Y, HH:mm') }}
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
@php
    $countPaid    = $sales->where('status','paid')->count();
    $countPartial = $sales->where('status','partial')->count();
    $countUnpaid  = $sales->where('status','unpaid')->count();
    $totalUnpaid  = $sales->sum(fn($s) => $s->total_amount - $s->amount_paid);
    $margin       = $totalRevenue > 0 ? round($totalProfit / $totalRevenue * 100, 1) : 0;
@endphp
<div class="cards">
    <div class="card card-blue">
        <div class="card-label">Total Penjualan</div>
        <div class="card-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="card-sub">{{ $sales->count() }} transaksi</div>
    </div>
    <div class="card card-green">
        <div class="card-label">Total Profit</div>
        <div class="card-value">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
        <div class="card-sub">Margin {{ $margin }}%</div>
    </div>
    <div class="card card-amber">
        <div class="card-label">Sudah Lunas</div>
        <div class="card-value">{{ $countPaid }}</div>
        <div class="card-sub">{{ $countPartial }} sebagian bayar</div>
    </div>
    <div class="card card-red">
        <div class="card-label">Total Piutang</div>
        <div class="card-value">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</div>
        <div class="card-sub">{{ $countUnpaid + $countPartial }} belum lunas</div>
    </div>
</div>

<!-- Detail Table -->
<div class="section-title">Rincian Transaksi ({{ $sales->count() }} data)</div>
<table>
    <thead>
        <tr>
            <th style="width:22px;">No</th>
            <th>No. Invoice</th>
            <th>Tanggal &amp; Waktu</th>
            <th>Customer</th>
            <th class="c">Tipe</th>
            <th class="r">Total</th>
            <th class="r">Profit</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $i => $sale)
        @php
            $profit = $sale->details->sum(fn($d) => $d->subtotal - ($d->quantity * ($d->product->modal_awal ?? 0)));
        @endphp
        <tr class="{{ $i % 2 === 0 ? 'row-odd' : 'row-even' }}">
            <td class="c" style="color:#9ca3af;font-size:10px;">{{ $i + 1 }}</td>
            <td class="mono">{{ $sale->invoice_number }}</td>
            <td style="color:#6b7280;font-size:10px;white-space:nowrap;">{{ $sale->created_at->format('d/m/Y') }} &nbsp; {{ $sale->created_at->format('H:i') }}</td>
            <td>{{ $sale->customer?->name ?? '<i style="color:#9ca3af">Umum</i>' }}</td>
            <td class="c {{ $sale->payment_type === 'cash' ? 'type-cash' : 'type-tempo' }}" style="font-size:9.5px;">
                {{ strtoupper($sale->payment_type) }}
            </td>
            <td class="r" style="font-weight:600;white-space:nowrap;">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
            <td class="r {{ $profit >= 0 ? 'profit-pos' : 'profit-neg' }}" style="white-space:nowrap;">
                {{ $profit >= 0 ? '' : '−' }}Rp {{ number_format(abs($profit), 0, ',', '.') }}
            </td>
            <td class="c">
                <span class="badge {{ $sale->status==='paid' ? 'badge-paid' : ($sale->status==='partial' ? 'badge-partial' : 'badge-unpaid') }}">
                    {{ $sale->status==='paid' ? 'Lunas' : ($sale->status==='partial' ? 'Sebagian' : 'Belum Bayar') }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align:center;padding:20px;color:#9ca3af;font-style:italic;">
                Tidak ada data transaksi pada periode ini
            </td>
        </tr>
        @endforelse

        @if($sales->count())
        <tr class="total-row">
            <td colspan="5" class="r">GRAND TOTAL ({{ $sales->count() }} transaksi)</td>
            <td class="r" style="white-space:nowrap;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            <td class="r {{ $totalProfit >= 0 ? 'profit-pos' : 'profit-neg' }}" style="white-space:nowrap;">
                {{ $totalProfit >= 0 ? '' : '−' }}Rp {{ number_format(abs($totalProfit), 0, ',', '.') }}
            </td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>

<!-- Footer -->
<div class="footer">
    <div class="footer-inner">
        <div class="footer-note">
            @if($setting->invoice_footer){{ $setting->invoice_footer }}<br>@endif
            Laporan ini digenerate secara otomatis oleh sistem POS.
        </div>
        <div class="footer-sign">
            Mengetahui,
            <span class="sign-line">{{ $setting->petugas ?? 'Pimpinan / Manager' }}</span>
        </div>
    </div>
</div>

</body>
</html>
