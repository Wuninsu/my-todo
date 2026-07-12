<?php

namespace App\Livewire\Main;

use App\Services\TrashService;
use App\Traits\TryAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class Trash extends Component
{
    use TryAction;

    #[Url]
    public ?string $model = null;

    public function restore(string $type, int $id, TrashService $trash): void
    {
        $this->tryAction(function () use ($trash, $type, $id) {
            $label = $trash->restore($type, $id);

            $this->dispatch('toast', type: 'success', message: $label.' restored.');
        }, 'Could not restore that item.');
    }

    #[On('trash-force-delete-confirmed')]
    public function forceDelete(string $type, int $id, TrashService $trash): void
    {
        $this->tryAction(function () use ($trash, $type, $id) {
            $label = $trash->forceDelete($type, $id);

            $this->dispatch('toast', type: 'success', message: $label.' permanently deleted.');
        }, 'Could not permanently delete that item.');
    }

    #[Title('Trash')]
    public function render(TrashService $trash)
    {
        $types = ['todos', 'lists', 'tags', 'devices'];
        $user = Auth::user();

        $counts = collect($types)->mapWithKeys(fn ($type) => [$type => $trash->itemsFor($user, $type)->count()]);

        $activeTypes = $this->model ? [$this->model] : $types;

        $items = collect($activeTypes)
            ->flatMap(fn ($type) => $trash->itemsFor($user, $type))
            ->sortByDesc('deleted_at')
            ->values();

        return view('livewire.main.trash', [
            'items' => $items,
            'counts' => $counts,
        ]);
    }
}
