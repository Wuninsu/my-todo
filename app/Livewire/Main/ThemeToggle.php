<?php

namespace App\Livewire\Main;

use App\Services\ProfileService;
use App\Traits\TryAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ThemeToggle extends Component
{
    use TryAction;

    public string $theme;

    public function mount(): void
    {
        $this->theme = Auth::user()->theme === 'dark' ? 'dark' : 'light';
    }

    public function toggle(ProfileService $profiles): void
    {
        $next = $this->theme === 'dark' ? 'light' : 'dark';

        $this->tryAction(function () use ($profiles, $next) {
            $profiles->setTheme(Auth::user(), $next);

            $this->theme = $next;

            $this->dispatch('theme-changed', theme: $this->theme);
        }, 'Could not change the theme.');
    }

    public function render()
    {
        return view('livewire.main.theme-toggle');
    }
}
