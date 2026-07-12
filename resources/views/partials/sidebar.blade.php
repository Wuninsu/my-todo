@php
    $activeFilter = request()->routeIs('dashboard') ? request()->query('filter') : null;
@endphp

<div class="d-flex flex-column h-100">

    {{-- LOGO (fixed at top) --}}
    <div class="p-4 flex-shrink-0">

        <h4 class="fw-bold mb-0">
            TodoFlow
        </h4>

    </div>

    {{-- SCROLLABLE: SEARCH + NAVIGATION --}}
    <div class="flex-grow-1 overflow-auto min-vh-0">

        {{-- SEARCH --}}
        <div class="px-3 mb-4">

            <div class="position-relative">

                <i class="bi bi-search app-search-icon"></i>

                <input type="text" class="form-control app-input ps-5" placeholder="Search todos... (press /)"
                    data-global-search onchange="window.Livewire.dispatch('search-changed', { term: this.value })">

            </div>

        </div>

        {{-- NAVIGATION --}}
        <div class="px-3">

            {{-- WORKSPACE --}}
            <div class="small text-uppercase text-muted fw-semibold mb-2">
                Workspace
            </div>

            <nav class="d-flex flex-column gap-1">

                <a href="{{ route('dashboard') }}" wire:navigate
                    class="app-sidebar-item {{ request()->routeIs('dashboard') && !$activeFilter ? 'active' : '' }}">

                    <i class="bi bi-grid"></i>

                    <span>Dashboard</span>

                </a>

                <a href="{{ route('tags') }}" wire:navigate
                    class="app-sidebar-item {{ request()->routeIs('tags') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i>
                    <span>Tags</span>
                </a>

                <a href="{{ route('trash') }}" wire:navigate
                    class="app-sidebar-item {{ request()->routeIs('trash') ? 'active' : '' }}">
                    <i class="bi bi-trash"></i>
                    <span>Trash</span>
                </a>

                @if (auth()->user()?->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                        class="app-sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Admin Overview</span>
                    </a>

                    <a href="{{ route('admin.users') }}" wire:navigate
                        class="app-sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                    </a>
                @endif

                <a href="{{ route('dashboard', ['filter' => 'today']) }}" wire:navigate
                    class="app-sidebar-item {{ $activeFilter === 'today' ? 'active' : '' }}">

                    <i class="bi bi-sun"></i>

                    <span>Today</span>

                </a>

                <a href="{{ route('dashboard', ['filter' => 'upcoming']) }}" wire:navigate
                    class="app-sidebar-item {{ $activeFilter === 'upcoming' ? 'active' : '' }}">

                    <i class="bi bi-calendar-event"></i>

                    <span>Upcoming</span>

                </a>

                <a href="{{ route('dashboard', ['filter' => 'completed']) }}" wire:navigate
                    class="app-sidebar-item {{ $activeFilter === 'completed' ? 'active' : '' }}">

                    <i class="bi bi-check2-circle"></i>

                    <span>Completed</span>

                </a>

                <a href="{{ route('dashboard', ['filter' => 'favorites']) }}" wire:navigate
                    class="app-sidebar-item {{ $activeFilter === 'favorites' ? 'active' : '' }}">

                    <i class="bi bi-star"></i>

                    <span>Favorites</span>

                </a>

            </nav>

            {{-- LISTS --}}
            <livewire:main.sidebar-lists :key="'sidebar-lists-' . ($variant ?? 'default')" />

        </div>

    </div>

    {{-- FOOTER --}}
    <div class="p-2 border-top sidebar-footer mt-auto">

        <a href="{{ route('dashboard', ['new' => 1]) }}" wire:navigate class="btn app-btn-primary w-100 rounded-4">
            <i class="bi bi-plus-lg"></i>
            New Todo
        </a>
    </div>

</div>
