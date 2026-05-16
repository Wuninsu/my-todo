<?php

namespace App\Livewire\Main\Admin;

use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
     use WithPagination;

    public string $search = '';
    public string $role = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRole()
    {
        $this->resetPage();
    }

   #[Title('Users')]
    public function render()
    {
        $users = User::query()

            ->when($this->search, function ($query) {

                $query->where(function ($q) {

                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");

                });
            })

            ->when($this->role, function ($query) {
                $query->where('role', $this->role);

            })

            ->latest()

            ->paginate(10);

        return view('livewire.main.admin.user-index', [
            'users' => $users,
        ]);
    }
}
