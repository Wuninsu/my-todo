<div wire:poll.30s>

    <div class="dropdown">

        <button class="btn app-icon-btn position-relative" data-bs-toggle="dropdown" data-bs-auto-close="outside" title="Alerts" wire:ignore.self>
            <i class="bi bi-bell"></i>
            @if ($unreadCount > 0)
                <span class="app-alert-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
            @endif
        </button>

        <div class="dropdown-menu dropdown-menu-end app-alert-dropdown" wire:ignore.self>

            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                <h6 class="fw-bold mb-0">Alerts</h6>
                @if ($unreadCount > 0)
                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" wire:click="markAllAsRead">
                        Mark all read
                    </button>
                @endif
            </div>

            <div class="app-alert-list">
                @forelse ($notifications as $notification)
                    <button type="button" wire:key="alert-{{ $notification->id }}"
                        wire:click="openTodo('{{ $notification->id }}', {{ $notification->data['todo_id'] }})"
                        class="app-alert-item {{ is_null($notification->read_at) ? 'unread' : '' }}">

                        <span class="app-alert-icon {{ $notification->data['kind'] }}">
                            <i class="bi {{ match ($notification->data['kind']) {
                                'overdue' => 'bi-exclamation-triangle-fill',
                                'due_today' => 'bi-calendar-event-fill',
                                default => 'bi-alarm-fill',
                            } }}"></i>
                        </span>

                        <span class="flex-grow-1 text-start min-width-0">
                            <span class="d-block text-truncate fw-semibold">{{ $notification->data['todo_title'] }}</span>
                            <span class="d-block small text-muted text-truncate">{{ $notification->data['message'] }}</span>
                            <span class="d-block small text-muted">{{ $notification->created_at->diffForHumans() }}</span>
                        </span>

                        @if (is_null($notification->read_at))
                            <span class="app-dot flex-shrink-0" style="background-color: var(--app-primary)"></span>
                        @endif

                    </button>
                @empty
                    <div class="text-center text-muted py-4 small">
                        <i class="bi bi-bell-slash fs-4 d-block mb-2"></i>
                        No alerts yet.
                    </div>
                @endforelse
            </div>

            @if ($notifications->isNotEmpty())
                <div class="border-top px-3 py-2 text-center">
                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-danger" wire:click="clearAll">
                        Clear all
                    </button>
                </div>
            @endif

        </div>

    </div>

</div>
