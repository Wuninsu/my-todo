<?php

use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;

test('the dashboard route renders the full layout, sidebar, and todos for an authenticated user', function () {
    $user = User::factory()->create();
    $default = TodoList::factory()->for($user)->default()->create(['name' => 'My Tasks']);
    Todo::factory()->for($user)->for($default, 'list')->create(['title' => 'Route smoke test todo']);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('TodoFlow');
    $response->assertSee('My Tasks');
    $response->assertSee('Route smoke test todo');
});

test('the today filter is bound to the URL and highlighted when active', function () {
    $user = User::factory()->create();
    $today = Todo::factory()->for($user)->create(['title' => 'Due today', 'due_date' => today()]);
    $later = Todo::factory()->for($user)->create(['title' => 'Due later', 'due_date' => today()->addWeek()]);

    $response = $this->actingAs($user)->get('/dashboard?filter=today');

    $response->assertOk();
    $response->assertSee('Due today');
    $response->assertDontSee('Due later');

    // the sidebar's "Today" link is marked active while the filter is applied via the URL
    $response->assertSee('class="app-sidebar-item active"', false);
});

test('the filter navigation links are present and point at the dashboard even from another page', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/admin/users');

    $response->assertOk();
    $response->assertSee(route('dashboard', ['filter' => 'today']), false);
    $response->assertSee(route('dashboard', ['filter' => 'favorites']), false);
});

test('visiting the dashboard with the new-todo deep link opens the create modal', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard?new=1');

    $response->assertOk();
    $response->assertSee('New Todo');
});

test('the new todo link is present and points at the dashboard even from another page', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/admin/users');

    $response->assertOk();
    $response->assertSee(route('dashboard', ['new' => 1]), false);
});

test('a flashed toast is rendered as a pending-toast bootstrap script on the next page', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->withSession(['toast' => ['type' => 'success', 'message' => 'Flashed from a redirect.']])
        ->get('/dashboard');

    $response->assertOk();
    $response->assertSee('window.__pendingToast', false);
    $response->assertSee('Flashed from a redirect.');
});

test('the global toast container and confirm dialog are present on every authenticated page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('id="app-toast-container"', false);
    $response->assertSee('id="appConfirmBackdrop"', false);
});
