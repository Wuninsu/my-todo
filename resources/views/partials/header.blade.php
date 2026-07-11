<header class="app-header app-glass">

    @php
        [$headerTitle, $headerSubtitle] = match (true) {
            request()->routeIs('admin.dashboard') => ['Admin Overview', 'Application-wide stats and sync activity'],
            request()->routeIs('admin.users.edit') => ['Edit User', 'Update user account information'],
            request()->routeIs('admin.users.view') => ['User Details', 'View user account details'],
            request()->routeIs('admin.users') => ['Users', 'Manage application users'],
            request()->routeIs('profile') => ['My Profile', 'Manage your account settings'],
            default => ['Dashboard', 'Manage your tasks efficiently'],
        };
    @endphp

    <div class="d-flex align-items-center justify-content-between">

        {{-- LEFT --}}
        <div class="d-flex align-items-center gap-3">

            {{-- MOBILE MENU --}}
            <button class="btn d-lg-none app-icon-btn" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">

                <i class="bi bi-list"></i>

            </button>

            <div class="d-none d-lg-block">

                <h5 class="fw-bold mb-0">
                    {{ $headerTitle }}
                </h5>

                <div class="small">
                    {{ $headerSubtitle }}
                </div>

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="d-flex align-items-center gap-2">

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
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn app-icon-btn" title="Log out">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>

        </div>

    </div>

</header>