<?php

namespace App\Services;

use App\Exceptions\ActionNotAllowedException;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class UserService
{
    public function scopedUsers(string $search, string $role, bool $showTrashed): Builder
    {
        return User::query()
            ->when($showTrashed, fn ($query) => $query->onlyTrashed())
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, fn ($query) => $query->where('role', $role))
            ->latest();
    }

    public function deactivate(User $admin, int $targetId): void
    {
        if ($admin->id === $targetId) {
            throw new ActionNotAllowedException('You cannot delete your own account.');
        }

        $target = User::findOrFail($targetId);
        Gate::authorize('delete', $target);

        $target->delete();
    }

    public function restore(int $id): User
    {
        $user = User::onlyTrashed()->findOrFail($id);
        Gate::authorize('restore', $user);

        $user->restore();

        return $user;
    }

    public function update(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'theme' => $data['theme'],
            'timezone' => $data['timezone'],
            'is_synced' => false,
            'version' => $user->version + 1,
            'client_updated_at' => now(),
        ]);

        return $user;
    }
}
