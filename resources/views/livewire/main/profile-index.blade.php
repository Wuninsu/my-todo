<div wire:keydown.escape.window="cancelEditing(); $set('changingPassword', false)">

    @php
        $user = auth()->user();
    @endphp

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">

        <div class="d-flex align-items-center gap-3">

            {{-- AVATAR --}}
            @if ($user->avatar)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                    class="app-user-profile-avatar" style="object-fit: cover;">
            @else
                <div class="app-user-profile-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif

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

        {{-- ACTION --}}
        <div>
            <button type="button" class="btn app-btn-primary rounded-4" wire:click="startEditing">
                <i class="bi bi-pencil-square"></i>
                Edit Profile
            </button>
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
    <div class="row g-4 mb-4">

        {{-- PROFILE INFO / EDIT FORM --}}
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

                            Current Version

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

    {{-- PASSWORD + DEVICES --}}
    <div class="row g-4">

        {{-- PASSWORD --}}
        <div class="col-xl-4">

            <div class="app-card p-4 h-100">

                <h5 class="fw-bold mb-4">Password</h5>

                <p class="text-muted mb-3">Change your account password.</p>

                <button type="button" class="btn app-btn-primary rounded-4" wire:click="$set('changingPassword', true)">
                    <i class="bi bi-key"></i>
                    Change Password
                </button>

            </div>

        </div>

        {{-- DEVICES --}}
        <div class="col-xl-8">

            <div class="app-card p-4 h-100">

                <h5 class="fw-bold mb-4">Connected Devices</h5>

                @forelse ($devices as $device)
                    <div class="d-flex align-items-center justify-content-between gap-3 py-2 {{ ! $loop->last ? 'border-bottom' : '' }}"
                        wire:key="device-{{ $device->id }}">

                        <div class="min-width-0">
                            <div class="fw-semibold text-break">{{ $device->device_name }}</div>
                            <div class="small text-muted">
                                {{ $device->platform ?? 'Unknown platform' }} &middot;
                                last seen {{ $device->last_seen_at?->diffForHumans() ?? 'never' }}
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" wire:click="revokeDevice({{ $device->id }})">
                            Revoke
                        </button>

                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-phone fs-1 d-block mb-2"></i>
                        No devices connected yet. Device sync is coming in a later phase.
                    </div>
                @endforelse

            </div>

        </div>

    </div>

    {{-- EDIT PROFILE MODAL --}}
    @if ($editing)
        <div class="app-modal-backdrop" wire:click.self="cancelEditing">
            <div class="app-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="editProfileModalTitle">

                <form wire:submit="save" class="d-flex flex-column min-height-0">

                    <div class="app-modal-header">
                        <h5 class="app-modal-title" id="editProfileModalTitle">Edit Profile</h5>
                        <button type="button" class="btn-close" wire:click="cancelEditing"></button>
                    </div>

                    <div class="app-modal-body d-flex flex-column gap-3">

                        <div>
                            <label class="form-label">Full Name</label>
                            <input type="text" wire:model="name" class="form-control app-input">
                            @error('name') <div class="small text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label">Email Address</label>
                            <input type="email" wire:model="email" class="form-control app-input">
                            @error('email') <div class="small text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label">Timezone</label>
                            <input type="text" wire:model="timezone" class="form-control app-input" placeholder="UTC">
                            @error('timezone') <div class="small text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label">Theme</label>
                            <select wire:model="theme" class="form-select app-input">
                                <option value="light">Light</option>
                                <option value="dark">Dark</option>
                            </select>
                            @error('theme') <div class="small text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label">Avatar</label>
                            <input type="file" wire:model="avatar" class="form-control app-input" accept="image/*">
                            <div wire:loading wire:target="avatar" class="small text-muted mt-1">Uploading...</div>
                            @error('avatar') <div class="small text-danger">{{ $message }}</div> @enderror
                            @if ($avatar)
                                <img src="{{ $avatar->temporaryUrl() }}" class="rounded-circle mt-2" width="60" height="60" style="object-fit: cover;">
                            @endif
                        </div>

                    </div>

                    <div class="app-modal-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-4" wire:click="cancelEditing">Cancel</button>
                        <button type="submit" class="btn app-btn-primary rounded-4" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save">Save</span>
                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm"></span>
                            </span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif

    {{-- CHANGE PASSWORD MODAL --}}
    @if ($changingPassword)
        <div class="app-modal-backdrop" wire:click.self="$set('changingPassword', false)">
            <div class="app-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="changePasswordModalTitle">

                <form wire:submit="changePassword" class="d-flex flex-column min-height-0">

                    <div class="app-modal-header">
                        <h5 class="app-modal-title" id="changePasswordModalTitle">Change Password</h5>
                        <button type="button" class="btn-close" wire:click="$set('changingPassword', false)"></button>
                    </div>

                    <div class="app-modal-body d-flex flex-column gap-3">

                        <div>
                            <label class="form-label">Current Password</label>
                            <input type="password" wire:model="current_password" class="form-control app-input">
                            @error('current_password') <div class="small text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label">New Password</label>
                            <input type="password" wire:model="password" class="form-control app-input">
                            @error('password') <div class="small text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" wire:model="password_confirmation" class="form-control app-input">
                        </div>

                    </div>

                    <div class="app-modal-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-4"
                            wire:click="$set('changingPassword', false)">Cancel</button>
                        <button type="submit" class="btn app-btn-primary rounded-4" wire:loading.attr="disabled" wire:target="changePassword">
                            <span wire:loading.remove wire:target="changePassword">Update Password</span>
                            <span wire:loading wire:target="changePassword">
                                <span class="spinner-border spinner-border-sm"></span>
                            </span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif

</div>
