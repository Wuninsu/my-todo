<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
{
    public function update(User $user, Tag $tag): bool
    {
        return ! $tag->isSystem() && $user->id === $tag->user_id;
    }

    public function delete(User $user, Tag $tag): bool
    {
        return ! $tag->isSystem() && $user->id === $tag->user_id;
    }

    public function restore(User $user, Tag $tag): bool
    {
        return ! $tag->isSystem() && $user->id === $tag->user_id;
    }

    public function forceDelete(User $user, Tag $tag): bool
    {
        return ! $tag->isSystem() && $user->id === $tag->user_id;
    }
}
