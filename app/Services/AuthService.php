<?php

namespace App\Services;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthService
{
    public function attempt(array $credentials, bool $remember): bool
    {
        return Auth::attempt($credentials, $remember);
    }

    /**
     * Register a new user with their first (default) todo list, then log
     * them in.
     */
    public function register(array $data): User
    {
        $user = User::create([
            'uuid' => Str::uuid(),
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_synced' => false,
            'version' => 1,
            'client_updated_at' => now(),
            'device_uuid' => Str::uuid(),
        ]);

        TodoList::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'name' => 'My Tasks',
            'is_default' => true,
            'version' => 1,
            'client_updated_at' => now(),
        ]);

        Auth::login($user);

        return $user;
    }

    public function redirectPathFor(User $user): string
    {
        return $user->role === 'admin' ? route('admin.dashboard') : route('dashboard');
    }
}
