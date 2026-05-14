<?php
namespace App\Livewire;
use App\Models\{Product, Sale, Debt, Setting, SaleDetail};
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component {
    public function render() {
        $setting       = Setting::getSettings();
        $totalSales    = Sale::sum('total_amount');
        $totalProducts = Product::count();
        $totalProfit   = Sale::join('sale_details','sales.id','=','sale_details.sale_id')
            ->join('products','sale_details.product_id','=','products.id')
            ->selectRaw('SUM(sale_details.subtotal - (sale_details.quantity * products.modal_awal)) as profit')
            ->value('profit') ?? 0;
        $totalDebt     = Debt::where('status','belum_lunas')->sum('sisa_hutang');

        $recentSales   = Sale::with('customer')->latest()->take(5)->get();

        $topProducts   = Product::select('products.*')
            ->join('sale_details','products.id','=','sale_details.product_id')
            ->selectRaw('products.*, SUM(sale_details.quantity) as total_terjual, SUM(sale_details.subtotal) as total_pendapatan')
            ->groupBy('products.id','products.kode_barang','products.nama_barang','products.jenis_barang','products.kuantitas','products.modal_awal','products.harga_grosir','products.harga_ecer','products.harga_satuan','products.stock_minimum','products.created_at','products.updated_at')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        // Chart: penjualan 7 hari terakhir
        $last7Days  = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));
        $salesByDate = Sale::selectRaw("DATE(created_at) as date, SUM(total_amount) as total")
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupByRaw("DATE(created_at)")
            ->pluck('total', 'date');
        $profitByDate = Sale::join('sale_details','sales.id','=','sale_details.sale_id')
            ->join('products','sale_details.product_id','=','products.id')
            ->selectRaw("DATE(sales.created_at) as date, SUM(sale_details.subtotal - sale_details.quantity * products.modal_awal) as profit")
            ->where('sales.created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupByRaw("DATE(sales.created_at)")
            ->pluck('profit', 'date');

        $chartLabels  = $last7Days->map(fn($d) => \Carbon\Carbon::parse($d)->locale('id')->isoFormat('D MMM'))->values()->toArray();
        $chartSales   = $last7Days->map(fn($d) => (float) ($salesByDate[$d] ?? 0))->values()->toArray();
        $chartProfit  = $last7Days->map(fn($d) => (float) ($profitByDate[$d] ?? 0))->values()->toArray();

        return view('livewire.dashboard', compact(
            'setting','totalSales','totalProducts','totalProfit',
            'totalDebt','recentSales','topProducts',
            'chartLabels','chartSales','chartProfit'
        ));
    }
}
