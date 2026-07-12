<?php

namespace App\Livewire\Main\Admin;

use App\Models\User;
use App\Services\UserService;
use App\Traits\TryAction;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

class UserEdit extends Component
{
    use TryAction;

    public User $user;

    public string $name = '';

    public string $email = '';

    public string $role = 'user';

    public string $theme = 'light';

    public string $timezone = 'UTC';

    public function mount(User $user)
    {
        $this->authorize('update', $user);

        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->theme = $user->theme;
        $this->timezone = $user->timezone;
    }

    protected function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id), ],

            'role' => [
                'required',
                Rule::in(['admin', 'user']),
            ],

            'theme' => [
                'required',
                Rule::in(['light', 'dark']),
            ],

            'timezone' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }

    public function update(UserService $users)
    {
        $validated = $this->validate();

        return $this->tryAction(function () use ($users, $validated) {
            $users->update($this->user, $validated);

            session()->flash('toast', ['type' => 'success', 'message' => 'User updated successfully.']);

            return redirect()->route('admin.users.view', $this->user);
        }, 'Could not update that user.');
    }

    #[Title('Edit User')]
    public function render()
    {
        return view('livewire.main.admin.user-edit');
    }
}
