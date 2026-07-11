<?php

use App\Livewire\Main\ThemeToggle;
use App\Models\User;
use Livewire\Livewire;

test('toggling theme flips and persists the users preference', function () {
    $user = User::factory()->create(['theme' => 'light']);

    Livewire::actingAs($user)
        ->test(ThemeToggle::class)
        ->assertSet('theme', 'light')
        ->call('toggle')
        ->assertSet('theme', 'dark');

    expect($user->fresh()->theme)->toBe('dark');

    Livewire::actingAs($user)
        ->test(ThemeToggle::class)
        ->call('toggle');

    expect($user->fresh()->theme)->toBe('light');
});
