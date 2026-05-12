<?php
namespace App\Livewire\Reports;

use App\Models\{Sale, Product};
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReportIndex extends Component
{
    public string $activeTab    = 'sales';
    public string $filterType   = 'daily';
    public string $filterDate   = '';
    public string $filterMonth  = '';
    public string $filterYear   = '';
    public string $stockDate    = '';
    public string $stockSearch  = '';

    public function mount(): void
    {
        $this->filterDate  = now()->format('Y-m-d');
        $this->filterMonth = now()->format('Y-m');
        $this->filterYear  = now()->format('Y');
        $this->stockDate   = now()->format('Y-m-d');
    }

    public function render()
    {
        // ── Sales tab ────────────────────────────────────────────────────
        $salesQuery = Sale::with(['customer', 'details.product']);
        if ($this->filterType === 'daily' && $this->filterDate) {
            $salesQuery->whereDate('created_at', $this->filterDate);
        } elseif ($this->filterType === 'monthly' && $this->filterMonth) {
            [$y, $m] = explode('-', $this->filterMonth);
            $salesQuery->whereYear('created_at', $y)->whereMonth('created_at', $m);
        } elseif ($this->filterType === 'yearly' && $this->filterYear) {
            $salesQuery->whereYear('created_at', $this->filterYear);
        }
        $sales        = $salesQuery->latest()->get();
        $totalRevenue = $sales->sum('total_amount');
        $totalProfit  = $sales->sum(
            fn($s) => $s->details->sum(fn($d) => $d->subtotal - ($d->quantity * ($d->product->modal_awal ?? 0)))
        );

        // ── Stock Harian tab ─────────────────────────────────────────────
        // Aggregate sold qty & revenue per product on chosen date
        $soldData = DB::table('sale_details')
            ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
            ->whereDate('sales.created_at', $this->stockDate)
            ->select(
                'sale_details.product_id',
                DB::raw('SUM(sale_details.quantity) as total_qty'),
                DB::raw('SUM(sale_details.subtotal) as total_pendapatan')
            )
            ->groupBy('sale_details.product_id')
            ->get()
            ->keyBy('product_id');

        $productQuery = Product::query()->orderBy('nama_barang');
        if ($this->stockSearch !== '') {
            $productQuery->where(function ($q) {
                $q->where('nama_barang', 'like', '%' . $this->stockSearch . '%')
                  ->orWhere('kode_barang', 'like', '%' . $this->stockSearch . '%');
            });
        }
        $products = $productQuery->get()->map(function ($p) use ($soldData) {
            $sold              = $soldData->get($p->id);
            $p->terjual        = $sold ? (int) $sold->total_qty : 0;
            $p->pendapatan     = $sold ? (float) $sold->total_pendapatan : 0;
            $p->stock_awal     = $p->kuantitas + $p->terjual;
            $p->keuntungan     = ($p->pendapatan) - ($p->terjual * (float) $p->modal_awal);
            return $p;
        });

        $stockSummary = [
            'total_produk'     => $products->count(),
            'total_terjual'    => $products->sum('terjual'),
            'tidak_terjual'    => $products->where('terjual', 0)->count(),
            'total_pendapatan' => $products->sum('pendapatan'),
            'total_keuntungan' => $products->sum('keuntungan'),
            'low_stock'        => $products->filter(fn($p) => $p->kuantitas <= $p->stock_minimum)->count(),
        ];

        return view('livewire.reports.report-index', compact(
            'sales', 'totalRevenue', 'totalProfit',
            'products', 'stockSummary'
        ));
    }
}
