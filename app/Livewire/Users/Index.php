<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;
    public $deleteId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'CASHIER';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        'role' => 'required|in:ADMIN,CASHIER',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->reset(['editingId', 'name', 'email', 'password', 'role']);
        $this->role = 'CASHIER';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['editingId', 'name', 'email', 'password', 'role']);
    }

    public function edit($id)
    {
        $user = User::find($id);
        $this->editingId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->editingId) {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $this->editingId,
                'role' => 'required|in:ADMIN,CASHIER',
            ];
            if ($this->password) {
                $rules['password'] = 'required|string|min:6';
            }
            $this->validate($rules);

            $user = User::find($this->editingId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
            ]);
            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }
            $this->dispatch('notify', message: 'User updated successfully');
        } else {
            $this->validate();
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
            ]);
            $this->dispatch('notify', message: 'User created successfully');
        }

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function delete($id)
    {
        User::find($id)->delete();
        $this->dispatch('notify', message: 'User deleted successfully');
    }

    public function render()
    {
        $users = User::where('name', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%")
            ->paginate(10);

        return view('livewire.users.index', ['users' => $users])->layout('components.app-layout', ['title' => 'Users']);
    }
}
