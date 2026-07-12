<div class="app-page-fill">

    {{-- MODEL FILTER --}}
    <div class="app-filter-bar d-flex flex-wrap align-items-center gap-2 flex-shrink-0 mb-3">

        <button type="button" wire:click="$set('model', null)"
            class="app-filter-pill {{ ! $model ? 'active' : '' }}">
            All <span class="app-filter-count">{{ $counts->sum() }}</span>
        </button>

        @foreach ([
            'todos' => ['Todos', 'bi-check2-square'],
            'lists' => ['Lists', 'bi-collection'],
            'tags' => ['Tags', 'bi-tags'],
            'devices' => ['Devices', 'bi-phone'],
        ] as $type => [$label, $icon])
            <button type="button" wire:click="$set('model', '{{ $type }}')"
                class="app-filter-pill {{ $model === $type ? 'active' : '' }}">
                <i class="bi {{ $icon }}"></i>
                {{ $label }} <span class="app-filter-count">{{ $counts[$type] }}</span>
            </button>
        @endforeach

    </div>

    {{-- ITEMS --}}
    <div class="d-flex flex-column gap-3 flex-grow-1 overflow-auto">
        @forelse ($items as $item)
            <div class="app-card p-3" wire:key="trash-{{ $item['type'] }}-{{ $item['id'] }}">

                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-3">

                    <div class="d-flex align-items-center gap-3 min-width-0">
                        <div class="app-stat-icon flex-shrink-0">
                            <i class="bi {{ $item['icon'] }}"></i>
                        </div>

                        <div class="min-width-0">
                            <h6 class="fw-semibold mb-1 text-break">{{ $item['label'] }}</h6>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <span class="badge bg-body-tertiary text-muted">{{ $item['meta'] }}</span>
                                <span class="small text-muted">Deleted {{ $item['deleted_at']->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            wire:click="restore('{{ $item['type'] }}', {{ $item['id'] }})">
                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="appConfirm({
                                title: 'Delete forever?',
                                message: 'This permanently deletes this {{ strtolower($item['meta']) }}. This cannot be undone.',
                                confirmText: 'Delete forever',
                                danger: true,
                                onConfirm: 'trash-force-delete-confirmed',
                                onConfirmParams: { type: '{{ $item['type'] }}', id: {{ $item['id'] }} },
                            })">
                            <i class="bi bi-trash3"></i> Delete forever
                        </button>
                    </div>

                </div>

            </div>
        @empty
            <div class="app-card p-4 text-center text-muted">
                <i class="bi bi-trash fs-1 d-block mb-2"></i>
                Trash is empty.
            </div>
        @endforelse
    </div>

</div>
