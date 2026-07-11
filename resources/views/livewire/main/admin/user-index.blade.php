<div>

    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">

        <div>

            <h4 class="fw-bold mb-1">
                Users
            </h4>

            <p class="text-muted mb-0">
                Manage application users.
            </p>

        </div>

    </div>

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="alert alert-success rounded-4 border-0 mb-4">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger rounded-4 border-0 mb-4">{{ session('error') }}</div>
    @endif

    {{-- FILTERS --}}
    <div class="app-card p-3 mb-4">

        <div class="row g-3">

            {{-- SEARCH --}}
            <div class="col-lg-6">

                <div class="position-relative">

                    <i class="bi bi-search app-input-icon"></i>

                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control app-input ps-5"
                        placeholder="Search users...">

                </div>

            </div>

            {{-- ROLE --}}
            <div class="col-lg-3">

                <select wire:model.live="role" class="form-select app-input">

                    <option value="">
                        All Roles
                    </option>

                    <option value="admin">
                        Admin
                    </option>

                    <option value="user">
                        User
                    </option>

                </select>

            </div>

            {{-- TRASHED --}}
            <div class="col-lg-3 d-flex align-items-center">

                <div class="form-check">
                    <input type="checkbox" wire:model.live="showTrashed" class="form-check-input" id="showTrashed">
                    <label class="form-check-label" for="showTrashed">Show deactivated</label>
                </div>

            </div>

        </div>

    </div>

    {{-- USERS TABLE --}}
    <div class="app-card overflow-hidden">

        <div class="table-responsive">

            <table class="table align-middle mb-0 app-table">

                <thead>

                    <tr>

                        <th>
                            User
                        </th>

                        <th>
                            Role
                        </th>

                        <th>
                            Theme
                        </th>

                        <th>
                            Sync
                        </th>

                        <th>
                            Joined
                        </th>

                        <th class="text-end">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($users as $user)

                    <tr wire:key="user-{{ $user->id }}">

                        {{-- USER --}}
                        <td>

                            <div class="d-flex align-items-center gap-3">

                                <div class="app-user-avatar">

                                    {{ strtoupper(substr($user->name, 0, 1)) }}

                                </div>

                                <div>

                                    <div class="fw-semibold">

                                        {{ $user->name }}

                                    </div>

                                    <div class="small text-muted">

                                        {{ $user->email }}

                                    </div>

                                </div>

                            </div>

                        </td>

                        {{-- ROLE --}}
                        <td>

                            <span class="badge app-role-badge">

                                {{ ucfirst($user->role) }}

                            </span>

                        </td>

                        {{-- THEME --}}
                        <td>

                            <span class="small text-muted">

                                {{ ucfirst($user->theme) }}

                            </span>

                        </td>

                        {{-- SYNC --}}
                        <td>

                            @if ($user->is_synced)

                            <span class="badge bg-success-subtle text-success">

                                Synced

                            </span>

                            @else

                            <span class="badge bg-warning-subtle text-warning">

                                Pending

                            </span>

                            @endif

                        </td>

                        {{-- CREATED --}}
                        <td>

                            <span class="small text-muted">

                                {{ $user->created_at->diffForHumans() }}

                            </span>

                        </td>

                        {{-- ACTIONS --}}
                        <td class="text-end">

                            <div class="dropdown">

                                <button class="btn app-icon-btn btn-sm" data-bs-toggle="dropdown">

                                    <i class="bi bi-three-dots"></i>

                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">

                                    @if ($user->trashed())
                                        <li>
                                            <button type="button" class="dropdown-item"
                                                wire:click="restore({{ $user->id }})">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                                Restore
                                            </button>
                                        </li>
                                    @else
                                        <li>
                                            <a href="{{ route('admin.users.view', $user) }}" wire:navigate class="dropdown-item">
                                                <i class="bi bi-eye"></i>
                                                View
                                            </a>
                                        </li>

                                        <li>
                                            <a href="{{ route('admin.users.edit', $user) }}" wire:navigate class="dropdown-item">
                                                <i class="bi bi-pencil"></i>
                                                Edit
                                            </a>
                                        </li>

                                        <li>
                                            <button type="button" class="dropdown-item text-danger"
                                                wire:click="delete({{ $user->id }})"
                                                wire:confirm="Deactivate this user? Their data is kept and this can be undone.">
                                                <i class="bi bi-trash"></i>
                                                Delete
                                            </button>
                                        </li>
                                    @endif

                                </ul>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="6">

                            <div class="text-center py-5">

                                <div class="mb-3">

                                    <i class="bi bi-people fs-1 text-muted"></i>

                                </div>

                                <h6 class="fw-semibold">
                                    No users found
                                </h6>

                                <p class="text-muted mb-0">
                                    Try adjusting your search filters.
                                </p>

                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- PAGINATION --}}
    <div class="mt-4">

        {{ $users->links() }}

    </div>

</div>