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

    protected array $allowedSortFields = ['created_at', 'total_hutang', 'sisa_hutang', 'jatuh_tempo', 'status'];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }

    public function sort($field) {
        if (!in_array($field, $this->allowedSortFields, true)) return;
        $this->sortDirection = ($this->sortField === $field && $this->sortDirection === 'asc') ? 'desc' : 'asc';
        $this->sortField = $field;
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
        $debt = Debt::findOrFail($this->payDebtId);
        $this->validate([
            'payAmount' => ['required','numeric','min:1'],
            'payDate'   => 'required|date',
        ], [
            'payAmount.min' => 'Jumlah bayar minimal Rp 1.',
        ]);
        $actualAmount = min((float) $this->payAmount, $debt->sisa_hutang);
        DebtPayment::create(['debt_id'=>$debt->id,'amount'=>$actualAmount,'payment_date'=>$this->payDate,'notes'=>$this->payNotes]);
        $debt->total_bayar += $actualAmount;
        $debt->sisa_hutang = max(0, $debt->total_hutang - $debt->total_bayar);
        $debt->status = $debt->sisa_hutang <= 0 ? 'lunas' : 'belum_lunas';
        $debt->save();
        if ($debt->sale_id) {
            $sale = $debt->sale;
            $sale->amount_paid = $debt->total_bayar;
            $sale->status = $debt->status === 'lunas' ? 'paid' : 'partial';
            $sale->save();
        }
        $this->dispatch('toast', type: 'success', title: 'Pembayaran Dicatat', message: 'Pembayaran Rp '.number_format($this->payAmount,0,',','.').' berhasil dicatat.');
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
