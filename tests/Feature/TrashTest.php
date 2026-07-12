<?php

use App\Livewire\Main\Trash;
use App\Models\Device;
use App\Models\Tag;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get('/trash')->assertRedirect('/login');
});

test('the trash route renders the full layout for an authenticated user', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create(['title' => 'Route smoke test todo']);
    $todo->delete();

    $response = $this->actingAs($user)->get('/trash');

    $response->assertOk();
    $response->assertSee('TodoFlow');
    $response->assertSee('Route smoke test todo');
});

test('trashed todos, lists, tags, and devices from other models all appear together', function () {
    $user = User::factory()->create();

    $todo = Todo::factory()->for($user)->create(['title' => 'Deleted Todo']);
    $todo->delete();

    $list = TodoList::factory()->for($user)->create(['name' => 'Deleted List']);
    $list->delete();

    $tag = Tag::factory()->for($user)->create(['name' => 'deleted-tag']);
    $tag->delete();

    $device = Device::factory()->for($user)->create(['device_name' => 'Deleted Device']);
    $device->delete();

    Livewire::actingAs($user)
        ->test(Trash::class)
        ->assertSee('Deleted Todo')
        ->assertSee('Deleted List')
        ->assertSee('deleted-tag')
        ->assertSee('Deleted Device');
});

test('the model filter narrows the list to a single type', function () {
    $user = User::factory()->create();

    $todo = Todo::factory()->for($user)->create(['title' => 'Deleted Todo']);
    $todo->delete();

    $list = TodoList::factory()->for($user)->create(['name' => 'Deleted List']);
    $list->delete();

    Livewire::actingAs($user)
        ->test(Trash::class)
        ->set('model', 'todos')
        ->assertSee('Deleted Todo')
        ->assertDontSee('Deleted List');
});

test('a todo can be restored from the trash page', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create();
    $todo->delete();

    Livewire::actingAs($user)
        ->test(Trash::class)
        ->call('restore', 'todos', $todo->id)
        ->assertDispatched('toast', type: 'success', message: 'Todo restored.');

    expect($todo->fresh()->trashed())->toBeFalse();
});

test('a list can be restored from the trash page', function () {
    $user = User::factory()->create();
    $list = TodoList::factory()->for($user)->create();
    $list->delete();

    Livewire::actingAs($user)
        ->test(Trash::class)
        ->call('restore', 'lists', $list->id)
        ->assertDispatched('toast', type: 'success', message: 'List restored.');

    expect($list->fresh()->trashed())->toBeFalse();
});

test('a tag can be restored from the trash page', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->for($user)->create();
    $tag->delete();

    Livewire::actingAs($user)
        ->test(Trash::class)
        ->call('restore', 'tags', $tag->id)
        ->assertDispatched('toast', type: 'success', message: 'Tag restored.');

    expect($tag->fresh()->trashed())->toBeFalse();
});

test('a device can be restored from the trash page', function () {
    $user = User::factory()->create();
    $device = Device::factory()->for($user)->create();
    $device->delete();

    Livewire::actingAs($user)
        ->test(Trash::class)
        ->call('restore', 'devices', $device->id)
        ->assertDispatched('toast', type: 'success', message: 'Device restored.');

    expect($device->fresh()->trashed())->toBeFalse();
});

test('force-delete is only reachable through the confirmed event, exercising the listener the way the dialog does', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create();
    $todo->delete();

    Livewire::actingAs($user)
        ->test(Trash::class)
        ->dispatch('trash-force-delete-confirmed', type: 'todos', id: $todo->id)
        ->assertDispatched('toast', type: 'success', message: 'Todo permanently deleted.');

    expect(Todo::withTrashed()->find($todo->id))->toBeNull();
});

test('a user cannot restore or force-delete another users trashed records', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $todo = Todo::factory()->for($other)->create();
    $todo->delete();
    $list = TodoList::factory()->for($other)->create();
    $list->delete();
    $tag = Tag::factory()->for($other)->create();
    $tag->delete();
    $device = Device::factory()->for($other)->create();
    $device->delete();

    Livewire::actingAs($user)->test(Trash::class)->call('restore', 'todos', $todo->id);
    Livewire::actingAs($user)->test(Trash::class)->call('restore', 'lists', $list->id);
    Livewire::actingAs($user)->test(Trash::class)->call('restore', 'tags', $tag->id);
    Livewire::actingAs($user)->test(Trash::class)->call('restore', 'devices', $device->id);

    expect($todo->fresh()->trashed())->toBeTrue();
    expect($list->fresh()->trashed())->toBeTrue();
    expect($tag->fresh()->trashed())->toBeTrue();
    expect($device->fresh()->trashed())->toBeTrue();

    Livewire::actingAs($user)->test(Trash::class)->call('forceDelete', 'todos', $todo->id);
    expect(Todo::withTrashed()->find($todo->id))->not->toBeNull();
});

test('the trash page uses the custom confirm dialog instead of the browser-native confirm', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create();
    $todo->delete();

    $response = $this->actingAs($user)->get('/trash');

    $response->assertOk();
    $response->assertDontSee('wire:confirm', false);
    $response->assertSee('appConfirm(', false);
});

test('an empty trash shows an empty state', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/trash');

    $response->assertOk();
    $response->assertSee('Trash is empty.');
});
