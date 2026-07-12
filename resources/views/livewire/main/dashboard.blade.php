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
                placeholder="Quick add a todo and press enter..." wire:loading.attr="disabled" wire:target="quickAdd">
            <button type="submit" class="btn app-btn-primary text-nowrap" wire:loading.attr="disabled" wire:target="quickAdd">
                <span wire:loading.remove wire:target="quickAdd"><i class="bi bi-plus-lg"></i> Add</span>
                <span wire:loading wire:target="quickAdd">
                    <span class="spinner-border spinner-border-sm"></span>
                </span>
            </button>
        </form>

    </div>

    {{-- FILTERS --}}
    <div class="app-filter-bar d-flex flex-wrap align-items-center gap-3">

        <span class="app-filter-label">
            <i class="bi bi-funnel"></i> Priority
        </span>

        <div class="d-flex flex-wrap gap-2">

            <button type="button" wire:click="$set('priorityFilter', null)"
                class="app-filter-pill {{ ! $priorityFilter ? 'active' : '' }}">
                All <span class="app-filter-count">{{ $priorityCounts->sum() }}</span>
            </button>

            @foreach ([
                'low' => ['Low', '#22c55e'],
                'medium' => ['Medium', '#f59e0b'],
                'high' => ['High', '#ef4444'],
            ] as $value => [$label, $color])
                <button type="button" wire:click="$set('priorityFilter', '{{ $value }}')"
                    class="app-filter-pill {{ $priorityFilter === $value ? 'active' : '' }}">
                    <span class="app-dot" style="background-color: {{ $color }}"></span>
                    {{ $label }} <span class="app-filter-count">{{ $priorityCounts[$value] }}</span>
                </button>
            @endforeach

        </div>

        @if ($allTags->isNotEmpty())
            <span class="app-filter-divider d-none d-sm-block"></span>

            <select wire:model.live="tagFilter" class="form-select form-select-sm app-filter-select w-auto">
                <option value="">All tags</option>
                @foreach ($allTags as $tag)
                    <option value="{{ $tag->id }}">#{{ $tag->name }}</option>
                @endforeach
            </select>
        @endif

        @if ($priorityFilter || $tagFilter)
            <button type="button" class="app-filter-clear ms-auto" wire:click="$set('priorityFilter', null); $set('tagFilter', null)">
                <i class="bi bi-x-circle"></i> Clear filters
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
        <div class="app-modal-backdrop" wire:click.self="$set('showTodoModal', false)">
            <div class="app-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="todoModalTitle">

                <form wire:submit="saveTodo" class="d-flex flex-column min-height-0">

                    <div class="app-modal-header">
                        <h5 class="app-modal-title" id="todoModalTitle">{{ $todoId ? 'Edit Todo' : 'New Todo' }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('showTodoModal', false)"></button>
                    </div>

                    <div class="app-modal-body d-flex flex-column gap-3">

                        <div>
                            <label class="form-label">Title</label>
                            <input type="text" wire:model="title"
                                class="form-control app-input @error('title') is-invalid @enderror" autofocus>
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">Description</label>
                            <textarea wire:model="description" class="form-control app-input" rows="3"></textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Priority</label>
                                <select wire:model="priority" class="form-select app-input">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Due date</label>
                                <input type="date" wire:model="due_date" class="form-control app-input">
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Reminder</label>
                            <input type="datetime-local" wire:model="reminder_at" class="form-control app-input">
                        </div>

                        <div>
                            <label class="form-label">List</label>
                            <select wire:model="todo_list_id" class="form-select app-input">
                                <option value="">No list</option>
                                @foreach ($lists as $list)
                                    <option value="{{ $list->id }}">{{ $list->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Tags</label>
                            <input type="text" wire:model="tagsInput" class="form-control app-input"
                                placeholder="comma, separated, tags">
                        </div>

                    </div>

                    <div class="app-modal-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-4"
                            wire:click="$set('showTodoModal', false)">Cancel</button>
                        <button type="submit" class="btn app-btn-primary rounded-4" wire:loading.attr="disabled" wire:target="saveTodo">
                            <span wire:loading.remove wire:target="saveTodo">Save</span>
                            <span wire:loading wire:target="saveTodo">
                                <span class="spinner-border spinner-border-sm"></span>
                            </span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif

</div>
