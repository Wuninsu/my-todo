<?php

namespace App\Livewire\Main;

use App\Services\TodoListService;
use App\Traits\TryAction;
use Livewire\Attributes\On;
use Livewire\Component;

class SidebarLists extends Component
{
    use TryAction;

    public ?string $activeListUuid = null;

    public bool $showForm = false;

    public ?int $editingListId = null;

    public string $name = '';

    public string $color = '#0d6efd';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:20'],
        ];
    }

    #[On('list-selected')]
    public function highlight(?string $uuid = null): void
    {
        $this->activeListUuid = $uuid;
    }

    public function openCreate(): void
    {
        $this->reset(['editingListId', 'name']);
        $this->color = '#0d6efd';
        $this->showForm = true;
    }

    public function startEdit(int $listId, TodoListService $lists): void
    {
        $this->tryAction(function () use ($lists, $listId) {
            $list = $lists->findForEdit($listId);

            $this->editingListId = $list->id;
            $this->name = $list->name;
            $this->color = $list->color ?? '#0d6efd';
            $this->showForm = true;
        }, 'Could not open that list.');
    }

    public function save(TodoListService $lists): void
    {
        $validated = $this->validate();
        $wasEditing = (bool) $this->editingListId;

        $this->tryAction(function () use ($lists, $validated, $wasEditing) {
            if ($this->editingListId) {
                $lists->update($this->editingListId, $validated);
            } else {
                $lists->create(auth()->user(), $validated);
            }

            $this->reset(['showForm', 'editingListId', 'name']);
            $this->color = '#0d6efd';

            $this->dispatch('toast', type: 'success', message: $wasEditing ? 'List renamed.' : 'List created.');
        }, 'Could not save the list.');
    }

    public function delete(int $listId, TodoListService $lists): void
    {
        $this->tryAction(function () use ($lists, $listId) {
            $list = $lists->delete(auth()->user(), $listId);

            if ($this->activeListUuid === $list->uuid) {
                $this->dispatch('list-selected', uuid: null);
            }

            $this->dispatch('toast', type: 'success', message: 'List deleted.');
        }, 'Could not delete the list.');
    }

    public function render()
    {
        $lists = auth()->user()->todoLists()
            ->withCount(['todos' => function ($query) {
                $query->whereNotIn('status', ['done', 'archived']);
            }])
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return view('livewire.main.sidebar-lists', [
            'lists' => $lists,
        ]);
    }
}
