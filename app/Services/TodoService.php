<?php

namespace App\Services;

use App\Models\Todo;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class TodoService
{
    public function scopedTodos(
        User $user,
        ?string $activeListUuid,
        ?string $filter,
        ?string $priorityFilter,
        ?int $tagFilter,
        string $search,
        bool $applyPriorityFilter = true,
    ) {
        $query = $user->todos()->where('status', '!=', 'archived');

        if ($activeListUuid) {
            $query->whereHas('list', fn ($q) => $q->where('uuid', $activeListUuid));
        }

        match ($filter) {
            'today' => $query->whereDate('due_date', today()),
            'upcoming' => $query->whereDate('due_date', '>', today()),
            'completed' => $query->where('status', 'done'),
            'favorites' => $query->where('is_favorite', true),
            default => null,
        };

        if ($applyPriorityFilter && $priorityFilter) {
            $query->where('priority', $priorityFilter);
        }

        if ($tagFilter) {
            $query->whereHas('tags', fn ($q) => $q->where('tags.id', $tagFilter));
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        return $query;
    }

    public function priorityCounts(User $user, ?string $activeListUuid, ?string $filter, ?int $tagFilter, string $search): Collection
    {
        return collect(['low' => 0, 'medium' => 0, 'high' => 0])
            ->merge(
                $this->scopedTodos($user, $activeListUuid, $filter, null, $tagFilter, $search, applyPriorityFilter: false)
                    ->selectRaw('priority, count(*) as aggregate')
                    ->groupBy('priority')
                    ->pluck('aggregate', 'priority')
            );
    }

    public function resolveTargetListId(User $user, ?string $activeListUuid): ?int
    {
        if ($activeListUuid) {
            return TodoList::where('user_id', $user->id)
                ->where('uuid', $activeListUuid)
                ->value('id');
        }

        return $user->todoLists()->where('is_default', true)->value('id');
    }

    protected function nextPosition(User $user, ?int $listId): int
    {
        return $user->todos()->where('todo_list_id', $listId)->max('position') + 1;
    }

    public function quickAdd(User $user, string $title, ?int $listId): Todo
    {
        return Todo::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'todo_list_id' => $listId,
            'title' => $title,
            'status' => 'todo',
            'priority' => 'medium',
            'position' => $this->nextPosition($user, $listId),
            'version' => 1,
            'client_updated_at' => now(),
        ]);
    }

    public function create(User $user, array $data, Collection $tagIds): Todo
    {
        $todo = Todo::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'todo_list_id' => $data['todo_list_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'due_date' => $data['due_date'],
            'reminder_at' => $data['reminder_at'],
            'status' => 'todo',
            'position' => $this->nextPosition($user, $data['todo_list_id']),
            'version' => 1,
            'client_updated_at' => now(),
        ]);

        $todo->tags()->sync($tagIds);

        return $todo;
    }

    public function update(int $id, array $data, Collection $tagIds): Todo
    {
        $todo = Todo::findOrFail($id);
        Gate::authorize('update', $todo);

        $todo->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'due_date' => $data['due_date'],
            'reminder_at' => $data['reminder_at'],
            'todo_list_id' => $data['todo_list_id'],
            'version' => $todo->version + 1,
            'client_updated_at' => now(),
        ]);

        $todo->tags()->sync($tagIds);

        return $todo;
    }

    public function findForEdit(int $id): Todo
    {
        $todo = Todo::with('tags')->findOrFail($id);
        Gate::authorize('update', $todo);

        return $todo;
    }

    public function delete(int $id): void
    {
        $todo = Todo::findOrFail($id);
        Gate::authorize('delete', $todo);

        $todo->delete();
    }

    public function toggleStatus(int $id): Todo
    {
        $todo = Todo::findOrFail($id);
        Gate::authorize('update', $todo);

        $next = match ($todo->status) {
            'todo' => 'doing',
            'doing' => 'done',
            default => 'todo',
        };

        $todo->update([
            'status' => $next,
            'started_at' => $next === 'doing' ? now() : $todo->started_at,
            'completed_at' => $next === 'done' ? now() : null,
            'version' => $todo->version + 1,
            'client_updated_at' => now(),
        ]);

        return $todo;
    }

    public function toggleFavorite(int $id): Todo
    {
        $todo = Todo::findOrFail($id);
        Gate::authorize('update', $todo);

        $todo->update([
            'is_favorite' => ! $todo->is_favorite,
            'version' => $todo->version + 1,
            'client_updated_at' => now(),
        ]);

        return $todo;
    }

    public function reorder(User $user, int $id, int $direction): void
    {
        $todo = Todo::findOrFail($id);
        Gate::authorize('update', $todo);

        $siblings = $user->todos()
            ->where('todo_list_id', $todo->todo_list_id)
            ->orderBy('position')
            ->orderBy('created_at')
            ->get()
            ->values();

        $index = $siblings->search(fn ($sibling) => $sibling->id === $todo->id);
        $targetIndex = $index + $direction;

        if ($index === false || $targetIndex < 0 || $targetIndex >= $siblings->count()) {
            return;
        }

        $moved = $siblings->pull($index);
        $siblings->splice($targetIndex, 0, [$moved]);

        foreach ($siblings->values() as $position => $sibling) {
            if ($sibling->position !== $position) {
                $sibling->update(['position' => $position]);
            }
        }
    }
}
