<?php

namespace App\Livewire\Main;

use App\Models\Tag;
use App\Services\TagService;
use App\Services\TodoService;
use App\Traits\TryAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class Dashboard extends Component
{
    use TryAction;

    public ?string $activeListUuid = null;

    #[Url]
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

    public function mount(TodoService $todos): void
    {
        if (request()->boolean('new')) {
            $this->openCreateTodo($todos);
        }

        if ($todoId = request()->integer('todo')) {
            $todo = Auth::user()->todos()->find($todoId);

            if ($todo) {
                $this->editTodo($todo->id, $todos);
            }
        }
    }

    #[On('list-selected')]
    public function onListSelected(?string $uuid = null): void
    {
        $this->activeListUuid = $uuid;
        $this->filter = null;
    }

    #[On('search-changed')]
    public function onSearchChanged(string $term = ''): void
    {
        $this->search = $term;
    }

    public function quickAdd(TodoService $todos): void
    {
        $title = trim($this->quickTitle);

        if ($title === '') {
            return;
        }

        $this->tryAction(function () use ($todos, $title) {
            $listId = $todos->resolveTargetListId(Auth::user(), $this->activeListUuid);

            $todos->quickAdd(Auth::user(), $title, $listId);

            $this->quickTitle = '';

            $this->dispatch('toast', type: 'success', message: 'Todo added.');
        }, 'Could not add the todo.');
    }

    public function openCreateTodo(TodoService $todos): void
    {
        $this->reset(['todoId', 'title', 'description', 'due_date', 'reminder_at', 'tagsInput']);
        $this->priority = 'medium';
        $this->todo_list_id = $todos->resolveTargetListId(Auth::user(), $this->activeListUuid);
        $this->resetErrorBag();
        $this->showTodoModal = true;
    }

    public function editTodo(int $id, TodoService $todos): void
    {
        $this->tryAction(function () use ($todos, $id) {
            $todo = $todos->findForEdit($id);

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
        }, 'Could not open that todo.');
    }

    public function saveTodo(TodoService $todos, TagService $tags): void
    {
        $this->todo_list_id = $this->todo_list_id ?: null;

        $validated = $this->validate();

        $this->tryAction(function () use ($todos, $tags, $validated) {
            $tagIds = $tags->resolveTagIds(Auth::user(), $this->tagsInput);

            if ($this->todoId) {
                $todos->update($this->todoId, $validated, $tagIds);
            } else {
                $todos->create(Auth::user(), $validated, $tagIds);
            }

            $wasEditing = (bool) $this->todoId;

            $this->showTodoModal = false;

            $this->dispatch('toast', type: 'success', message: $wasEditing ? 'Todo updated.' : 'Todo added.');
        }, 'Could not save the todo.');
    }

    public function deleteTodo(int $id, TodoService $todos): void
    {
        $this->tryAction(function () use ($todos, $id) {
            $todos->delete($id);

            $this->dispatch('toast', type: 'success', message: 'Todo moved to trash.');
        }, 'Could not delete the todo.');
    }

    public function toggleStatus(int $id, TodoService $todos): void
    {
        $this->tryAction(fn () => $todos->toggleStatus($id), 'Could not update the todo.');
    }

    public function toggleFavorite(int $id, TodoService $todos): void
    {
        $this->tryAction(fn () => $todos->toggleFavorite($id), 'Could not update the todo.');
    }

    public function moveUp(int $id, TodoService $todos): void
    {
        $this->tryAction(fn () => $todos->reorder(Auth::user(), $id, -1), 'Could not reorder the todo.');
    }

    public function moveDown(int $id, TodoService $todos): void
    {
        $this->tryAction(fn () => $todos->reorder(Auth::user(), $id, 1), 'Could not reorder the todo.');
    }

    #[Title('Dashboard')]
    public function render(TodoService $todos)
    {
        $todoList = $todos->scopedTodos(Auth::user(), $this->activeListUuid, $this->filter, $this->priorityFilter, $this->tagFilter, $this->search)
            ->with(['list', 'tags'])
            ->orderBy('position')
            ->orderByDesc('created_at')
            ->get();

        $priorityCounts = $todos->priorityCounts(Auth::user(), $this->activeListUuid, $this->filter, $this->tagFilter, $this->search);

        return view('livewire.main.dashboard', [
            'todos' => $todoList,
            'lists' => Auth::user()->todoLists()->orderByDesc('is_default')->orderBy('name')->get(),
            'allTags' => Tag::availableTo(Auth::user())->orderBy('name')->get(),
            'priorityCounts' => $priorityCounts,
        ]);
    }
}
