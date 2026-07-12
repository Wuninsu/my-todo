@php
    $statusIcon = match ($todo->status) {
        'doing' => 'bi-arrow-repeat',
        'done' => 'bi-check-circle-fill',
        default => 'bi-circle',
    };

    $statusBadgeClass = match ($todo->status) {
        'doing' => 'app-badge-doing',
        'done' => 'app-badge-done',
        default => 'app-badge-todo',
    };

    $priorityLabel = ucfirst($todo->priority).' Priority';

    $isOverdue = $todo->due_date
        && $todo->due_date->isPast()
        && ! $todo->due_date->isToday()
        && ! in_array($todo->status, ['done', 'archived']);

    $canMoveUp = $canMoveUp ?? false;
    $canMoveDown = $canMoveDown ?? false;
@endphp

<div class="app-card p-3 {{ $isOverdue ? 'border-danger' : '' }}" wire:key="todo-{{ $todo->id }}">

    <div class="d-flex align-items-start justify-content-between">

        {{-- LEFT --}}
        <div class="d-flex gap-3 min-width-0">

            {{-- REORDER + CHECKBOX --}}
            <div class="d-flex flex-column align-items-center gap-1">

                <button type="button" class="btn app-icon-btn p-0" style="font-size: .75rem"
                    wire:click="moveUp({{ $todo->id }})" title="Move up" @disabled(! $canMoveUp)>
                    <i class="bi bi-chevron-up"></i>
                </button>

                <button type="button" class="app-check-btn" wire:click="toggleStatus({{ $todo->id }})"
                    title="Cycle status">

                    <i class="bi {{ $statusIcon }}"></i>

                </button>

                <button type="button" class="btn app-icon-btn p-0" style="font-size: .75rem"
                    wire:click="moveDown({{ $todo->id }})" title="Move down" @disabled(! $canMoveDown)>
                    <i class="bi bi-chevron-down"></i>
                </button>

            </div>

            {{-- CONTENT --}}
            <div class="min-width-0">

                <h6 class="fw-semibold mb-1 text-break {{ $todo->status === 'done' ? 'text-decoration-line-through text-muted' : '' }}">
                    {{ $todo->title }}
                </h6>

                @if ($todo->description)
                    <p class="small mb-2">
                        {{ \Illuminate\Support\Str::limit($todo->description, 120) }}
                    </p>
                @endif

                <div class="d-flex flex-wrap gap-2">

                    <span class="badge {{ $statusBadgeClass }}">
                        {{ ucfirst($todo->status) }}
                    </span>

                    <span class="badge app-badge-priority">
                        {{ $priorityLabel }}
                    </span>

                    @if ($todo->list)
                        <span class="badge bg-body-tertiary text-muted">
                            <span class="app-dot" style="background-color: {{ $todo->list->color ?? '#6c757d' }}"></span>
                            {{ $todo->list->name }}
                        </span>
                    @endif

                    @if ($todo->due_date)
                        <span class="badge {{ $isOverdue ? 'bg-danger text-white' : 'bg-body-tertiary text-muted' }}">
                            <i class="bi {{ $isOverdue ? 'bi-exclamation-circle' : 'bi-calendar-event' }}"></i>
                            {{ $isOverdue ? 'Overdue' : ($todo->due_date->isToday() ? 'Today' : $todo->due_date->format('M j')) }}
                        </span>
                    @endif

                    @if ($todo->reminder_at)
                        <span class="badge bg-body-tertiary text-muted">
                            <i class="bi bi-bell"></i>
                            {{ $todo->reminder_at->format('M j, g:ia') }}
                        </span>
                    @endif

                    @foreach ($todo->tags as $tag)
                        <span class="badge bg-body-tertiary text-muted">#{{ $tag->name }}</span>
                    @endforeach

                </div>

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="d-flex align-items-center gap-1 flex-shrink-0">

            <button type="button" class="btn app-icon-btn" wire:click="toggleFavorite({{ $todo->id }})"
                title="Toggle favorite">
                <i class="bi {{ $todo->is_favorite ? 'bi-star-fill text-warning' : 'bi-star' }}"></i>
            </button>

            <div class="dropdown">

                <button class="btn app-icon-btn" data-bs-toggle="dropdown">

                    <i class="bi bi-three-dots"></i>

                </button>

                <ul class="dropdown-menu dropdown-menu-end">

                    <li>
                        <button type="button" class="dropdown-item" wire:click="editTodo({{ $todo->id }})">
                            Edit
                        </button>
                    </li>

                    <li>
                        <button type="button" class="dropdown-item text-danger"
                            wire:click="deleteTodo({{ $todo->id }})">
                            Delete
                        </button>
                    </li>

                </ul>

            </div>

        </div>

    </div>

</div>
