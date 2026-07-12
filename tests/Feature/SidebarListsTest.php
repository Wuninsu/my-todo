<?php

use App\Livewire\Main\SidebarLists;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Livewire\Livewire;

test('creating a list adds it for the current user', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(SidebarLists::class)
        ->call('openCreate')
        ->set('name', 'Groceries')
        ->call('save')
        ->assertSee('Groceries')
        ->assertDispatched('toast', type: 'success', message: 'List created.');

    expect(TodoList::where('user_id', $user->id)->where('name', 'Groceries')->exists())->toBeTrue();
});

test('renaming a list updates it and bumps the version', function () {
    $user = User::factory()->create();
    $list = TodoList::factory()->for($user)->create(['name' => 'Old name', 'version' => 1]);

    Livewire::actingAs($user)
        ->test(SidebarLists::class)
        ->call('startEdit', $list->id)
        ->set('name', 'New name')
        ->call('save');

    $list->refresh();

    expect($list->name)->toBe('New name');
    expect($list->version)->toBe(2);
});

test('deleting a list reassigns its todos to the default list', function () {
    $user = User::factory()->create();
    $default = TodoList::factory()->for($user)->default()->create();
    $work = TodoList::factory()->for($user)->create(['name' => 'Work']);
    $todo = Todo::factory()->for($user)->for($work, 'list')->create();

    Livewire::actingAs($user)
        ->test(SidebarLists::class)
        ->call('delete', $work->id)
        ->assertDispatched('toast', type: 'success', message: 'List deleted.');

    expect(TodoList::find($work->id))->toBeNull();
    expect($todo->fresh()->todo_list_id)->toBe($default->id);
});

test('the sidebar renders no browser-native confirm dialog for list deletion', function () {
    $user = User::factory()->create();
    TodoList::factory()->for($user)->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertDontSee('wire:confirm', false);
});

test('the default list cannot be deleted', function () {
    $user = User::factory()->create();
    $default = TodoList::factory()->for($user)->default()->create();

    Livewire::actingAs($user)
        ->test(SidebarLists::class)
        ->call('delete', $default->id)
        ->assertDispatched('toast', type: 'error', message: 'The default list cannot be deleted.');

    expect(TodoList::find($default->id))->not->toBeNull();
});

test('a user cannot rename or delete another users list', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $list = TodoList::factory()->for($other)->create(['name' => 'Not yours']);

    Livewire::actingAs($user)->test(SidebarLists::class)->call('startEdit', $list->id);
    expect($list->fresh()->name)->toBe('Not yours');

    Livewire::actingAs($user)->test(SidebarLists::class)->call('delete', $list->id);
    expect(TodoList::find($list->id))->not->toBeNull();
});
