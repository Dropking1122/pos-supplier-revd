<?php
namespace App\Livewire\Sales;
use App\Models\{Sale, Setting};
use Livewire\Component;
use Livewire\WithPagination;
class SaleList extends Component {
    use WithPagination;
    public $search = '', $filterStatus = '', $filterDate = '';
    public $sortField = 'created_at', $sortDirection = 'desc';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterDate() { $this->resetPage(); }

    public function sort($field) {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function render() {
        $sales = Sale::with('customer')
            ->when($this->search, fn($q)=>$q->where('invoice_number','like',"%{$this->search}%")->orWhereHas('customer',fn($q2)=>$q2->where('name','like',"%{$this->search}%")))
            ->when($this->filterStatus, fn($q)=>$q->where('status',$this->filterStatus))
            ->when($this->filterDate, fn($q)=>$q->whereDate('created_at',$this->filterDate))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
        $setting = Setting::getSettings();
        return view('livewire.sales.sale-list', compact('sales','setting'));
    }
}
