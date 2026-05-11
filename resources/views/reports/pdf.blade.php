<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }

/* ── Header ─────────────────────────────────── */
.header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 14px; margin-bottom: 16px; border-bottom: 3px solid #4f46e5; }
.header-left { display: flex; align-items: flex-start; gap: 12px; }
.logo { width: 52px; height: 52px; object-fit: contain; border-radius: 6px; }
.logo-placeholder { width: 48px; height: 48px; background: #4f46e5; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
.company-name { font-size: 16px; font-weight: bold; color: #4f46e5; line-height: 1.2; }
.company-sub { font-size: 10px; color: #6b7280; margin-top: 3px; line-height: 1.6; }
.header-right { text-align: right; }
.report-badge { background: #4f46e5; color: white; font-size: 12px; font-weight: bold; padding: 5px 14px; border-radius: 20px; display: inline-block; }
.report-period { font-size: 10px; color: #6b7280; margin-top: 6px; line-height: 1.6; }

/* ── Summary Cards ───────────────────────────── */
.summary { display: flex; gap: 10px; margin-bottom: 16px; }
.card { flex: 1; border-radius: 8px; padding: 10px 12px; border: 1px solid #e5e7eb; }
.card-blue  { background: #eff6ff; border-color: #bfdbfe; }
.card-green { background: #f0fdf4; border-color: #bbf7d0; }
.card-amber { background: #fffbeb; border-color: #fde68a; }
.card-red   { background: #fef2f2; border-color: #fecaca; }
.card-label { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; margin-bottom: 4px; }
.card-value { font-size: 15px; font-weight: bold; color: #1f2937; line-height: 1; }
.card-sub   { font-size: 9px; color: #9ca3af; margin-top: 3px; }
.card-blue .card-value  { color: #1d4ed8; }
.card-green .card-value { color: #15803d; }
.card-amber .card-value { color: #b45309; }
.card-red .card-value   { color: #dc2626; }

/* ── Table ───────────────────────────────────── */
.section-title { font-size: 11px; font-weight: bold; color: #374151; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 1px solid #e5e7eb; }
table { width: 100%; border-collapse: collapse; }
thead tr { background: #4f46e5; }
th { color: white; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.4px; padding: 8px 9px; text-align: left; }
th.r { text-align: right; }
th.c { text-align: center; }
td { padding: 7px 9px; font-size: 10px; border-bottom: 1px solid #f3f4f6; color: #374151; }
td.r { text-align: right; }
td.c { text-align: center; }
.odd  { background: #ffffff; }
.even { background: #f9fafb; }
.mono { font-family: DejaVu Sans Mono, monospace; font-size: 9.5px; color: #4f46e5; }
.profit { color: #16a34a; font-weight: 600; }
.type-cash   { color: #0369a1; }
.type-tempo  { color: #9333ea; }
.badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 9px; font-weight: bold; }
.badge-paid    { background: #d1fae5; color: #065f46; }
.badge-partial { background: #fef3c7; color: #92400e; }
.badge-unpaid  { background: #fee2e2; color: #991b1b; }

/* ── Total Row ───────────────────────────────── */
.total-row td { background: #eef2ff; font-weight: bold; font-size: 11px; border-top: 2px solid #4f46e5; color: #1f2937; padding: 9px 9px; }
.total-row .profit { color: #15803d; }

/* ── Footer ──────────────────────────────────── */
.footer { margin-top: 24px; display: flex; justify-content: space-between; align-items: flex-end; border-top: 1px solid #e5e7eb; padding-top: 12px; }
.footer-note { font-size: 9.5px; color: #9ca3af; font-style: italic; }
.footer-stamp { text-align: center; font-size: 9.5px; color: #6b7280; }
.stamp-line { display: block; margin-top: 36px; border-top: 1px solid #9ca3af; padding-top: 4px; }
</style>
</head>
<body>

<!-- ══ Header ══════════════════════════════════════ -->
<div class="header">
    <div class="header-left">
        @if($setting->company_logo)
        <img class="logo" src="{{ public_path('storage/'.$setting->company_logo) }}" alt="Logo">
        @endif
        <div>
            <div class="company-name">{{ $setting->company_name }}</div>
            <div class="company-sub">
                @if($setting->company_address){{ $setting->company_address }}<br>@endif
                @if($setting->company_phone){{ $setting->company_phone }}@endif
            </div>
        </div>
    </div>
    <div class="header-right">
        <div class="report-badge">LAPORAN PENJUALAN</div>
        <div class="report-period">
            @if($request->type === 'daily')
                Periode: {{ \Carbon\Carbon::parse($request->date)->isoFormat('D MMMM Y') }}<br>
            @elseif($request->type === 'monthly')
                Periode: {{ \Carbon\Carbon::createFromFormat('Y-m',$request->month)->isoFormat('MMMM Y') }}<br>
            @else
                Periode: Tahun {{ $request->year }}<br>
            @endif
            Dicetak: {{ now()->isoFormat('D MMMM Y, HH:mm') }}
        </div>
    </div>
</div>

<!-- ══ Summary Cards ════════════════════════════════ -->
@php
    $countPaid    = $sales->where('status','paid')->count();
    $countPartial = $sales->where('status','partial')->count();
    $countUnpaid  = $sales->where('status','unpaid')->count();
    $totalUnpaid  = $sales->sum(fn($s) => $s->total_amount - $s->amount_paid);
@endphp
<div class="summary">
    <div class="card card-blue">
        <div class="card-label">Total Penjualan</div>
        <div class="card-value">Rp {{ number_format($totalRevenue,0,',','.') }}</div>
        <div class="card-sub">{{ $sales->count() }} transaksi</div>
    </div>
    <div class="card card-green">
        <div class="card-label">Total Profit</div>
        <div class="card-value">Rp {{ number_format($totalProfit,0,',','.') }}</div>
        <div class="card-sub">
            @if($totalRevenue > 0)
                Margin {{ number_format($totalProfit/$totalRevenue*100,1) }}%
            @else
                -
            @endif
        </div>
    </div>
    <div class="card card-amber">
        <div class="card-label">Sudah Lunas</div>
        <div class="card-value">{{ $countPaid }}</div>
        <div class="card-sub">{{ $countPartial }} sebagian bayar</div>
    </div>
    <div class="card card-red">
        <div class="card-label">Total Piutang</div>
        <div class="card-value">Rp {{ number_format($totalUnpaid,0,',','.') }}</div>
        <div class="card-sub">{{ $countUnpaid + $countPartial }} transaksi belum lunas</div>
    </div>
</div>

<!-- ══ Detail Table ══════════════════════════════════ -->
<div class="section-title">Rincian Transaksi</div>
<table>
    <thead>
        <tr>
            <th style="width:22px">No</th>
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
        <tr class="{{ $i % 2 === 0 ? 'odd' : 'even' }}">
            <td class="c" style="color:#9ca3af">{{ $i + 1 }}</td>
            <td class="mono">{{ $sale->invoice_number }}</td>
            <td style="color:#6b7280">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $sale->customer?->name ?? '<i style="color:#9ca3af">Umum</i>' }}</td>
            <td class="c {{ $sale->payment_type === 'cash' ? 'type-cash' : 'type-tempo' }}" style="font-weight:600;font-size:9.5px;">
                {{ strtoupper($sale->payment_type) }}
            </td>
            <td class="r" style="font-weight:600">Rp {{ number_format($sale->total_amount,0,',','.') }}</td>
            <td class="r profit">Rp {{ number_format($profit,0,',','.') }}</td>
            <td class="c">
                <span class="badge {{ $sale->status==='paid'?'badge-paid':($sale->status==='partial'?'badge-partial':'badge-unpaid') }}">
                    {{ $sale->status==='paid'?'Lunas':($sale->status==='partial'?'Sebagian':'Belum Bayar') }}
                </span>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:20px;color:#9ca3af;font-style:italic;">Tidak ada data transaksi pada periode ini</td></tr>
        @endforelse
        @if($sales->count())
        <tr class="total-row">
            <td colspan="5" class="r">GRAND TOTAL ({{ $sales->count() }} transaksi)</td>
            <td class="r">Rp {{ number_format($totalRevenue,0,',','.') }}</td>
            <td class="r profit">Rp {{ number_format($totalProfit,0,',','.') }}</td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>

<!-- ══ Footer ═══════════════════════════════════════ -->
<div class="footer">
    <div class="footer-note">
        @if($setting->invoice_footer){{ $setting->invoice_footer }}<br>@endif
        Laporan ini digenerate secara otomatis oleh sistem POS.
    </div>
    <div class="footer-stamp">
        Mengetahui,<br>
        <span class="stamp-line">Pimpinan / Manager</span>
    </div>
</div>

</body>
</html>
