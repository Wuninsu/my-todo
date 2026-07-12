<?php

use App\Livewire\Main\Tags;
use App\Models\Tag;
use App\Models\Todo;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get('/tags')->assertRedirect('/login');
});

test('the tags route renders the full layout for an authenticated user', function () {
    $user = User::factory()->create();
    Tag::factory()->for($user)->create(['name' => 'errands']);

    $response = $this->actingAs($user)->get('/tags');

    $response->assertOk();
    $response->assertSee('TodoFlow');
    $response->assertSee('errands');
});

test('a user can create a tag', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Tags::class)
        ->call('openCreate')
        ->set('name', 'urgent')
        ->set('color', '#ef4444')
        ->call('save')
        ->assertSee('urgent')
        ->assertDispatched('toast', type: 'success', message: 'Tag created.');

    $tag = Tag::where('user_id', $user->id)->where('name', 'urgent')->first();

    expect($tag)->not->toBeNull();
    expect($tag->uuid)->not->toBeNull();
});

test('a user cannot create two tags with the same name', function () {
    $user = User::factory()->create();
    Tag::factory()->for($user)->create(['name' => 'urgent']);

    Livewire::actingAs($user)
        ->test(Tags::class)
        ->call('openCreate')
        ->set('name', 'urgent')
        ->call('save')
        ->assertHasErrors(['name']);

    expect(Tag::where('user_id', $user->id)->where('name', 'urgent')->count())->toBe(1);
});

test('a user can rename a tag', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->for($user)->create(['name' => 'old-name', 'version' => 1]);

    Livewire::actingAs($user)
        ->test(Tags::class)
        ->call('startEdit', $tag->id)
        ->set('name', 'new-name')
        ->call('save');

    $tag->refresh();

    expect($tag->name)->toBe('new-name');
    expect($tag->version)->toBe(2);
});

test('deleting a tag detaches it from todos without deleting the todos', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->for($user)->create();
    $todo = Todo::factory()->for($user)->create();
    $todo->tags()->attach($tag);

    Livewire::actingAs($user)
        ->test(Tags::class)
        ->call('delete', $tag->id)
        ->assertDispatched('toast', type: 'success', message: 'Tag deleted.');

    expect(Tag::find($tag->id))->toBeNull();
    expect($todo->fresh())->not->toBeNull();
    expect($todo->fresh()->tags)->toHaveCount(0);
});

test('the tags page renders no browser-native confirm dialog', function () {
    $user = User::factory()->create();
    Tag::factory()->for($user)->create();

    $response = $this->actingAs($user)->get('/tags');

    $response->assertOk();
    $response->assertDontSee('wire:confirm', false);
});

test('a user cannot rename or delete another users tag', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $tag = Tag::factory()->for($other)->create(['name' => 'not-yours']);

    Livewire::actingAs($user)->test(Tags::class)->call('startEdit', $tag->id);
    expect($tag->fresh()->name)->toBe('not-yours');

    Livewire::actingAs($user)->test(Tags::class)->call('delete', $tag->id);
    expect(Tag::find($tag->id))->not->toBeNull();
});

test('the tags page shows a todo count per tag', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->for($user)->create(['name' => 'work']);
    $todo = Todo::factory()->for($user)->create();
    $todo->tags()->attach($tag);

    Livewire::actingAs($user)
        ->test(Tags::class)
        ->assertViewHas('tags', fn ($tags) => $tags->firstWhere('id', $tag->id)->todos_count === 1);
});
