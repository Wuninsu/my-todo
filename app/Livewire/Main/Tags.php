<?php

namespace App\Livewire\Main;

use App\Models\Tag;
use App\Services\TagService;
use App\Traits\TryAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

class Tags extends Component
{
    use TryAction;

    public bool $showForm = false;

    public ?int $editingTagId = null;

    public string $name = '';

    public string $color = '#6366f1';

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // A user's new/renamed tag can't collide with one of their
                // own tags or with a system tag — system tags are meant to
                // be reused, not shadowed by a same-named personal one.
                Rule::unique('tags', 'name')
                    ->where(fn ($query) => $query->where('user_id', Auth::id())->orWhereNull('user_id'))
                    ->ignore($this->editingTagId),
            ],
            'color' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editingTagId', 'name']);
        $this->color = '#6366f1';
        $this->resetErrorBag();
        $this->showForm = true;
    }

    public function startEdit(int $tagId, TagService $tags): void
    {
        $this->tryAction(function () use ($tags, $tagId) {
            $tag = $tags->findForEdit($tagId);

            $this->editingTagId = $tag->id;
            $this->name = $tag->name;
            $this->color = $tag->color ?? '#6366f1';
            $this->resetErrorBag();
            $this->showForm = true;
        }, 'Could not open that tag.');
    }

    public function save(TagService $tags): void
    {
        $validated = $this->validate();
        $wasEditing = (bool) $this->editingTagId;

        $this->tryAction(function () use ($tags, $validated, $wasEditing) {
            if ($this->editingTagId) {
                $tags->update($this->editingTagId, $validated);
            } else {
                $tags->create(Auth::user(), $validated);
            }

            $this->cancel();

            $this->dispatch('toast', type: 'success', message: $wasEditing ? 'Tag renamed.' : 'Tag created.');
        }, 'Could not save the tag.');
    }

    public function cancel(): void
    {
        $this->reset(['showForm', 'editingTagId', 'name']);
        $this->color = '#6366f1';
        $this->resetErrorBag();
    }

    public function delete(int $tagId, TagService $tags): void
    {
        $this->tryAction(function () use ($tags, $tagId) {
            $tags->delete($tagId);

            $this->dispatch('toast', type: 'success', message: 'Tag deleted.');
        }, 'Could not delete the tag.');
    }

    #[Title('Tags')]
    public function render()
    {
        return view('livewire.main.tags', [
            'tags' => Tag::availableTo(Auth::user())
                ->withCount('todos')
                ->orderByRaw('user_id is not null') // system tags first
                ->orderBy('name')
                ->get(),
        ]);
    }
}
