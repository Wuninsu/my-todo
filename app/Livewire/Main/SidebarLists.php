<?php

namespace App\Livewire\Main;

use App\Models\TodoList;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class SidebarLists extends Component
{
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

    public function startEdit(int $listId): void
    {
        $list = TodoList::findOrFail($listId);
        $this->authorize('update', $list);

        $this->editingListId = $list->id;
        $this->name = $list->name;
        $this->color = $list->color ?? '#0d6efd';
        $this->showForm = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingListId) {
            $list = TodoList::findOrFail($this->editingListId);
            $this->authorize('update', $list);

            $list->update([
                'name' => $validated['name'],
                'color' => $validated['color'],
                'version' => $list->version + 1,
                'client_updated_at' => now(),
            ]);
        } else {
            auth()->user()->todoLists()->create([
                'uuid' => Str::uuid(),
                'name' => $validated['name'],
                'color' => $validated['color'],
                'version' => 1,
                'client_updated_at' => now(),
            ]);
        }

        $this->reset(['showForm', 'editingListId', 'name']);
        $this->color = '#0d6efd';
    }

    public function delete(int $listId): void
    {
        $list = TodoList::findOrFail($listId);
        $this->authorize('delete', $list);

        if ($list->is_default) {
            return;
        }

        $defaultList = auth()->user()->todoLists()->where('is_default', true)->first();

        $list->todos()->update(['todo_list_id' => $defaultList?->id]);
        $list->delete();

        if ($this->activeListUuid === $list->uuid) {
            $this->dispatch('list-selected', uuid: null);
        }
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
