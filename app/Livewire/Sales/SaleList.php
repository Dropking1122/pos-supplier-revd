<?php
namespace App\Livewire\Sales;
use App\Models\{Sale, Setting, Customer, Debt, DebtPayment};
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
class SaleList extends Component {
    use WithPagination;
    public $search = '', $filterStatus = '', $filterDate = '', $filterCustomer = '', $filterKasir = '';
    public $sortField = 'created_at', $sortDirection = 'desc';
    public $showDeleteModal = false, $deleteSaleId = null, $deleteSaleInvoice = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterDate() { $this->resetPage(); }
    public function updatingFilterCustomer() { $this->resetPage(); }
    public function updatingFilterKasir() { $this->resetPage(); }

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
        if (!auth()->user()->is_admin && $sale->user_id !== auth()->id()) {
            $this->dispatch('toast', type: 'error', message: 'Anda tidak bisa menghapus transaksi milik kasir lain.');
            return;
        }
        $this->deleteSaleId      = $id;
        $this->deleteSaleInvoice = $sale->invoice_number;
        $this->showDeleteModal   = true;
    }

    public function deleteSale() {
        $sale = Sale::with(['details.product', 'debt.payments'])->findOrFail($this->deleteSaleId);

        if (!auth()->user()->is_admin && $sale->user_id !== auth()->id()) {
            $this->dispatch('toast', type: 'error', message: 'Anda tidak bisa menghapus transaksi milik kasir lain.');
            $this->showDeleteModal = false;
            return;
        }

        foreach ($sale->details as $detail) {
            if ($detail->product) {
                $detail->product->increment('kuantitas', $detail->quantity);
            }
        }

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
        $isAdmin   = auth()->user()->is_admin;
        $customers = Customer::orderBy('name')->get();
        $kasirList = $isAdmin ? User::orderBy('name')->get() : collect();

        $sales = Sale::with('customer', 'user')
            ->when(!$isAdmin, fn($q) => $q->where('user_id', auth()->id()))
            ->when($this->search, fn($q)=>$q->where('invoice_number','like',"%{$this->search}%")->orWhereHas('customer',fn($q2)=>$q2->where('name','like',"%{$this->search}%")))
            ->when($this->filterStatus, fn($q)=>$q->where('status',$this->filterStatus))
            ->when($this->filterDate, fn($q)=>$q->whereDate('created_at',$this->filterDate))
            ->when($this->filterCustomer, fn($q)=>$q->where('customer_id',$this->filterCustomer))
            ->when($isAdmin && $this->filterKasir, fn($q)=>$q->where('user_id',$this->filterKasir))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        $setting = Setting::getSettings();
        return view('livewire.sales.sale-list', compact('sales','setting','customers','isAdmin','kasirList'));
    }
}
