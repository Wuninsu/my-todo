<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Tag;
use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class TrashService
{
    public function modelClass(string $type): string
    {
        return match ($type) {
            'todos' => Todo::class,
            'lists' => TodoList::class,
            'tags' => Tag::class,
            'devices' => Device::class,
            default => abort(404),
        };
    }

    public function label(string $type): string
    {
        return match ($type) {
            'todos' => 'Todo',
            'lists' => 'List',
            'tags' => 'Tag',
            'devices' => 'Device',
        };
    }

    public function restore(string $type, int $id): string
    {
        $record = $this->modelClass($type)::onlyTrashed()->findOrFail($id);
        Gate::authorize('restore', $record);

        $record->restore();

        return $this->label($type);
    }

    public function forceDelete(string $type, int $id): string
    {
        $record = $this->modelClass($type)::onlyTrashed()->findOrFail($id);
        Gate::authorize('forceDelete', $record);

        $record->forceDelete();

        return $this->label($type);
    }

    public function itemsFor(User $user, string $type): Collection
    {
        $records = match ($type) {
            'todos' => $user->todos()->onlyTrashed()->get(),
            'lists' => $user->todoLists()->onlyTrashed()->get(),
            'tags' => $user->tags()->onlyTrashed()->get(),
            'devices' => $user->devices()->onlyTrashed()->get(),
        };

        $icon = match ($type) {
            'todos' => 'bi-check2-square',
            'lists' => 'bi-collection',
            'tags' => 'bi-tags',
            'devices' => 'bi-phone',
        };

        return $records->map(fn ($record) => [
            'type' => $type,
            'id' => $record->id,
            'label' => match ($type) {
                'todos' => $record->title,
                'lists', 'tags' => $record->name,
                'devices' => $record->device_name,
            },
            'meta' => $this->label($type),
            'icon' => $icon,
            'deleted_at' => $record->deleted_at,
        ]);
    }
}
