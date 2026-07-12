<div>

    {{-- FORM --}}
    <form wire:submit="update">

        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-xl-8">

                <div class="app-card p-4">

                    <h5 class="fw-bold mb-4">

                        Basic Information

                    </h5>

                    <div class="row g-4">

                        {{-- NAME --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">

                                Full Name

                            </label>

                            <div class="position-relative">

                                <i class="bi bi-person app-input-icon"></i>

                                <input
                                    type="text"
                                    wire:model="name"
                                    class="form-control app-input ps-5 @error('name') is-invalid @enderror"
                                    placeholder="Enter full name">

                            </div>

                            @error('name')

                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        {{-- EMAIL --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">

                                Email Address

                            </label>

                            <div class="position-relative">

                                <i class="bi bi-envelope app-input-icon"></i>

                                <input
                                    type="email"
                                    wire:model="email"
                                    class="form-control app-input ps-5 @error('email') is-invalid @enderror"
                                    placeholder="Enter email address">

                            </div>

                            @error('email')

                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        {{-- ROLE --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">

                                Role

                            </label>

                            <select
                                wire:model="role"
                                class="form-select app-input @error('role') is-invalid @enderror">

                                <option value="admin">
                                    Admin
                                </option>

                                <option value="user">
                                    User
                                </option>

                            </select>

                            @error('role')

                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        {{-- THEME --}}
                        <div class="col-md-6">

                            <label class="form-label fw-semibold">

                                Theme

                            </label>

                            <select
                                wire:model="theme"
                                class="form-select app-input @error('theme') is-invalid @enderror">

                                <option value="light">
                                    Light
                                </option>

                                <option value="dark">
                                    Dark
                                </option>

                            </select>

                            @error('theme')

                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                        {{-- TIMEZONE --}}
                        <div class="col-12">

                            <label class="form-label fw-semibold">

                                Timezone

                            </label>

                            <input
                                type="text"
                                wire:model="timezone"
                                class="form-control app-input @error('timezone') is-invalid @enderror"
                                placeholder="UTC">

                            @error('timezone')

                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                    </div>

                </div>

            </div>

            {{-- RIGHT --}}
            <div class="col-xl-4">

                <div class="app-card p-4">

                    <h5 class="fw-bold mb-4">

                        Sync Information

                    </h5>

                    <div class="d-flex flex-column gap-4">

                        <div>

                            <div class="small text-muted mb-1">

                                UUID

                            </div>

                            <div class="fw-semibold text-break">

                                {{ $user->uuid }}

                            </div>

                        </div>

                        <div>

                            <div class="small text-muted mb-1">

                                Current Version

                            </div>

                            <div class="fw-semibold">

                                v{{ $user->version }}

                            </div>

                        </div>

                        <div>

                            <div class="small text-muted mb-1">

                                Sync Status

                            </div>

                            <div>

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

                        </div>

                    </div>

                </div>

                {{-- ACTIONS --}}
                <div class="app-card p-4 mt-4">

                    <div class="d-grid gap-3">

                        <button
                            type="submit"
                            class="btn app-btn-primary rounded-4">

                            <span wire:loading.remove>

                                <i class="bi bi-check2-circle"></i>

                                Save Changes

                            </span>

                            <span wire:loading>

                                <span class="spinner-border spinner-border-sm me-2"></span>

                                Updating...

                            </span>

                        </button>

                        <a
                            href="{{ route('admin.users.view', $user) }}"
                            wire:navigate
                            class="btn btn-light rounded-4">
                            Cancel

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>