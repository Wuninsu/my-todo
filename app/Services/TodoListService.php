<?php

namespace App\Services;

use App\Exceptions\ActionNotAllowedException;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class TodoListService
{
    public function create(User $user, array $data): TodoList
    {
        return $user->todoLists()->create([
            'uuid' => Str::uuid(),
            'name' => $data['name'],
            'color' => $data['color'],
            'version' => 1,
            'client_updated_at' => now(),
        ]);
    }

    public function findForEdit(int $id): TodoList
    {
        $list = TodoList::findOrFail($id);
        Gate::authorize('update', $list);

        return $list;
    }

    public function update(int $id, array $data): TodoList
    {
        $list = $this->findForEdit($id);

        $list->update([
            'name' => $data['name'],
            'color' => $data['color'],
            'version' => $list->version + 1,
            'client_updated_at' => now(),
        ]);

        return $list;
    }

    /**
     * Deleting a list reassigns its todos to the user's default list rather
     * than deleting them — a list is just an organizational label.
     */
    public function delete(User $user, int $id): TodoList
    {
        $list = TodoList::findOrFail($id);
        Gate::authorize('delete', $list);

        if ($list->is_default) {
            throw new ActionNotAllowedException('The default list cannot be deleted.');
        }

        $defaultList = $user->todoLists()->where('is_default', true)->first();

        $list->todos()->update(['todo_list_id' => $defaultList?->id]);
        $list->delete();

        return $list;
    }
}
