<?php
namespace App\Livewire;
use App\Models\{Product, Sale, Debt, Customer, Setting, SaleDetail};
use Livewire\Component;
class Dashboard extends Component {
    public function render() {
        $setting     = Setting::getSettings();
        $totalSales  = Sale::sum('total_amount');
        $totalProducts = Product::count();
        $totalProfit = Sale::join('sale_details','sales.id','=','sale_details.sale_id')
            ->join('products','sale_details.product_id','=','products.id')
            ->selectRaw('SUM(sale_details.subtotal - (sale_details.quantity * products.modal_awal)) as profit')
            ->value('profit') ?? 0;
        $totalDebt   = Debt::where('status','belum_lunas')->sum('sisa_hutang');
        $lowStockProducts = Product::whereColumn('kuantitas','<=','stock_minimum')->get();
        $monthlySales = Sale::selectRaw("MONTH(created_at) as month, SUM(total_amount) as total")
            ->whereYear('created_at', now()->year)
            ->groupByRaw("MONTH(created_at)")
            ->orderByRaw("MONTH(created_at)")
            ->pluck('total', 'month');
        $recentSales = Sale::with('customer')->latest()->take(5)->get();
        $topProducts = Product::select('products.*')
            ->join('sale_details','products.id','=','sale_details.product_id')
            ->selectRaw('products.*, SUM(sale_details.quantity) as total_terjual, SUM(sale_details.subtotal) as total_pendapatan')
            ->groupBy('products.id','products.kode_barang','products.nama_barang','products.jenis_barang','products.kuantitas','products.modal_awal','products.harga_grosir','products.harga_ecer','products.harga_satuan','products.stock_minimum','products.created_at','products.updated_at')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();
        return view('livewire.dashboard', compact(
            'setting','totalSales','totalProducts','totalProfit',
            'totalDebt','lowStockProducts','monthlySales','recentSales','topProducts'
        ));
    }
}
