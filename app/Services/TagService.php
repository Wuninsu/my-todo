<?php

namespace App\Services;

use App\Exceptions\ActionNotAllowedException;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class TagService
{
    /**
     * Resolve a comma-separated tag string into tag ids, reusing a system
     * tag or one of the user's own tags by name before creating a new
     * personal one — so typing "urgent" never shadows the shared system tag.
     */
    public function resolveTagIds(User $user, string $tagsInput): Collection
    {
        $names = collect(explode(',', $tagsInput))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique();

        return $names->map(function (string $name) use ($user) {
            $existing = Tag::availableTo($user)->where('name', $name)->first();

            if ($existing) {
                return $existing->id;
            }

            return $user->tags()->create([
                'uuid' => Str::uuid(),
                'name' => $name,
                'version' => 1,
                'client_updated_at' => now(),
            ])->id;
        });
    }

    public function create(User $user, array $data): Tag
    {
        return $user->tags()->create([
            'uuid' => Str::uuid(),
            'name' => $data['name'],
            'color' => $data['color'],
            'version' => 1,
            'client_updated_at' => now(),
        ]);
    }

    public function findForEdit(int $id): Tag
    {
        $tag = Tag::findOrFail($id);

        if ($tag->isSystem()) {
            throw new ActionNotAllowedException('System tags cannot be edited.');
        }

        Gate::authorize('update', $tag);

        return $tag;
    }

    public function update(int $id, array $data): Tag
    {
        $tag = $this->findForEdit($id);

        $tag->update([
            'name' => $data['name'],
            'color' => $data['color'],
            'version' => $tag->version + 1,
            'client_updated_at' => now(),
        ]);

        return $tag;
    }

    public function delete(int $id): void
    {
        $tag = Tag::findOrFail($id);

        if ($tag->isSystem()) {
            throw new ActionNotAllowedException('System tags cannot be deleted.');
        }

        Gate::authorize('delete', $tag);

        $tag->todos()->detach();
        $tag->delete();
    }
}
