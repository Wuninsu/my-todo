<?php

namespace App\Livewire\Auth;

use App\Services\AuthService;
use App\Traits\TryAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
class Login extends Component
{
    use TryAction;

    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    /*
    |--------------------------------------------------------------------------
    | RULES
    |--------------------------------------------------------------------------
    */

    protected function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'min:6',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(AuthService $auth)
    {
        $validated = $this->validate();

        return $this->tryAction(function () use ($auth, $validated) {
            if (! $auth->attempt([
                'email' => $validated['email'],
                'password' => $validated['password'],
            ], $this->remember)) {

                $this->addError(
                    'email',
                    'Invalid login credentials.'
                );

                return null;
            }

            session()->regenerate();

            return redirect()->intended($auth->redirectPathFor(Auth::user()));
        }, 'Something went wrong while signing in. Please try again.');
    }

    #[Title('Login')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
