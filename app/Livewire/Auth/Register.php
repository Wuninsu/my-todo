<?php

namespace App\Livewire\Auth;

use App\Services\AuthService;
use App\Traits\TryAction;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
class Register extends Component
{
    use TryAction;

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

    public function register(AuthService $auth)
    {
        $validated = $this->validate();

        return $this->tryAction(function () use ($auth, $validated) {
            $user = $auth->register($validated);

            session()->regenerate();

            return redirect()->intended($auth->redirectPathFor($user));
        }, 'Something went wrong while creating your account. Please try again.');
    }

    #[Title('Register')]
    public function render()
    {
        return view('livewire.auth.register');
    }
}
