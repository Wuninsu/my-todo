<?php

namespace App\Livewire\Main;

use Livewire\Attributes\Title;
use Livewire\Component;

class ProfileIndex extends Component
{
    #[Title('My Profile')]
    public function render()
    {
        return view('livewire.main.profile-index');
    }
}
