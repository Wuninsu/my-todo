<?php

use App\Livewire\Main\Tags;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\TagSeeder;
use Livewire\Livewire;

test('the tag seeder creates system tags with no owner', function () {
    (new TagSeeder)->run();

    expect(Tag::system()->count())->toBe(5);
    expect(Tag::system()->pluck('user_id')->filter()->count())->toBe(0);
});

test('the tag seeder is idempotent and never duplicates system tags', function () {
    (new TagSeeder)->run();
    (new TagSeeder)->run();

    expect(Tag::system()->count())->toBe(5);
    expect(Tag::system()->where('name', 'urgent')->count())->toBe(1);
});

test('availableTo scope returns a users own tags plus every system tag', function () {
    (new TagSeeder)->run();

    $user = User::factory()->create();
    $mine = Tag::factory()->for($user)->create(['name' => 'mine']);

    $other = User::factory()->create();
    Tag::factory()->for($other)->create(['name' => 'not-mine']);

    $available = Tag::availableTo($user)->pluck('name')->sort()->values()->all();

    expect($available)->toContain('mine');
    expect($available)->toContain('urgent');
    expect($available)->not->toContain('not-mine');
});

test('a user cannot rename a system tag', function () {
    (new TagSeeder)->run();
    $user = User::factory()->create();
    $systemTag = Tag::system()->first();

    Livewire::actingAs($user)
        ->test(Tags::class)
        ->call('startEdit', $systemTag->id)
        ->assertDispatched('toast', type: 'error', message: 'System tags cannot be edited.');

    expect($systemTag->fresh()->name)->toBe($systemTag->name);
});

test('a user cannot delete a system tag', function () {
    (new TagSeeder)->run();
    $user = User::factory()->create();
    $systemTag = Tag::system()->first();

    Livewire::actingAs($user)
        ->test(Tags::class)
        ->call('delete', $systemTag->id)
        ->assertDispatched('toast', type: 'error', message: 'System tags cannot be deleted.');

    expect(Tag::find($systemTag->id))->not->toBeNull();
});

test('a user cannot create a personal tag that collides with a system tag name', function () {
    (new TagSeeder)->run();
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Tags::class)
        ->call('openCreate')
        ->set('name', 'urgent')
        ->call('save')
        ->assertHasErrors(['name']);

    expect(Tag::where('user_id', $user->id)->where('name', 'urgent')->count())->toBe(0);
});

test('the tags page shows system tags with a lock instead of edit/delete controls', function () {
    (new TagSeeder)->run();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/tags');

    $response->assertOk();
    $response->assertSee('System');
    $response->assertSee('bi-lock', false);
});
