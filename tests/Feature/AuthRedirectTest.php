<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Livewire\Livewire;

test('guests visiting the root see the marketing page', function () {
    $this->get('/')->assertOk()->assertViewIs('welcome');
});

test('an authenticated admin visiting the root is redirected to the admin overview', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get('/')->assertRedirect(route('admin.dashboard'));
});

test('an authenticated regular user visiting the root is redirected to the dashboard', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->get('/')->assertRedirect(route('dashboard'));
});

test('an admin logging in lands on the admin overview', function () {
    $admin = User::factory()->create(['role' => 'admin', 'password' => 'password']);

    Livewire::test(Login::class)
        ->set('email', $admin->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('admin.dashboard'));
});

test('a regular user logging in lands on the dashboard', function () {
    $user = User::factory()->create(['role' => 'user', 'password' => 'password']);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard'));
});
