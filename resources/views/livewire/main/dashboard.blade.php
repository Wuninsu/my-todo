@php
    $heading = match (true) {
        !is_null($filter) => ucfirst($filter),
        !is_null($activeListUuid) => optional($lists->firstWhere('uuid', $activeListUuid))->name ?? 'List',
        default => 'All Todos',
    };

    $emptyMessage = match (true) {
        $search !== '' => "No todos match \"{$search}\".",
        $filter === 'favorites' => 'No favorites yet — star a todo to pin it here.',
        $filter === 'completed' => 'Nothing completed yet.',
        $filter === 'today' => 'Nothing due today.',
        $filter === 'upcoming' => 'Nothing coming up.',
        $priorityFilter || $tagFilter => 'No todos match these filters.',
        !is_null($activeListUuid) => 'This list is empty. Add your first todo above.',
        default => 'Nothing here yet. Add your first todo above.',
    };
@endphp

<div class="d-flex flex-column gap-3" wire:keydown.escape.window="$set('showTodoModal', false)">

    {{-- HEADING + QUICK ADD --}}
    <div class="d-flex flex-column flex-md-row gap-2 align-items-md-center justify-content-between">

        <h5 class="fw-bold mb-0">{{ $heading }}</h5>

        <form wire:submit="quickAdd" class="d-flex gap-2">
            <input type="text" wire:model="quickTitle" class="form-control app-input"
                placeholder="Quick add a todo and press enter...">
            <button type="submit" class="btn app-btn-primary text-nowrap">
                <i class="bi bi-plus-lg"></i> Add
            </button>
        </form>

    </div>

    {{-- FILTERS --}}
    <div class="d-flex flex-wrap gap-2">

        <select wire:model.live="priorityFilter" class="form-select form-select-sm w-auto">
            <option value="">All priorities</option>
            <option value="low">Low priority</option>
            <option value="medium">Medium priority</option>
            <option value="high">High priority</option>
        </select>

        @if ($allTags->isNotEmpty())
            <select wire:model.live="tagFilter" class="form-select form-select-sm w-auto">
                <option value="">All tags</option>
                @foreach ($allTags as $tag)
                    <option value="{{ $tag->id }}">#{{ $tag->name }}</option>
                @endforeach
            </select>
        @endif

        @if ($priorityFilter || $tagFilter)
            <button type="button" class="btn btn-sm btn-outline-secondary"
                wire:click="$set('priorityFilter', null); $set('tagFilter', null)">
                Clear filters
            </button>
        @endif

    </div>

    {{-- TODOS --}}
    @forelse ($todos as $index => $todo)
        @include('partials.todo-card', [
            'todo' => $todo,
            'canMoveUp' => $index > 0,
            'canMoveDown' => $index < $todos->count() - 1,
        ])
    @empty
        <div class="app-card p-4 text-center text-muted">
            <i class="bi bi-check2-circle fs-1 d-block mb-2"></i>
            {{ $emptyMessage }}
        </div>
    @endforelse

    {{-- CREATE / EDIT TODO MODAL --}}
    @if ($showTodoModal)
        <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.5)" wire:click.self="$set('showTodoModal', false)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <form wire:submit="saveTodo">

                        <div class="modal-header">
                            <h5 class="modal-title">{{ $todoId ? 'Edit Todo' : 'New Todo' }}</h5>
                            <button type="button" class="btn-close" wire:click="$set('showTodoModal', false)"></button>
                        </div>

                        <div class="modal-body d-flex flex-column gap-3">

                            <div>
                                <label class="form-label">Title</label>
                                <input type="text" wire:model="title" class="form-control" autofocus>
                                @error('title') <div class="small text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div>
                                <label class="form-label">Description</label>
                                <textarea wire:model="description" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label">Priority</label>
                                    <select wire:model="priority" class="form-select">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Due date</label>
                                    <input type="date" wire:model="due_date" class="form-control">
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Reminder</label>
                                <input type="datetime-local" wire:model="reminder_at" class="form-control">
                            </div>

                            <div>
                                <label class="form-label">List</label>
                                <select wire:model="todo_list_id" class="form-select">
                                    <option value="">No list</option>
                                    @foreach ($lists as $list)
                                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="form-label">Tags</label>
                                <input type="text" wire:model="tagsInput" class="form-control"
                                    placeholder="comma, separated, tags">
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                wire:click="$set('showTodoModal', false)">Cancel</button>
                            <button type="submit" class="btn app-btn-primary">Save</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    @endif

</div>
