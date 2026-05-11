<?php
namespace App\Livewire\Debts;
use App\Models\{Debt, DebtPayment};
use Livewire\Component;
use Livewire\WithPagination;
class DebtList extends Component {
    use WithPagination;
    public $search = '', $filterStatus = '';
    public $showPayModal = false;
    public $payDebtId = null, $payAmount = 0, $payDate = '', $payNotes = '';
    public $sortField = 'created_at', $sortDirection = 'desc';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }

    public function sort($field) {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function openPay($debtId) {
        $debt = Debt::findOrFail($debtId);
        $this->payDebtId = $debtId;
        $this->payAmount = $debt->sisa_hutang;
        $this->payDate = now()->format('Y-m-d');
        $this->payNotes = '';
        $this->showPayModal = true;
    }
    public function savePayment() {
        $this->validate(['payAmount'=>'required|numeric|min:1','payDate'=>'required|date']);
        $debt = Debt::findOrFail($this->payDebtId);
        DebtPayment::create(['debt_id'=>$debt->id,'amount'=>$this->payAmount,'payment_date'=>$this->payDate,'notes'=>$this->payNotes]);
        $debt->total_bayar += $this->payAmount;
        $debt->sisa_hutang = max(0, $debt->total_hutang - $debt->total_bayar);
        $debt->status = $debt->sisa_hutang <= 0 ? 'lunas' : 'belum_lunas';
        $debt->save();
        if ($debt->sale_id) {
            $sale = $debt->sale;
            $sale->amount_paid = $debt->total_bayar;
            $sale->status = $debt->status === 'lunas' ? 'paid' : 'partial';
            $sale->save();
        }
        session()->flash('message','Pembayaran berhasil dicatat!');
        $this->showPayModal = false;
    }
    public function render() {
        $debts = Debt::with(['customer','sale'])
            ->when($this->search, fn($q)=>$q->whereHas('customer',fn($q2)=>$q2->where('name','like',"%{$this->search}%")))
            ->when($this->filterStatus, fn($q)=>$q->where('status',$this->filterStatus))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
        return view('livewire.debts.debt-list', compact('debts'));
    }
}
