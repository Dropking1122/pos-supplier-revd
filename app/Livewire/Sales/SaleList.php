<?php
namespace App\Livewire\Sales;
use App\Models\{Sale, Setting, Customer, Debt, DebtPayment};
use Livewire\Component;
use Livewire\WithPagination;
class SaleList extends Component {
    use WithPagination;
    public $search = '', $filterStatus = '', $filterDate = '', $filterCustomer = '';
    public $sortField = 'created_at', $sortDirection = 'desc';
    public $showDeleteModal = false, $deleteSaleId = null, $deleteSaleInvoice = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterDate() { $this->resetPage(); }
    public function updatingFilterCustomer() { $this->resetPage(); }

    public function sort($field) {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function confirmDelete($id) {
        $sale = Sale::findOrFail($id);
        $this->deleteSaleId      = $id;
        $this->deleteSaleInvoice = $sale->invoice_number;
        $this->showDeleteModal   = true;
    }

    public function deleteSale() {
        $sale = Sale::with(['details.product', 'debt.payments'])->findOrFail($this->deleteSaleId);

        // Restore stock for each item
        foreach ($sale->details as $detail) {
            if ($detail->product) {
                $detail->product->increment('kuantitas', $detail->quantity);
            }
        }

        // Remove debt payments then debt
        if ($sale->debt) {
            $sale->debt->payments()->delete();
            $sale->debt->delete();
        }

        $invoice = $sale->invoice_number;
        $sale->details()->delete();
        $sale->delete();

        $this->showDeleteModal = false;
        $this->deleteSaleId    = null;

        $this->dispatch('toast', type: 'success', title: 'Transaksi Dihapus', message: 'Invoice '.$invoice.' berhasil dihapus. Stok produk telah dikembalikan.');
    }

    public function render() {
        $customers = Customer::orderBy('name')->get();
        $sales = Sale::with('customer', 'user')
            ->when($this->search, fn($q)=>$q->where('invoice_number','like',"%{$this->search}%")->orWhereHas('customer',fn($q2)=>$q2->where('name','like',"%{$this->search}%")))
            ->when($this->filterStatus, fn($q)=>$q->where('status',$this->filterStatus))
            ->when($this->filterDate, fn($q)=>$q->whereDate('created_at',$this->filterDate))
            ->when($this->filterCustomer, fn($q)=>$q->where('customer_id',$this->filterCustomer))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);
        $setting = Setting::getSettings();
        return view('livewire.sales.sale-list', compact('sales','setting','customers'));
    }
}
