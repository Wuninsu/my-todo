<?php

use App\Livewire\Main\Admin\Overview;
use App\Livewire\Main\Admin\UserEdit;
use App\Livewire\Main\Admin\UserIndex;
use App\Models\SyncLog;
use App\Models\Todo;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login for admin routes', function () {
    $this->get('/admin')->assertRedirect('/login');
    $this->get('/admin/users')->assertRedirect('/login');
});

test('non-admin users are forbidden from admin routes', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->get('/admin')->assertForbidden();
    $this->actingAs($user)->get('/admin/users')->assertForbidden();
});

test('admins can reach the overview and users pages', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get('/admin')->assertOk();
    $this->actingAs($admin)->get('/admin/users')->assertOk();
});

test('the overview shows real counts', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $other = User::factory()->create(['role' => 'user']);
    Todo::factory()->for($other)->create(['status' => 'done']);
    Todo::factory()->for($other)->create(['status' => 'todo']);
    SyncLog::create(['user_id' => $other->id, 'status' => 'success', 'uploaded' => 3, 'downloaded' => 1, 'conflicts' => 0]);

    Livewire::actingAs($admin)
        ->test(Overview::class)
        ->assertViewHas('totalUsers', 2)
        ->assertViewHas('totalAdmins', 1)
        ->assertViewHas('totalTodos', 2)
        ->assertViewHas('completedTodos', 1);
});

test('an admin cannot delete their own account', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(UserIndex::class)
        ->call('delete', $admin->id)
        ->assertDispatched('toast', type: 'error', message: 'You cannot delete your own account.');

    expect($admin->fresh()->trashed())->toBeFalse();
});

test('a regular user can be deactivated and restored', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $target = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($admin)
        ->test(UserIndex::class)
        ->call('delete', $target->id)
        ->assertDispatched('toast', type: 'success', message: 'User deactivated.');
    expect($target->fresh()->trashed())->toBeTrue();

    Livewire::actingAs($admin)
        ->test(UserIndex::class)
        ->call('restore', $target->id)
        ->assertDispatched('toast', type: 'success', message: 'User restored.');
    expect($target->fresh()->trashed())->toBeFalse();
});

test('the users page renders no browser-native confirm dialog', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/admin/users');

    $response->assertOk();
    $response->assertDontSee('wire:confirm', false);
});

test('updating a user as admin flashes a toast that survives the redirect', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $target = User::factory()->create(['role' => 'user']);

    Livewire::actingAs($admin)
        ->test(UserEdit::class, ['user' => $target])
        ->set('name', 'Updated Name')
        ->call('update')
        ->assertRedirect(route('admin.users.view', $target));

    expect(session('toast'))->toBe(['type' => 'success', 'message' => 'User updated successfully.']);
});

test('the users list shows a load more button instead of pagination links', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->count(12)->create(['role' => 'user']);

    $component = Livewire::actingAs($admin)->test(UserIndex::class);

    $component->assertViewHas('users', fn ($users) => $users->count() === 10);
    $component->assertViewHas('hasMore', true);
    $component->assertDontSee('wire:confirm', false);

    $component->call('loadMore');

    $component->assertViewHas('users', fn ($users) => $users->count() === 13);
    $component->assertViewHas('hasMore', false);
    $component->assertDontSee('Load More');
});

test('changing the search filter resets the load more count', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->count(12)->create(['role' => 'user']);

    Livewire::actingAs($admin)
        ->test(UserIndex::class)
        ->call('loadMore')
        ->assertViewHas('users', fn ($users) => $users->count() === 13)
        ->set('search', 'nobody-matches-this')
        ->assertViewHas('users', fn ($users) => $users->count() === 0);
});

test('deactivated users are hidden by default and shown with the trashed toggle', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $target = User::factory()->create(['role' => 'user', 'name' => 'Ghost User']);
    $target->delete();

    Livewire::actingAs($admin)
        ->test(UserIndex::class)
        ->assertDontSee('Ghost User')
        ->set('showTrashed', true)
        ->assertSee('Ghost User');
});
