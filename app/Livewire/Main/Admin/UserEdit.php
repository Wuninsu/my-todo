<?php

namespace App\Livewire\Main\Admin;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

class UserEdit extends Component
{

    public User $user;
    public string $name = '';
    public string $email = '';

    public string $role = 'user';
    public string $theme = 'light';
    public string $timezone = 'UTC';

  
    public function mount(User $user)
    {
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
                Rule::unique('users', 'email')->ignore($this->user->id),],

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

   
    public function update()
    {
        $validated =
            $this->validate();

        $this->user->update([

            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'theme' => $validated['theme'],
            'timezone' => $validated['timezone'],
            'is_synced' => false,
            'version' => $this->user->version + 1,
            'client_updated_at' => now(),
        ]);

        session()->flash('success','User updated successfully.');
        return redirect()->route('admin.users.view',$this->user);
    }

    #[Title('Edit User')]
    public function render()
    {
        return view('livewire.main.admin.user-edit');
    }
}
