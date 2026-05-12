<?php
namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $showDeleteModal = false;
    public $editId = null;
    public $deleteId = null;
    public $deleteName = '';

    public $name = '', $email = '', $password = '', $password_confirmation = '';

    public function updatingSearch() { $this->resetPage(); }

    protected function rules()
    {
        $passwordRule = $this->editId
            ? 'nullable|string|min:8|confirmed'
            : ['required', 'string', Password::min(8), 'confirmed'];

        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . ($this->editId ?? 'NULL'),
            'password' => $passwordRule,
        ];
    }

    public function openCreate()
    {
        $this->reset(['editId', 'name', 'email', 'password', 'password_confirmation']);
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $user = User::findOrFail($id);
        $this->editId   = $id;
        $this->name     = $user->name;
        $this->email    = $user->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editId) {
            $data = ['name' => $this->name, 'email' => $this->email];
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            User::findOrFail($this->editId)->update($data);
            $msg = 'User berhasil diperbarui.';
        } else {
            User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $msg = 'User baru berhasil ditambahkan.';
        }

        $this->showModal = false;
        $this->reset(['editId', 'name', 'email', 'password', 'password_confirmation']);
        $this->dispatch('toast', type: 'success', message: $msg);
    }

    public function confirmDelete($id)
    {
        if ($id === auth()->id()) {
            $this->dispatch('toast', type: 'error', message: 'Tidak bisa menghapus akun yang sedang digunakan.');
            return;
        }
        $user = User::findOrFail($id);
        $this->deleteId   = $id;
        $this->deleteName = $user->name;
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        if ($this->deleteId === auth()->id()) {
            $this->dispatch('toast', type: 'error', message: 'Tidak bisa menghapus akun yang sedang digunakan.');
            $this->showDeleteModal = false;
            return;
        }
        User::findOrFail($this->deleteId)->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->dispatch('toast', type: 'success', message: 'User ' . $this->deleteName . ' berhasil dihapus.');
        $this->deleteName = '';
    }

    public function render()
    {
        $users = User::when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->withCount('sales')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.users.user-index', compact('users'));
    }
}
