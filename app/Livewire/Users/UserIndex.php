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

    public $name = '', $email = '', $password = '', $password_confirmation = '', $is_admin = false;

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
            'is_admin' => 'boolean',
        ];
    }

    private function requireAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->is_admin, 403, 'Akses ditolak.');
    }

    public function openCreate()
    {
        $this->requireAdmin();
        $this->reset(['editId', 'name', 'email', 'password', 'password_confirmation', 'is_admin']);
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $this->requireAdmin();
        $user = User::findOrFail($id);
        $this->editId   = $id;
        $this->name     = $user->name;
        $this->email    = $user->email;
        $this->is_admin = (bool) $user->is_admin;
        $this->password = '';
        $this->password_confirmation = '';
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save()
    {
        $this->requireAdmin();
        $this->validate();

        if ($this->editId) {
            $data = [
                'name'     => $this->name,
                'email'    => $this->email,
                'is_admin' => (bool) $this->is_admin,
            ];
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            // Jangan cabut admin dari diri sendiri
            if ($this->editId === auth()->id()) {
                $data['is_admin'] = true;
            }
            User::findOrFail($this->editId)->update($data);
            $msg = 'User berhasil diperbarui.';
        } else {
            User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => Hash::make($this->password),
                'is_admin' => (bool) $this->is_admin,
            ]);
            $msg = 'User baru berhasil ditambahkan.';
        }

        $this->showModal = false;
        $this->reset(['editId', 'name', 'email', 'password', 'password_confirmation', 'is_admin']);
        $this->dispatch('toast', type: 'success', message: $msg);
    }

    public function confirmDelete($id)
    {
        $this->requireAdmin();
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
        $this->requireAdmin();
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
            ->orderBy('is_admin', 'desc')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.users.user-index', compact('users'));
    }
}
