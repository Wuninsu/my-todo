<?php

use App\Livewire\Main\Dashboard;
use App\Models\Tag;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated users see their own todos on the dashboard', function () {
    $user = User::factory()->create();
    $list = TodoList::factory()->for($user)->default()->create();
    $mine = Todo::factory()->for($user)->for($list, 'list')->create(['title' => 'My todo']);

    $other = User::factory()->create();
    $otherList = TodoList::factory()->for($other)->default()->create();
    Todo::factory()->for($other)->for($otherList, 'list')->create(['title' => 'Not mine']);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('My todo')
        ->assertDontSee('Not mine');
});

test('quick add creates a todo in the default list', function () {
    $user = User::factory()->create();
    $default = TodoList::factory()->for($user)->default()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('quickTitle', 'Ship the feature')
        ->call('quickAdd')
        ->assertSee('Ship the feature');

    $todo = Todo::where('user_id', $user->id)->first();

    expect($todo->title)->toBe('Ship the feature');
    expect($todo->todo_list_id)->toBe($default->id);
    expect($todo->uuid)->not->toBeNull();
});

test('toggle status cycles todo through doing and done', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create(['status' => 'todo']);

    $component = Livewire::actingAs($user)->test(Dashboard::class);

    $component->call('toggleStatus', $todo->id);
    expect($todo->fresh()->status)->toBe('doing');

    $component->call('toggleStatus', $todo->id);
    expect($todo->fresh()->status)->toBe('done');
    expect($todo->fresh()->completed_at)->not->toBeNull();

    $component->call('toggleStatus', $todo->id);
    expect($todo->fresh()->status)->toBe('todo');
    expect($todo->fresh()->completed_at)->toBeNull();
});

test('toggle favorite flips the flag', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create(['is_favorite' => false]);

    Livewire::actingAs($user)->test(Dashboard::class)->call('toggleFavorite', $todo->id);

    expect($todo->fresh()->is_favorite)->toBeTrue();
});

test('saving a todo with tags attaches them', function () {
    $user = User::factory()->create();
    $list = TodoList::factory()->for($user)->default()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('openCreateTodo')
        ->set('title', 'Write docs')
        ->set('tagsInput', 'urgent, writing')
        ->call('saveTodo');

    $todo = Todo::where('title', 'Write docs')->firstOrFail();

    expect($todo->tags->pluck('name')->sort()->values()->all())->toBe(['urgent', 'writing']);
    expect(Tag::where('user_id', $user->id)->count())->toBe(2);
});

test('delete todo soft deletes it and it disappears from the dashboard', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create(['title' => 'Temp todo']);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Temp todo')
        ->call('deleteTodo', $todo->id)
        ->assertDontSee('Temp todo');

    expect($todo->fresh()->trashed())->toBeTrue();
});

test('a user cannot edit or delete another users todo', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $todo = Todo::factory()->for($other)->create();

    expect(fn () => Livewire::actingAs($user)->test(Dashboard::class)->call('deleteTodo', $todo->id))
        ->toThrow(ModelNotFoundException::class);

    expect($todo->fresh()->trashed())->toBeFalse();
});

test('selecting a list filters the dashboard to that list only', function () {
    $user = User::factory()->create();
    $listA = TodoList::factory()->for($user)->create(['name' => 'List A']);
    $listB = TodoList::factory()->for($user)->create(['name' => 'List B']);

    Todo::factory()->for($user)->for($listA, 'list')->create(['title' => 'Todo A']);
    Todo::factory()->for($user)->for($listB, 'list')->create(['title' => 'Todo B']);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('onListSelected', $listA->uuid)
        ->assertSee('Todo A')
        ->assertDontSee('Todo B');
});

test('priority filter narrows the list', function () {
    $user = User::factory()->create();
    Todo::factory()->for($user)->create(['title' => 'High one', 'priority' => 'high']);
    Todo::factory()->for($user)->create(['title' => 'Low one', 'priority' => 'low']);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('priorityFilter', 'high')
        ->assertSee('High one')
        ->assertDontSee('Low one');
});

test('tag filter narrows the list', function () {
    $user = User::factory()->create();
    $tagged = Todo::factory()->for($user)->create(['title' => 'Tagged todo']);
    Todo::factory()->for($user)->create(['title' => 'Untagged todo']);

    $tag = Tag::factory()->for($user)->create(['name' => 'urgent']);
    $tagged->tags()->attach($tag);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('tagFilter', $tag->id)
        ->assertSee('Tagged todo')
        ->assertDontSee('Untagged todo');
});

test('moving a todo up and down reorders it within its list', function () {
    $user = User::factory()->create();
    $list = TodoList::factory()->for($user)->default()->create();

    $first = Todo::factory()->for($user)->for($list, 'list')->create(['title' => 'First', 'position' => 0]);
    $second = Todo::factory()->for($user)->for($list, 'list')->create(['title' => 'Second', 'position' => 1]);
    $third = Todo::factory()->for($user)->for($list, 'list')->create(['title' => 'Third', 'position' => 2]);

    Livewire::actingAs($user)->test(Dashboard::class)->call('moveDown', $first->id);

    expect($first->fresh()->position)->toBe(1);
    expect($second->fresh()->position)->toBe(0);
    expect($third->fresh()->position)->toBe(2);

    Livewire::actingAs($user)->test(Dashboard::class)->call('moveUp', $third->id);

    expect($third->fresh()->position)->toBe(1);
    expect($first->fresh()->position)->toBe(2);
});

test('a new todo is appended to the end of its list', function () {
    $user = User::factory()->create();
    $list = TodoList::factory()->for($user)->default()->create();
    Todo::factory()->for($user)->for($list, 'list')->create(['position' => 5]);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('quickTitle', 'New at the end')
        ->call('quickAdd');

    $todo = Todo::where('title', 'New at the end')->firstOrFail();

    expect($todo->position)->toBe(6);
});
