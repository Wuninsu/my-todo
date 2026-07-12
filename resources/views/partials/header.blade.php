<header class="app-header app-glass">

    @php
        [$headerTitle, $headerSubtitle] = match (true) {
            request()->routeIs('admin.dashboard') => ['Admin Overview', 'Application-wide stats and sync activity'],
            request()->routeIs('admin.users.edit') => ['Edit User', 'Update user account information'],
            request()->routeIs('admin.users.view') => ['User Details', 'View user account details'],
            request()->routeIs('admin.users') => ['Users', 'Manage application users'],
            request()->routeIs('profile') => ['My Profile', 'Manage your account settings'],
            request()->routeIs('tags') => ['Tags', 'Organize your todos with reusable tags'],
            request()->routeIs('trash') => ['Trash', "Restore anything you've deleted, or remove it for good"],
            default => ['Dashboard', 'Manage your tasks efficiently'],
        };
    @endphp

    <div class="d-flex align-items-center justify-content-between gap-2">

        {{-- LEFT --}}
        <div class="d-flex align-items-center gap-3 min-width-0">

            {{-- MOBILE MENU --}}
            <button class="btn d-lg-none app-icon-btn flex-shrink-0" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">

                <i class="bi bi-list"></i>

            </button>

            <div class="min-width-0">

                <h5 class="fw-bold mb-0 text-truncate">
                    {{ $headerTitle }}
                </h5>

                <div class="small text-truncate d-none d-lg-block">
                    {{ $headerSubtitle }}
                </div>

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="d-flex align-items-center gap-2 flex-shrink-0">

            {{-- ALERTS --}}

            <livewire:main.alert-center />

            {{-- THEME --}}

            <livewire:main.theme-toggle />

            {{-- PROFILE --}}
            {{-- <button class="btn app-icon-btn">

                <i class="bi bi-person-circle"></i>

            </button> --}}

            <a href="{{ route('profile') }}" wire:navigate
                class="app-icon-btn">
                <i class="bi bi-person-circle"></i>
            </a>

            {{-- LOGOUT --}}
            <form method="POST" action="{{ route('logout') }}"
                onsubmit="this.querySelector('button').setAttribute('disabled', 'disabled'); this.querySelector('[data-logout-icon]').classList.add('d-none'); this.querySelector('[data-logout-spinner]').classList.remove('d-none');">
                @csrf
                <button type="submit" class="btn app-icon-btn" title="Log out">
                    <i class="bi bi-box-arrow-right" data-logout-icon></i>
                    <span class="spinner-border spinner-border-sm d-none" data-logout-spinner></span>
                </button>
            </form>

        </div>

    </div>

</header>