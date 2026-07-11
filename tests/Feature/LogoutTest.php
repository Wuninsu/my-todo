<?php

use App\Models\User;

test('an authenticated user can log out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect('/');

    $this->assertGuest();

    $this->get('/dashboard')->assertRedirect('/login');
});

test('guests cannot hit the logout route', function () {
    $this->post('/logout')->assertRedirect('/login');
});
