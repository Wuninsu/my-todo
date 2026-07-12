<div>

    {{-- STATS --}}
    <div class="row g-4 mb-4">

        <div class="col-6 col-xl-3">
            <div class="app-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="fw-bold mb-0">{{ $totalUsers }}</h3>
                    <div class="app-stat-icon"><i class="bi bi-people"></i></div>
                </div>
                <p class="text-muted mb-0 app-stat-desc">Users <span class="small">({{ $totalAdmins }}
                        admin{{ $totalAdmins === 1 ? '' : 's' }})</span></p>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="app-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="fw-bold mb-0">{{ $totalTodos }}</h3>
                    <div class="app-stat-icon success"><i class="bi bi-check2-square"></i></div>
                </div>
                <p class="text-muted mb-0 app-stat-desc">Todos <span class="small">({{ $completedTodos }} completed)</span></p>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="app-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="fw-bold mb-0">{{ $totalDevices }}</h3>
                    <div class="app-stat-icon info"><i class="bi bi-phone"></i></div>
                </div>
                <p class="text-muted mb-0 app-stat-desc">Devices <span class="small">({{ $activeDevices }} active/30d)</span></p>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="app-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="fw-bold mb-0">{{ $recentSyncLogs->count() }}</h3>
                    <div class="app-stat-icon warning"><i class="bi bi-arrow-repeat"></i></div>
                </div>
                <p class="text-muted mb-0 app-stat-desc">Recent sync attempts</p>
            </div>
        </div>

    </div>

    {{-- SYNC LOGS --}}
    <div class="app-card overflow-hidden">

        <div class="p-4 pb-0">
            <h5 class="fw-bold mb-0">Recent Sync Activity</h5>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 app-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Device</th>
                        <th>Status</th>
                        <th>Uploaded</th>
                        <th>Downloaded</th>
                        <th>Conflicts</th>
                        <th>When</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentSyncLogs as $log)
                        <tr wire:key="sync-log-{{ $log->id }}">
                            <td>{{ $log->user?->name ?? 'Unknown' }}</td>
                            <td>{{ $log->device?->device_name ?? 'Unknown' }}</td>
                            <td>
                                <span
                                    class="badge {{ match ($log->status) {
                                        'success' => 'bg-success-subtle text-success',
                                        'partial' => 'bg-warning-subtle text-warning',
                                        default => 'bg-danger-subtle text-danger',
                                    } }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td>{{ $log->uploaded }}</td>
                            <td>{{ $log->downloaded }}</td>
                            <td>{{ $log->conflicts }}</td>
                            <td class="small text-muted">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-arrow-repeat fs-1 d-block mb-2"></i>
                                    No sync activity yet — the sync engine hasn't shipped (Phase 5).
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
