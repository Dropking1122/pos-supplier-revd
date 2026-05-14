<?php
namespace App\Livewire;
use App\Models\{Product, Sale, Debt, Setting, SaleDetail};
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component {
    public function render() {
        $setting  = Setting::getSettings();
        $isAdmin  = auth()->user()->is_admin;
        $userId   = auth()->id();

        $salesQuery = Sale::query();
        if (!$isAdmin) {
            $salesQuery->where('user_id', $userId);
        }

        $totalSales    = (clone $salesQuery)->sum('total_amount');
        $totalProducts = $isAdmin ? Product::count() : null;
        $totalProfit   = (clone $salesQuery)
            ->join('sale_details','sales.id','=','sale_details.sale_id')
            ->join('products','sale_details.product_id','=','products.id')
            ->selectRaw('SUM(sale_details.subtotal - (sale_details.quantity * products.modal_awal)) as profit')
            ->value('profit') ?? 0;
        $totalDebt = $isAdmin ? Debt::where('status','belum_lunas')->sum('sisa_hutang') : null;

        $recentSales = (clone $salesQuery)->with('customer')->latest()->take(5)->get();

        $topProducts = $isAdmin ? Product::select('products.*')
            ->join('sale_details','products.id','=','sale_details.product_id')
            ->selectRaw('products.*, SUM(sale_details.quantity) as total_terjual, SUM(sale_details.subtotal) as total_pendapatan')
            ->groupBy('products.id','products.kode_barang','products.nama_barang','products.jenis_barang','products.kuantitas','products.modal_awal','products.harga_grosir','products.harga_ecer','products.harga_satuan','products.stock_minimum','products.created_at','products.updated_at')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get() : collect();

        // Chart: penjualan 7 hari terakhir
        $last7Days = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));

        $salesByDateQ = Sale::selectRaw("DATE(created_at) as date, SUM(total_amount) as total")
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $userId))
            ->groupByRaw("DATE(created_at)")
            ->pluck('total', 'date');

        $profitByDateQ = Sale::join('sale_details','sales.id','=','sale_details.sale_id')
            ->join('products','sale_details.product_id','=','products.id')
            ->selectRaw("DATE(sales.created_at) as date, SUM(sale_details.subtotal - sale_details.quantity * products.modal_awal) as profit")
            ->where('sales.created_at', '>=', now()->subDays(6)->startOfDay())
            ->when(!$isAdmin, fn($q) => $q->where('sales.user_id', $userId))
            ->groupByRaw("DATE(sales.created_at)")
            ->pluck('profit', 'date');

        $chartLabels = $last7Days->map(fn($d) => \Carbon\Carbon::parse($d)->locale('id')->isoFormat('D MMM'))->values()->toArray();
        $chartSales  = $last7Days->map(fn($d) => (float) ($salesByDateQ[$d] ?? 0))->values()->toArray();
        $chartProfit = $last7Days->map(fn($d) => (float) ($profitByDateQ[$d] ?? 0))->values()->toArray();

        return view('livewire.dashboard', compact(
            'setting','totalSales','totalProducts','totalProfit',
            'totalDebt','recentSales','topProducts',
            'chartLabels','chartSales','chartProfit','isAdmin'
        ));
    }
}
