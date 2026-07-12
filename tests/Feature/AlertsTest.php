<?php

use App\Livewire\Main\AlertCenter;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

test('the command creates a reminder alert for a due reminder', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create([
        'reminder_at' => now()->subMinute(),
        'status' => 'todo',
    ]);

    Artisan::call('todos:generate-alerts');

    expect($user->fresh()->notifications)->toHaveCount(1);
    expect($user->fresh()->notifications->first()->data['kind'])->toBe('reminder');
    expect($user->fresh()->notifications->first()->data['todo_id'])->toBe($todo->id);
});

test('the command creates a due-today alert', function () {
    $user = User::factory()->create();
    Todo::factory()->for($user)->create([
        'due_date' => today(),
        'status' => 'todo',
    ]);

    Artisan::call('todos:generate-alerts');

    expect($user->fresh()->notifications()->where('data->kind', 'due_today')->count())->toBe(1);
});

test('the command creates an overdue alert', function () {
    $user = User::factory()->create();
    Todo::factory()->for($user)->create([
        'due_date' => today()->subDays(3),
        'status' => 'todo',
    ]);

    Artisan::call('todos:generate-alerts');

    expect($user->fresh()->notifications()->where('data->kind', 'overdue')->count())->toBe(1);
});

test('the command never alerts for done or archived todos', function () {
    $user = User::factory()->create();
    Todo::factory()->for($user)->create(['due_date' => today()->subDays(2), 'status' => 'done']);
    Todo::factory()->for($user)->create(['due_date' => today()->subDays(2), 'status' => 'archived']);
    Todo::factory()->for($user)->create(['reminder_at' => now()->subMinute(), 'status' => 'done']);

    Artisan::call('todos:generate-alerts');

    expect($user->fresh()->notifications)->toHaveCount(0);
});

test('running the command twice does not duplicate alerts', function () {
    $user = User::factory()->create();
    Todo::factory()->for($user)->create(['due_date' => today()->subDays(1), 'status' => 'todo']);

    Artisan::call('todos:generate-alerts');
    Artisan::call('todos:generate-alerts');

    expect($user->fresh()->notifications)->toHaveCount(1);
});

test('the alert center shows the unread count and notification list', function () {
    $user = User::factory()->create();
    Todo::factory()->for($user)->create(['title' => 'Ship the release', 'due_date' => today()->subDay(), 'status' => 'todo']);

    Artisan::call('todos:generate-alerts');

    Livewire::actingAs($user)
        ->test(AlertCenter::class)
        ->assertViewHas('unreadCount', 1)
        ->assertSee('Ship the release');
});

test('a user can mark a single alert as read', function () {
    $user = User::factory()->create();
    Todo::factory()->for($user)->create(['due_date' => today()->subDay(), 'status' => 'todo']);
    Artisan::call('todos:generate-alerts');

    $notification = $user->fresh()->notifications->first();

    Livewire::actingAs($user)
        ->test(AlertCenter::class)
        ->call('markAsRead', $notification->id)
        ->assertViewHas('unreadCount', 0);

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('a user can mark all alerts as read', function () {
    $user = User::factory()->create();
    Todo::factory()->for($user)->create(['due_date' => today()->subDay(), 'status' => 'todo']);
    Todo::factory()->for($user)->create(['due_date' => today(), 'status' => 'todo']);
    Artisan::call('todos:generate-alerts');

    expect($user->fresh()->unreadNotifications)->toHaveCount(2);

    Livewire::actingAs($user)
        ->test(AlertCenter::class)
        ->call('markAllAsRead')
        ->assertViewHas('unreadCount', 0);

    expect($user->fresh()->unreadNotifications)->toHaveCount(0);
});

test('a user can clear all alerts', function () {
    $user = User::factory()->create();
    Todo::factory()->for($user)->create(['due_date' => today()->subDay(), 'status' => 'todo']);
    Artisan::call('todos:generate-alerts');

    Livewire::actingAs($user)
        ->test(AlertCenter::class)
        ->call('clearAll');

    expect($user->fresh()->notifications)->toHaveCount(0);
});

test('a user only sees their own alerts', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    Todo::factory()->for($other)->create(['title' => 'Not yours', 'due_date' => today()->subDay(), 'status' => 'todo']);

    Artisan::call('todos:generate-alerts');

    Livewire::actingAs($user)
        ->test(AlertCenter::class)
        ->assertViewHas('unreadCount', 0)
        ->assertDontSee('Not yours');
});

test('clicking an alert marks it read and redirects to the dashboard with the todo open', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create(['due_date' => today()->subDay(), 'status' => 'todo']);
    Artisan::call('todos:generate-alerts');

    $notification = $user->fresh()->notifications->first();

    Livewire::actingAs($user)
        ->test(AlertCenter::class)
        ->call('openTodo', $notification->id, $todo->id)
        ->assertRedirect(route('dashboard', ['todo' => $todo->id]));

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('visiting the dashboard with a todo query param opens that todo for editing', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create(['title' => 'Deep linked todo']);

    $response = $this->actingAs($user)->get('/dashboard?todo='.$todo->id);

    $response->assertOk();
    $response->assertSee('Edit Todo');
    $response->assertSee('Deep linked todo');
});

test('visiting the dashboard with another users todo id does not open it', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $todo = Todo::factory()->for($other)->create();

    $response = $this->actingAs($user)->get('/dashboard?todo='.$todo->id);

    $response->assertOk();
    $response->assertDontSee('Edit Todo');
});

test('the alert center renders no browser-native confirm dialog', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertDontSee('wire:confirm', false);
});
