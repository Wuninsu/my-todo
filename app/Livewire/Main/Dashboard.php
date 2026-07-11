<?php

namespace App\Livewire\Main;

use App\Models\Todo;
use App\Models\TodoList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class Dashboard extends Component
{
    public ?string $activeListUuid = null;

    public ?string $filter = null;

    public ?string $priorityFilter = null;

    public ?int $tagFilter = null;

    public string $search = '';

    public string $quickTitle = '';

    // Todo modal
    public bool $showTodoModal = false;

    public ?int $todoId = null;

    public string $title = '';

    public string $description = '';

    public string $priority = 'medium';

    public ?string $due_date = null;

    public ?string $reminder_at = null;

    public ?int $todo_list_id = null;

    public string $tagsInput = '';

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'reminder_at' => ['nullable', 'date'],
            'todo_list_id' => ['nullable', Rule::exists('todo_lists', 'id')->where('user_id', Auth::id())],
        ];
    }

    #[On('list-selected')]
    public function onListSelected(?string $uuid = null): void
    {
        $this->activeListUuid = $uuid;
        $this->filter = null;
    }

    #[On('filter-selected')]
    public function onFilterSelected(?string $filter = null): void
    {
        $this->filter = $filter;
        $this->activeListUuid = null;
    }

    #[On('search-changed')]
    public function onSearchChanged(string $term = ''): void
    {
        $this->search = $term;
    }

    public function quickAdd(): void
    {
        $title = trim($this->quickTitle);

        if ($title === '') {
            return;
        }

        $listId = $this->resolveTargetListId();

        Todo::create([
            'uuid' => Str::uuid(),
            'user_id' => Auth::id(),
            'todo_list_id' => $listId,
            'title' => $title,
            'status' => 'todo',
            'priority' => 'medium',
            'position' => $this->nextPosition($listId),
            'version' => 1,
            'client_updated_at' => now(),
        ]);

        $this->quickTitle = '';
    }

    #[On('open-create-todo')]
    public function openCreateTodo(): void
    {
        $this->reset(['todoId', 'title', 'description', 'due_date', 'reminder_at', 'tagsInput']);
        $this->priority = 'medium';
        $this->todo_list_id = $this->resolveTargetListId();
        $this->resetErrorBag();
        $this->showTodoModal = true;
    }

    public function editTodo(int $id): void
    {
        $todo = Todo::with('tags')->findOrFail($id);
        $this->authorize('update', $todo);

        $this->todoId = $todo->id;
        $this->title = $todo->title;
        $this->description = (string) $todo->description;
        $this->priority = $todo->priority;
        $this->due_date = $todo->due_date?->format('Y-m-d');
        $this->reminder_at = $todo->reminder_at?->format('Y-m-d\TH:i');
        $this->todo_list_id = $todo->todo_list_id;
        $this->tagsInput = $todo->tags->pluck('name')->implode(', ');
        $this->resetErrorBag();
        $this->showTodoModal = true;
    }

    public function saveTodo(): void
    {
        $this->todo_list_id = $this->todo_list_id ?: null;

        $validated = $this->validate();

        if ($this->todoId) {
            $todo = Todo::findOrFail($this->todoId);
            $this->authorize('update', $todo);

            $todo->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'due_date' => $validated['due_date'],
                'reminder_at' => $validated['reminder_at'],
                'todo_list_id' => $validated['todo_list_id'],
                'version' => $todo->version + 1,
                'client_updated_at' => now(),
            ]);
        } else {
            $todo = Todo::create([
                'uuid' => Str::uuid(),
                'user_id' => Auth::id(),
                'todo_list_id' => $validated['todo_list_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'due_date' => $validated['due_date'],
                'reminder_at' => $validated['reminder_at'],
                'status' => 'todo',
                'position' => $this->nextPosition($validated['todo_list_id']),
                'version' => 1,
                'client_updated_at' => now(),
            ]);
        }

        $this->syncTags($todo);

        $this->showTodoModal = false;
    }

    protected function syncTags(Todo $todo): void
    {
        $names = collect(explode(',', $this->tagsInput))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique();

        $tagIds = $names->map(function (string $name) {
            return Auth::user()->tags()->firstOrCreate(
                ['name' => $name],
                ['uuid' => Str::uuid(), 'version' => 1, 'client_updated_at' => now()]
            )->id;
        });

        $todo->tags()->sync($tagIds);
    }

    public function deleteTodo(int $id): void
    {
        $todo = Todo::findOrFail($id);
        $this->authorize('delete', $todo);

        $todo->delete();
    }

    public function toggleStatus(int $id): void
    {
        $todo = Todo::findOrFail($id);
        $this->authorize('update', $todo);

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
    }

    public function toggleFavorite(int $id): void
    {
        $todo = Todo::findOrFail($id);
        $this->authorize('update', $todo);

        $todo->update([
            'is_favorite' => ! $todo->is_favorite,
            'version' => $todo->version + 1,
            'client_updated_at' => now(),
        ]);
    }

    protected function nextPosition(?int $listId): int
    {
        return Auth::user()->todos()->where('todo_list_id', $listId)->max('position') + 1;
    }

    public function moveUp(int $id): void
    {
        $this->reorder($id, -1);
    }

    public function moveDown(int $id): void
    {
        $this->reorder($id, 1);
    }

    protected function reorder(int $id, int $direction): void
    {
        $todo = Todo::findOrFail($id);
        $this->authorize('update', $todo);

        $siblings = Auth::user()->todos()
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

    protected function resolveTargetListId(): ?int
    {
        if ($this->activeListUuid) {
            return TodoList::where('user_id', Auth::id())
                ->where('uuid', $this->activeListUuid)
                ->value('id');
        }

        return Auth::user()->todoLists()->where('is_default', true)->value('id');
    }

    #[Title('Dashboard')]
    public function render()
    {
        $query = Auth::user()->todos()->with(['list', 'tags'])->where('status', '!=', 'archived');

        if ($this->activeListUuid) {
            $query->whereHas('list', fn ($q) => $q->where('uuid', $this->activeListUuid));
        }

        match ($this->filter) {
            'today' => $query->whereDate('due_date', today()),
            'upcoming' => $query->whereDate('due_date', '>', today()),
            'completed' => $query->where('status', 'done'),
            'favorites' => $query->where('is_favorite', true),
            default => null,
        };

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        if ($this->tagFilter) {
            $query->whereHas('tags', fn ($q) => $q->where('tags.id', $this->tagFilter));
        }

        if ($this->search !== '') {
            $query->where('title', 'like', '%'.$this->search.'%');
        }

        $todos = $query->orderBy('position')->orderByDesc('created_at')->get();

        return view('livewire.main.dashboard', [
            'todos' => $todos,
            'lists' => Auth::user()->todoLists()->orderByDesc('is_default')->orderBy('name')->get(),
            'allTags' => Auth::user()->tags()->orderBy('name')->get(),
        ]);
    }
}
