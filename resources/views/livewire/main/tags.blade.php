<div class="app-page-fill">

    <div class="d-flex justify-content-end mb-4 flex-shrink-0">
        <button type="button" class="btn app-btn-primary rounded-4" wire:click="openCreate">
            <i class="bi bi-plus-lg"></i>
            New Tag
        </button>
    </div>

    @if ($showForm)
        <div class="app-card p-2 mb-4 flex-shrink-0">

            <h5 class="fw-bold mb-3">{{ $editingTagId ? 'Rename Tag' : 'New Tag' }}</h5>

            <form wire:submit="save" class="d-flex flex-wrap align-items-end gap-3">

                <div class="flex-grow-1" style="min-width: 200px;">
                    <label class="form-label">Name</label>
                    <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" autofocus>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Color</label>
                    <input type="color" wire:model="color" class="form-control form-control-color">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn app-btn-primary rounded-4" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">Save</span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm"></span>
                        </span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-4" wire:click="cancel">Cancel</button>
                </div>

            </form>

        </div>
    @endif

    <div class="app-card flex-grow-1 d-flex flex-column overflow-hidden">

        <div class="table-responsive flex-grow-1 overflow-auto">
            <table class="table align-middle mb-0 app-table">

                <thead>
                    <tr>
                        <th>Tag</th>
                        <th>Todos</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($tags as $tag)
                        <tr wire:key="tag-{{ $tag->id }}">

                            <td>
                                <span class="app-dot" style="background-color: {{ $tag->color ?? '#6366f1' }}"></span>
                                #{{ $tag->name }}
                                @if ($tag->isSystem())
                                    <span class="badge bg-body-tertiary text-muted ms-1">
                                        <i class="bi bi-shield-lock"></i>
                                        System
                                    </span>
                                @endif
                            </td>

                            <td>{{ $tag->todos_count }}</td>

                            <td class="text-end">
                                @if ($tag->isSystem())
                                    <span class="small text-muted" title="System tags cannot be edited or deleted">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                @else
                                    <div class="dropdown">
                                        <button class="btn app-icon-btn btn-sm" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button type="button" class="dropdown-item" wire:click="startEdit({{ $tag->id }})">
                                                    <i class="bi bi-pencil"></i>
                                                    Rename
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item text-danger"
                                                    wire:click="delete({{ $tag->id }})">
                                                    <i class="bi bi-trash"></i>
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-tags fs-1 d-block mb-2"></i>
                                    No tags yet. Tags are created automatically when you add them to a todo, or you can create one here.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>

</div>
