<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
class Login extends Component
{
    
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

    public function login()
    {
        $validated =
            $this->validate();

        if (! Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ], $this->remember)) {

            $this->addError(
                'email',
                'Invalid login credentials.'
            );

            return;
        }

        request()->session()->regenerate();

        return redirect()->intended('/');
    }

   
    #[Title('Login')]
    public function render()
    {
        return view('livewire.auth.login');
    }

}
