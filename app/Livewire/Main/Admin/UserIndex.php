<?php

namespace App\Livewire\Main\Admin;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $role = '';
    public bool $showTrashed = false;

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRole()
    {
        $this->resetPage();
    }

    public function updatedShowTrashed()
    {
        $this->resetPage();
    }

    public function delete(int $userId): void
    {
        $user = User::findOrFail($userId);

        try {
            $this->authorize('delete', $user);
        } catch (AuthorizationException) {
            session()->flash('error', 'You cannot delete your own account.');

            return;
        }

        $user->delete();

        session()->flash('success', 'User deactivated.');
    }

    public function restore(int $userId): void
    {
        $user = User::onlyTrashed()->findOrFail($userId);
        $this->authorize('restore', $user);

        $user->restore();

        session()->flash('success', 'User restored.');
    }

    #[Title('Users')]
    public function render()
    {
        $users = User::query()

            ->when($this->showTrashed, fn ($query) => $query->onlyTrashed())

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
