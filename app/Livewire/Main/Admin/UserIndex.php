<?php

namespace App\Livewire\Main\Admin;

use App\Models\User;
use App\Services\UserService;
use App\Traits\TryAction;
use Livewire\Attributes\Title;
use Livewire\Component;

class UserIndex extends Component
{
    use TryAction;

    protected const PER_PAGE = 10;

    public string $search = '';

    public string $role = '';

    public bool $showTrashed = false;

    public int $perPage = self::PER_PAGE;

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    public function loadMore(): void
    {
        $this->perPage += self::PER_PAGE;
    }

    public function updatedSearch(): void
    {
        $this->perPage = self::PER_PAGE;
    }

    public function updatedRole(): void
    {
        $this->perPage = self::PER_PAGE;
    }

    public function updatedShowTrashed(): void
    {
        $this->perPage = self::PER_PAGE;
    }

    public function delete(int $userId, UserService $users): void
    {
        $this->tryAction(function () use ($users, $userId) {
            $users->deactivate(auth()->user(), $userId);

            $this->dispatch('toast', type: 'success', message: 'User deactivated.');
        }, 'Could not deactivate that user.');
    }

    public function restore(int $userId, UserService $users): void
    {
        $this->tryAction(function () use ($users, $userId) {
            $users->restore($userId);

            $this->dispatch('toast', type: 'success', message: 'User restored.');
        }, 'Could not restore that user.');
    }

    #[Title('Users')]
    public function render(UserService $users)
    {
        $query = $users->scopedUsers($this->search, $this->role, $this->showTrashed);

        $total = (clone $query)->count();
        $records = $query->limit($this->perPage)->get();

        return view('livewire.main.admin.user-index', [
            'users' => $records,
            'hasMore' => $total > $this->perPage,
        ]);
    }
}
