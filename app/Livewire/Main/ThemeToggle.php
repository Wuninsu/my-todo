<?php

namespace App\Livewire\Main;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ThemeToggle extends Component
{
    public string $theme;

    public function mount(): void
    {
        $this->theme = Auth::user()->theme === 'dark' ? 'dark' : 'light';
    }

    public function toggle(): void
    {
        $this->theme = $this->theme === 'dark' ? 'light' : 'dark';

        $user = Auth::user();
        $user->update([
            'theme' => $this->theme,
            'version' => $user->version + 1,
            'client_updated_at' => now(),
        ]);

        $this->dispatch('theme-changed', theme: $this->theme);
    }

    public function render()
    {
        return view('livewire.main.theme-toggle');
    }
}
