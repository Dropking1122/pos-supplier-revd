<?php
namespace App\Livewire\Customers;
use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;
class CustomerList extends Component {
    use WithPagination;
    public $search = '';
    public $showModal = false;
    public $editId = null;
    public $name = '', $phone = '', $address = '';
    protected $rules = ['name'=>'required|string|max:255','phone'=>'nullable|string|max:50','address'=>'nullable|string'];
    public function updatingSearch() { $this->resetPage(); }
    public function openCreate() { $this->reset(['editId','name','phone','address']); $this->showModal = true; }
    public function openEdit($id) {
        $c = Customer::findOrFail($id);
        $this->editId = $id; $this->name = $c->name; $this->phone = $c->phone; $this->address = $c->address;
        $this->showModal = true;
    }
    public function save() {
        $this->validate();
        if ($this->editId) {
            Customer::findOrFail($this->editId)->update($this->only(['name','phone','address']));
            session()->flash('message','Customer berhasil diupdate!');
        } else {
            Customer::create($this->only(['name','phone','address']));
            session()->flash('message','Customer berhasil ditambahkan!');
        }
        $this->showModal = false;
    }
    public function delete($id) { Customer::findOrFail($id)->delete(); session()->flash('message','Customer dihapus!'); }
    public function render() {
        $customers = Customer::where('name','like',"%{$this->search}%")->orWhere('phone','like',"%{$this->search}%")->paginate(10);
        return view('livewire.customers.customer-list', compact('customers'));
    }
}
