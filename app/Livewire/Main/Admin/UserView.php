<?php

namespace App\Livewire\Main\Admin;

use App\Models\User;
use Livewire\Component;

class UserView extends Component
{
    public User $user;
    public function mount(User $user)
    {
        $this->authorize('view', $user);

        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.main.admin.user-view')
            ->layout('layouts.app', [
                'title' => $this->user->name,
            ]);
    }
}
