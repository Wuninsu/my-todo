<div>

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">

        <div class="d-flex align-items-center gap-3">

            {{-- AVATAR --}}
            <div class="app-user-profile-avatar">

                {{ strtoupper(substr($user->name, 0, 1)) }}

            </div>

            {{-- INFO --}}
            <div>

                <h4 class="fw-bold mb-1">

                    {{ $user->name }}

                </h4>

                <div class="d-flex flex-wrap align-items-center gap-2">

                    <span class="text-muted">

                        {{ $user->email }}

                    </span>

                    <span class="badge app-role-badge">

                        {{ ucfirst($user->role) }}

                    </span>

                </div>

            </div>

        </div>

        {{-- ACTIONS --}}
        <div class="d-flex align-items-center gap-2">

            <a  wire:navigate href="{{ route('admin.users.edit', $user) }}" class="btn app-btn-primary rounded-4">
                <i class="bi bi-pencil-square"></i>
                Edit User
            </a>

        </div>

    </div>

    {{-- STATS --}}
    <div class="row g-4 mb-4">

        {{-- TODOS --}}
        <div class="col-6 col-xl-3">
            <div class="app-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="fw-bold mb-0">{{ $user->todos()->count() }}</h3>
                    <div class="app-stat-icon"><i class="bi bi-check2-square"></i></div>
                </div>
                <p class="text-muted mb-0 app-stat-desc">Total Todos</p>
            </div>
        </div>

        {{-- COMPLETED --}}
        <div class="col-6 col-xl-3">
            <div class="app-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="fw-bold mb-0">{{ $user->todos()->where('status', 'done')->count() }}</h3>
                    <div class="app-stat-icon success"><i class="bi bi-check-circle"></i></div>
                </div>
                <p class="text-muted mb-0 app-stat-desc">Completed Tasks</p>
            </div>
        </div>

        {{-- LISTS --}}
        <div class="col-6 col-xl-3">
            <div class="app-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="fw-bold mb-0">{{ $user->todoLists()->count() }}</h3>
                    <div class="app-stat-icon warning"><i class="bi bi-collection"></i></div>
                </div>
                <p class="text-muted mb-0 app-stat-desc">Todo Lists</p>
            </div>
        </div>

        {{-- DEVICES --}}
        <div class="col-6 col-xl-3">
            <div class="app-card p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="fw-bold mb-0">{{ $user->devices()->count() }}</h3>
                    <div class="app-stat-icon info"><i class="bi bi-phone"></i></div>
                </div>
                <p class="text-muted mb-0 app-stat-desc">Connected Devices</p>
            </div>
        </div>

    </div>

    {{-- DETAILS --}}
    <div class="row g-4">

        {{-- PROFILE --}}
        <div class="col-xl-4">

            <div class="app-card p-4 h-100">

                <h5 class="fw-bold mb-4">

                    Profile Information

                </h5>

                <div class="d-flex flex-column gap-4">

                    <div>

                        <div class="small text-muted mb-1">

                            Full Name

                        </div>

                        <div class="fw-semibold">

                            {{ $user->name }}

                        </div>

                    </div>

                    <div>

                        <div class="small text-muted mb-1">

                            Email Address

                        </div>

                        <div class="fw-semibold">

                            {{ $user->email }}

                        </div>

                    </div>

                    <div>

                        <div class="small text-muted mb-1">

                            Theme

                        </div>

                        <div class="fw-semibold">

                            {{ ucfirst($user->theme) }}

                        </div>

                    </div>

                    <div>

                        <div class="small text-muted mb-1">

                            Timezone

                        </div>

                        <div class="fw-semibold">

                            {{ $user->timezone }}

                        </div>

                    </div>

                    <div>

                        <div class="small text-muted mb-1">

                            Joined

                        </div>

                        <div class="fw-semibold">

                            {{ $user->created_at->format('M d, Y') }}

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- SYNC --}}
        <div class="col-xl-8">

            <div class="app-card p-4 h-100">

                <div class="d-flex align-items-center justify-content-between mb-4">

                    <h5 class="fw-bold mb-0">

                        Sync Information

                    </h5>

                    @if ($user->is_synced)

                        <span class="badge bg-success-subtle text-success">

                            Synced

                        </span>

                    @else

                        <span class="badge bg-warning-subtle text-warning">

                            Pending Sync

                        </span>

                    @endif

                </div>

                <div class="row g-4">

                    <div class="col-md-6">

                        <div class="small text-muted mb-1">

                            UUID

                        </div>

                        <div class="fw-semibold text-break">

                            {{ $user->uuid }}

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="small text-muted mb-1">

                            Device UUID

                        </div>

                        <div class="fw-semibold text-break">

                            {{ $user->device_uuid }}

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="small text-muted mb-1">

                            Version

                        </div>

                        <div class="fw-semibold">

                            v{{ $user->version }}

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="small text-muted mb-1">

                            Last Synced

                        </div>

                        <div class="fw-semibold">

                            {{ $user->last_synced_at?->diffForHumans() ?? 'Never synced' }}

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="small text-muted mb-1">

                            Client Updated

                        </div>

                        <div class="fw-semibold">

                            {{ $user->client_updated_at?->diffForHumans() ?? 'N/A' }}

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="small text-muted mb-1">

                            Email Verification

                        </div>

                        <div class="fw-semibold">

                            {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>