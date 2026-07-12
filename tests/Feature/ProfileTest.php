<?php

use App\Livewire\Main\ProfileIndex;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get('/profile')->assertRedirect('/login');
});

test('authenticated users can view their profile', function () {
    $user = User::factory()->create(['name' => 'Jane Doe']);

    $this->actingAs($user)->get('/profile')->assertOk()->assertSee('Jane Doe');
});

test('a user can update their profile details', function () {
    $user = User::factory()->create(['name' => 'Old Name', 'timezone' => 'UTC', 'theme' => 'light']);

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->call('startEditing')
        ->set('name', 'New Name')
        ->set('email', $user->email)
        ->set('timezone', 'America/New_York')
        ->set('theme', 'dark')
        ->call('save')
        ->assertDispatched('toast', type: 'success', message: 'Profile updated.');

    $user->refresh();

    expect($user->name)->toBe('New Name');
    expect($user->timezone)->toBe('America/New_York');
    expect($user->theme)->toBe('dark');
});

test('a user cannot take another users email', function () {
    $taken = User::factory()->create(['email' => 'taken@example.com']);
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->call('startEditing')
        ->set('name', $user->name)
        ->set('email', 'taken@example.com')
        ->set('timezone', $user->timezone)
        ->set('theme', $user->theme)
        ->call('save')
        ->assertHasErrors(['email']);

    expect($user->fresh()->email)->not->toBe('taken@example.com');
});

test('a user can upload an avatar', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('avatar.jpg');

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->call('startEditing')
        ->set('name', $user->name)
        ->set('email', $user->email)
        ->set('timezone', $user->timezone)
        ->set('theme', $user->theme)
        ->set('avatar', $file)
        ->call('save');

    $user->refresh();

    expect($user->avatar)->not->toBeNull();
    Storage::disk('public')->assertExists($user->avatar);
});

test('a user can change their password with the correct current password', function () {
    $user = User::factory()->create(['password' => 'old-password']);

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('current_password', 'old-password')
        ->set('password', 'brand-new-password')
        ->set('password_confirmation', 'brand-new-password')
        ->call('changePassword')
        ->assertHasNoErrors()
        ->assertDispatched('toast', type: 'success', message: 'Password updated.');

    expect(Hash::check('brand-new-password', $user->fresh()->password))->toBeTrue();
});

test('changing password fails with the wrong current password', function () {
    $user = User::factory()->create(['password' => 'old-password']);

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('current_password', 'wrong-password')
        ->set('password', 'brand-new-password')
        ->set('password_confirmation', 'brand-new-password')
        ->call('changePassword')
        ->assertHasErrors(['current_password']);

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});

test('a user can revoke their own device', function () {
    $user = User::factory()->create();
    $device = Device::factory()->for($user)->create();

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->call('revokeDevice', $device->id)
        ->assertDispatched('toast', type: 'success', message: 'Device revoked.');

    expect(Device::find($device->id))->toBeNull();
});

test('a user cannot revoke another users device', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $device = Device::factory()->for($other)->create();

    Livewire::actingAs($user)->test(ProfileIndex::class)->call('revokeDevice', $device->id);

    expect(Device::find($device->id))->not->toBeNull();
});

test('the profile page renders no browser-native confirm dialog', function () {
    $user = User::factory()->create();
    Device::factory()->for($user)->create();

    $response = $this->actingAs($user)->get('/profile');

    $response->assertOk();
    $response->assertDontSee('wire:confirm', false);
});
