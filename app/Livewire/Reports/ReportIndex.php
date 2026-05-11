<?php
namespace App\Livewire\Reports;
use App\Models\{Sale, SaleDetail, Product};
use Livewire\Component;
class ReportIndex extends Component {
    public $filterType = 'daily', $filterDate = '', $filterMonth = '', $filterYear = '';
    public function mount() {
        $this->filterDate = now()->format('Y-m-d');
        $this->filterMonth = now()->format('Y-m');
        $this->filterYear = now()->format('Y');
    }
    public function render() {
        $salesQuery = Sale::with(['customer','details.product']);
        if ($this->filterType === 'daily' && $this->filterDate) {
            $salesQuery->whereDate('created_at', $this->filterDate);
        } elseif ($this->filterType === 'monthly' && $this->filterMonth) {
            [$y,$m] = explode('-', $this->filterMonth);
            $salesQuery->whereYear('created_at',$y)->whereMonth('created_at',$m);
        } elseif ($this->filterType === 'yearly' && $this->filterYear) {
            $salesQuery->whereYear('created_at',$this->filterYear);
        }
        $sales = $salesQuery->latest()->get();
        $totalRevenue = $sales->sum('total_amount');
        $totalProfit = $sales->sum(fn($s)=>$s->details->sum(fn($d)=>$d->subtotal - ($d->quantity * ($d->product->modal_awal ?? 0))));
        return view('livewire.reports.report-index', compact('sales','totalRevenue','totalProfit'));
    }
}
