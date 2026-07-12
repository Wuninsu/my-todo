<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class AlertService
{
    public function markAsRead(User $user, string $notificationId): void
    {
        $user->notifications()->where('id', $notificationId)->first()?->markAsRead();
    }

    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications->markAsRead();
    }

    public function clearAll(User $user): void
    {
        $user->notifications()->delete();
    }

    public function recent(User $user, int $limit = 20): Collection
    {
        return $user->notifications()->latest()->limit($limit)->get();
    }

    public function unreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }
}
