<?php
namespace App\Livewire;
use App\Models\{Product, Sale, Debt, Customer, Setting};
use Livewire\Component;
class Dashboard extends Component {
    public function render() {
        $setting = Setting::getSettings();
        $totalSales = Sale::sum('total_amount');
        $totalProducts = Product::count();
        $totalProfit = Sale::join('sale_details','sales.id','=','sale_details.sale_id')
            ->join('products','sale_details.product_id','=','products.id')
            ->selectRaw('SUM(sale_details.subtotal - (sale_details.quantity * products.modal_awal)) as profit')
            ->value('profit') ?? 0;
        $totalDebt = Debt::where('status','belum_lunas')->sum('sisa_hutang');
        $lowStockProducts = Product::whereColumn('kuantitas','<=','stock_minimum')->get();
        $monthlySales = Sale::selectRaw("MONTH(created_at) as month, SUM(total_amount) as total")
            ->whereYear('created_at', now()->year)
            ->groupByRaw("MONTH(created_at)")
            ->orderByRaw("MONTH(created_at)")
            ->pluck('total', 'month');
        $recentSales = Sale::with('customer')->latest()->take(5)->get();
        return view('livewire.dashboard', compact('setting','totalSales','totalProducts','totalProfit','totalDebt','lowStockProducts','monthlySales','recentSales'));
    }
}
