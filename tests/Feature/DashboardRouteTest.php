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
