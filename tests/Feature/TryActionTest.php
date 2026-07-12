<?php

use App\Livewire\Main\Dashboard;
use App\Models\Todo;
use App\Models\User;
use Livewire\Livewire;

test('a failed service call is caught and shown as a generic error toast instead of crashing', function () {
    $user = User::factory()->create();

    // A non-existent id makes the service throw ModelNotFoundException.
    // Without TryAction this would bubble up as an uncaught exception.
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('toggleStatus', 999999)
        ->assertDispatched('toast', type: 'error', message: 'Could not update the todo.')
        ->assertOk();
});

test('a failed action writes to the dedicated services log file, not the default log', function () {
    $user = User::factory()->create();

    // A dedicated file path is the real proof of a separate channel — the
    // Monolog line prefix itself is labelled by environment name, not the
    // Laravel config key, so it isn't a reliable thing to assert on.
    $path = storage_path('logs/services-'.now()->format('Y-m-d').'.log');
    $before = file_exists($path) ? filesize($path) : 0;

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('toggleStatus', 999999);

    expect(file_exists($path))->toBeTrue();
    expect(filesize($path))->toBeGreaterThan($before);

    $tail = file_get_contents($path);
    expect($tail)->toContain('No query results for model [App\Models\Todo] 999999');
});

test('an authorization failure inside a service shows a permission-denied toast, not a 403 crash', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $todo = Todo::factory()->for($other)->create(['status' => 'todo']);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('toggleStatus', $todo->id)
        ->assertDispatched('toast', type: 'error', message: 'You do not have permission to do that.')
        ->assertOk();

    expect($todo->fresh()->status)->toBe('todo');
});
