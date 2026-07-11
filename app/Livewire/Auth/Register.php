<?php

namespace App\Livewire\Auth;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

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
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ];
    }

    public function register()
    {
        $validated = $this->validate();

        $user = User::create([
            'uuid' => Str::uuid(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_synced' => false,
            'version' => 1,
            'client_updated_at' => now(),
            'device_uuid' => Str::uuid(),
        ]);

        TodoList::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'name' => 'My Tasks',
            'is_default' => true,
            'version' => 1,
            'client_updated_at' => now(),
        ]);

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->intended('/');
    }

    #[Title('Register')]
    public function render()
    {
        return view('livewire.auth.register');
    }
}
