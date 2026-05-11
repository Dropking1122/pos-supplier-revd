<?php
namespace App\Livewire\Sales;
use App\Models\{Sale, Setting};
use Livewire\Component;
use Livewire\WithPagination;
class SaleList extends Component {
    use WithPagination;
    public $search = '', $filterStatus = '', $filterDate = '';
    public function updatingSearch() { $this->resetPage(); }
    public function render() {
        $sales = Sale::with('customer')
            ->when($this->search, fn($q)=>$q->where('invoice_number','like',"%{$this->search}%")->orWhereHas('customer',fn($q2)=>$q2->where('name','like',"%{$this->search}%")))
            ->when($this->filterStatus, fn($q)=>$q->where('status',$this->filterStatus))
            ->when($this->filterDate, fn($q)=>$q->whereDate('created_at',$this->filterDate))
            ->latest()->paginate(10);
        $setting = Setting::getSettings();
        return view('livewire.sales.sale-list', compact('sales','setting'));
    }
}
