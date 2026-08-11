<div>

    <div class="d-flex align-items-center justify-content-between small text-uppercase text-muted fw-semibold mt-5 mb-2">
        <span>Lists</span>

        <button type="button" class="btn btn-sm p-0 app-icon-btn" wire:click="openCreate" title="New list">
            <i class="bi bi-plus-lg"></i>
        </button>
    </div>

      @if ($showForm)
        <form wire:submit="save" class="p-2 mb-2 app-card">

            <input type="text" wire:model="name" class="form-control form-control-sm mb-2"
                placeholder="List name" autofocus>
            @error('name') <div class="small text-danger mb-2">{{ $message }}</div> @enderror

            <input type="color" wire:model="color" class="form-control form-control-sm form-control-color mb-2">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm app-btn-primary flex-grow-1" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Save</span>
                    <span wire:loading wire:target="save">
                        <span class="spinner-border spinner-border-sm"></span>
                    </span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary"
                    wire:click="$set('showForm', false)">Cancel</button>
            </div>
        </form>
    @endif
    <nav class="d-flex flex-column gap-1 pb-2">

        <a href="#" wire:click.prevent="$dispatch('list-selected', { uuid: null })"
            class="app-sidebar-item {{ is_null($activeListUuid) ? 'active' : '' }}">
            <span class="app-dot bg-secondary"></span>
            <span>All Lists</span>
        </a>

        @foreach ($lists as $list)
            <div class="d-flex align-items-center app-sidebar-item p-0 {{ $activeListUuid === $list->uuid ? 'active' : '' }}">

                <a href="#" wire:click.prevent="$dispatch('list-selected', { uuid: '{{ $list->uuid }}' })"
                    class="d-flex align-items-center gap-2 flex-grow-1 min-width-0 text-decoration-none text-reset px-2 py-1">
                    <span class="app-dot flex-shrink-0" style="background-color: {{ $list->color ?? '#6c757d' }}"></span>
                    <span class="flex-grow-1 text-truncate">{{ $list->name }}</span>
                    @if ($list->todos_count > 0)
                        <span class="badge bg-body-tertiary text-muted flex-shrink-0">{{ $list->todos_count }}</span>
                    @endif
                </a>

                @unless ($list->is_default)
                    <div class="dropdown">
                        <button class="btn btn-sm app-icon-btn" data-bs-toggle="dropdown" title="List options">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <button type="button" class="dropdown-item" wire:click="startEdit({{ $list->id }})">
                                    Rename
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item text-danger"
                                    wire:click="delete({{ $list->id }})">
                                    Delete
                                </button>
                            </li>
                        </ul>
                    </div>
                @endunless

            </div>
        @endforeach

    </nav>

</div>
