<div class="d-flex flex-column h-100">

    {{-- CONTENT GROUP --}}
    <div class="flex-grow-1 d-flex flex-column min-vh-0">

        {{-- LOGO --}}
        <div class="p-4">

            <h4 class="fw-bold mb-0">
                TodoFlow
            </h4>

        </div>

        {{-- SEARCH --}}
        <div class="px-3 mb-4">

            <div class="position-relative">

                <i class="bi bi-search app-search-icon"></i>

                <input type="text" class="form-control app-input ps-5" placeholder="Search todos... (press /)"
                    data-global-search
                    onchange="window.Livewire.dispatch('search-changed', { term: this.value })">

            </div>

        </div>

        {{-- NAVIGATION --}}
        <div class="px-3 flex-grow-1 overflow-auto">

            {{-- WORKSPACE --}}
            <div class="small text-uppercase text-muted fw-semibold mb-2">
                Workspace
            </div>

            <nav class="d-flex flex-column gap-1">

                <a href="{{ route('dashboard') }}" wire:navigate class="app-sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                    <i class="bi bi-grid"></i>

                    <span>Dashboard</span>

                </a>

                @if (auth()->user()?->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                        class="app-sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Admin Overview</span>
                    </a>

                    <a href="{{route('admin.users')}}" wire:navigate
                        class="app-sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                    </a>
                @endif

                <a href="#" onclick="event.preventDefault(); window.Livewire.dispatch('filter-selected', { filter: 'today' })" class="app-sidebar-item">

                    <i class="bi bi-sun"></i>

                    <span>Today</span>

                </a>

                <a href="#" onclick="event.preventDefault(); window.Livewire.dispatch('filter-selected', { filter: 'upcoming' })" class="app-sidebar-item">

                    <i class="bi bi-calendar-event"></i>

                    <span>Upcoming</span>

                </a>

                <a href="#" onclick="event.preventDefault(); window.Livewire.dispatch('filter-selected', { filter: 'completed' })" class="app-sidebar-item">

                    <i class="bi bi-check2-circle"></i>

                    <span>Completed</span>

                </a>

                <a href="#" onclick="event.preventDefault(); window.Livewire.dispatch('filter-selected', { filter: 'favorites' })" class="app-sidebar-item">

                    <i class="bi bi-star"></i>

                    <span>Favorites</span>

                </a>

            </nav>

            {{-- LISTS --}}
            <livewire:main.sidebar-lists :key="'sidebar-lists-'.($variant ?? 'default')" />

        </div>

    </div>

    {{-- FOOTER --}}
    <div class="p-3 border-top sidebar-footer mt-auto">

        <button type="button" class="btn app-btn-primary w-100 rounded-4"
            onclick="window.Livewire.dispatch('open-create-todo')">

            <i class="bi bi-plus-lg"></i>

            New Todo

        </button>

    </div>

</div>