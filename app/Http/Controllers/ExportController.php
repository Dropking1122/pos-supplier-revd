<?php
namespace App\Http\Controllers;

use App\Models\{Sale, Product, Setting};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function pdf(Request $request)
    {
        $sales        = $this->getSales($request);
        $setting      = Setting::getSettings();
        $totalRevenue = $sales->sum('total_amount');
        $totalProfit  = $sales->sum(
            fn($s) => $s->details->sum(fn($d) => $d->subtotal - ($d->quantity * ($d->product->modal_awal ?? 0)))
        );

        $pdf = Pdf::loadView('reports.pdf', compact('sales', 'setting', 'totalRevenue', 'totalProfit', 'request'))
            ->setPaper('a4', 'landscape');

        $period = $this->getPeriodLabel($request);
        return $pdf->download("laporan-penjualan-{$period}.pdf");
    }

    public function excel(Request $request)
    {
        $sales    = $this->getSales($request);
        $setting  = Setting::getSettings();
        $period   = $this->getPeriodLabel($request);
        $filename = "laporan-penjualan-{$period}.csv";

        $totalRevenue = $sales->sum('total_amount');
        $totalProfit  = $sales->sum(
            fn($s) => $s->details->sum(fn($d) => $d->subtotal - ($d->quantity * ($d->product->modal_awal ?? 0)))
        );
        $countAll     = $sales->count();
        $countPaid    = $sales->where('status', 'paid')->count();
        $countPartial = $sales->where('status', 'partial')->count();
        $countUnpaid  = $sales->where('status', 'unpaid')->count();
        $totalUnpaid  = $sales->sum(fn($s) => $s->total_amount - $s->amount_paid);

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($sales, $setting, $request, $totalRevenue, $totalProfit, $countAll, $countPaid, $countPartial, $countUnpaid, $totalUnpaid) {
            $f = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // ── Company Header ──────────────────────────
            fputcsv($f, ['LAPORAN PENJUALAN']);
            fputcsv($f, ['Toko', $setting->company_name]);
            if ($setting->company_address) fputcsv($f, ['Alamat', $setting->company_address]);
            if ($setting->company_phone)   fputcsv($f, ['Telepon', $setting->company_phone]);
            fputcsv($f, ['Periode', $this->getPeriodDisplay($request)]);
            fputcsv($f, ['Dicetak Pada', now()->format('d/m/Y H:i')]);
            fputcsv($f, []);

            // ── Summary Section ─────────────────────────
            fputcsv($f, ['=== RINGKASAN ===']);
            fputcsv($f, ['Total Penjualan', 'Rp ' . number_format($totalRevenue, 0, ',', '.')]);
            fputcsv($f, ['Total Profit',    'Rp ' . number_format($totalProfit,  0, ',', '.')]);
            if ($totalRevenue > 0) {
                fputcsv($f, ['Margin Profit', number_format($totalProfit / $totalRevenue * 100, 1) . '%']);
            }
            fputcsv($f, ['Jumlah Transaksi',    $countAll]);
            fputcsv($f, ['Transaksi Lunas',      $countPaid]);
            fputcsv($f, ['Transaksi Sebagian',   $countPartial]);
            fputcsv($f, ['Transaksi Belum Bayar',$countUnpaid]);
            fputcsv($f, ['Total Piutang',  'Rp ' . number_format($totalUnpaid,  0, ',', '.')]);
            fputcsv($f, []);

            // ── Column Headers ──────────────────────────
            fputcsv($f, ['=== DETAIL TRANSAKSI ===']);
            fputcsv($f, [
                'No',
                'No. Invoice',
                'Tanggal',
                'Waktu',
                'Customer',
                'Tipe Pembayaran',
                'Total (Rp)',
                'Modal (Rp)',
                'Profit (Rp)',
                'Margin (%)',
                'Status Bayar',
                'Jatuh Tempo',
                'Sudah Dibayar (Rp)',
                'Sisa Hutang (Rp)',
            ]);

            // ── Data Rows ────────────────────────────────
            foreach ($sales as $i => $sale) {
                $modal  = $sale->details->sum(fn($d) => $d->quantity * ($d->product->modal_awal ?? 0));
                $profit = $sale->details->sum(fn($d) => $d->subtotal - ($d->quantity * ($d->product->modal_awal ?? 0)));
                $margin = $sale->total_amount > 0 ? round($profit / $sale->total_amount * 100, 1) : 0;
                $sisa   = $sale->total_amount - $sale->amount_paid;

                fputcsv($f, [
                    $i + 1,
                    $sale->invoice_number,
                    $sale->created_at->format('d/m/Y'),
                    $sale->created_at->format('H:i'),
                    $sale->customer?->name ?? 'Umum',
                    $sale->payment_type === 'cash' ? 'Cash' : 'Tempo',
                    $sale->total_amount,
                    $modal,
                    $profit,
                    $margin . '%',
                    $sale->status === 'paid' ? 'Lunas' : ($sale->status === 'partial' ? 'Sebagian' : 'Belum Bayar'),
                    $sale->due_date?->format('d/m/Y') ?? '-',
                    $sale->amount_paid,
                    $sisa > 0 ? $sisa : 0,
                ]);
            }

            // ── Totals Row ───────────────────────────────
            fputcsv($f, []);
            fputcsv($f, [
                '',
                '',
                '',
                '',
                'TOTAL',
                '',
                $totalRevenue,
                $sales->sum(fn($s) => $s->details->sum(fn($d) => $d->quantity * ($d->product->modal_awal ?? 0))),
                $totalProfit,
                ($totalRevenue > 0 ? number_format($totalProfit / $totalRevenue * 100, 1) : '0') . '%',
                '',
                '',
                $sales->sum('amount_paid'),
                $totalUnpaid > 0 ? $totalUnpaid : 0,
            ]);

            fputcsv($f, []);
            if ($setting->invoice_footer) {
                fputcsv($f, [$setting->invoice_footer]);
            }

            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function stockExcel(Request $request)
    {
        $date    = $request->date ?? now()->format('Y-m-d');
        $setting = Setting::getSettings();

        $soldData = DB::table('sale_details')
            ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
            ->whereDate('sales.created_at', $date)
            ->select(
                'sale_details.product_id',
                DB::raw('SUM(sale_details.quantity) as total_qty'),
                DB::raw('SUM(sale_details.subtotal) as total_pendapatan')
            )
            ->groupBy('sale_details.product_id')
            ->get()
            ->keyBy('product_id');

        $products = Product::orderBy('nama_barang')->get()->map(function ($p) use ($soldData) {
            $sold              = $soldData->get($p->id);
            $p->terjual        = $sold ? (int)   $sold->total_qty       : 0;
            $p->pendapatan     = $sold ? (float)  $sold->total_pendapatan : 0;
            $p->stock_awal     = $p->kuantitas + $p->terjual;
            $p->keuntungan     = $p->pendapatan - ($p->terjual * (float) $p->modal_awal);
            return $p;
        });

        $label    = \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y');
        $filename = "stock-harian-{$date}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($products, $setting, $label, $date) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($f, ['LAPORAN STOCK HARIAN']);
            fputcsv($f, ['Toko',      $setting->company_name]);
            fputcsv($f, ['Tanggal',   $label]);
            fputcsv($f, ['Dicetak',   now()->format('d/m/Y H:i')]);
            fputcsv($f, []);

            $total_terjual    = $products->sum('terjual');
            $tidak_terjual    = $products->where('terjual', 0)->count();
            $total_pendapatan = $products->sum('pendapatan');
            $total_keuntungan = $products->sum('keuntungan');
            $low_stock        = $products->filter(fn($p) => $p->kuantitas <= $p->stock_minimum)->count();

            fputcsv($f, ['=== RINGKASAN ===']);
            fputcsv($f, ['Total Produk',          $products->count()]);
            fputcsv($f, ['Total Terjual (unit)',   $total_terjual]);
            fputcsv($f, ['Tidak Terjual (jenis)',  $tidak_terjual]);
            fputcsv($f, ['Total Pendapatan', 'Rp ' . number_format($total_pendapatan, 0, ',', '.')]);
            fputcsv($f, ['Total Keuntungan', 'Rp ' . number_format($total_keuntungan, 0, ',', '.')]);
            fputcsv($f, ['Stok Menipis',           $low_stock]);
            fputcsv($f, []);

            fputcsv($f, ['=== DETAIL STOCK ===']);
            fputcsv($f, [
                'No', 'Kode Barang', 'Nama Produk', 'Jenis',
                'Stock Awal', 'Terjual', 'Sisa Stock', 'Stock Minimum',
                'Harga Modal (Rp)', 'Harga Grosir (Rp)', 'Harga Ecer (Rp)',
                'Pendapatan (Rp)', 'Keuntungan (Rp)', 'Status',
            ]);

            foreach ($products as $i => $p) {
                $low    = $p->kuantitas <= $p->stock_minimum;
                $sold   = $p->terjual > 0;
                $status = match (true) {
                    $sold && $low  => 'Terjual · Low Stock',
                    $sold          => 'Terjual',
                    $low           => 'Stok Menipis',
                    default        => 'Tidak Terjual',
                };
                fputcsv($f, [
                    $i + 1,
                    $p->kode_barang,
                    $p->nama_barang,
                    $p->jenis_barang,
                    $p->stock_awal,
                    $p->terjual ?: '-',
                    $p->kuantitas,
                    $p->stock_minimum,
                    $p->modal_awal,
                    $p->harga_grosir,
                    $p->harga_ecer,
                    $p->pendapatan ?: '-',
                    $p->terjual > 0 ? $p->keuntungan : '-',
                    $status,
                ]);
            }

            fputcsv($f, []);
            fputcsv($f, [
                '', '', '', 'TOTAL',
                $products->sum('stock_awal'),
                $total_terjual,
                $products->sum('kuantitas'),
                '', '', '', '',
                $total_pendapatan,
                $total_keuntungan,
                '',
            ]);

            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getSales(Request $request)
    {
        $q = Sale::with(['customer', 'details.product'])->latest();
        if ($request->type === 'daily' && $request->date) {
            $q->whereDate('created_at', $request->date);
        } elseif ($request->type === 'monthly' && $request->month) {
            [$y, $m] = explode('-', $request->month);
            $q->whereYear('created_at', $y)->whereMonth('created_at', $m);
        } elseif ($request->type === 'yearly' && $request->year) {
            $q->whereYear('created_at', $request->year);
        }
        return $q->get();
    }

    private function getPeriodLabel(Request $request): string
    {
        if ($request->type === 'daily' && $request->date) {
            return $request->date;
        } elseif ($request->type === 'monthly' && $request->month) {
            return $request->month;
        } elseif ($request->type === 'yearly' && $request->year) {
            return $request->year;
        }
        return now()->format('Y-m-d');
    }

    private function getPeriodDisplay(Request $request): string
    {
        if ($request->type === 'daily' && $request->date) {
            return \Carbon\Carbon::parse($request->date)->isoFormat('D MMMM Y');
        } elseif ($request->type === 'monthly' && $request->month) {
            return \Carbon\Carbon::createFromFormat('Y-m', $request->month)->isoFormat('MMMM Y');
        } elseif ($request->type === 'yearly' && $request->year) {
            return 'Tahun ' . $request->year;
        }
        return now()->isoFormat('D MMMM Y');
    }
}
