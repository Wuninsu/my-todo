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

                <input type="text" class="form-control app-input ps-5" placeholder="Search todos...">

            </div>

        </div>

        {{-- NAVIGATION --}}
        <div class="px-3 flex-grow-1 overflow-auto">

            {{-- WORKSPACE --}}
            <div class="small text-uppercase text-muted fw-semibold mb-2">
                Workspace
            </div>

            <nav class="d-flex flex-column gap-1">

                <a href="#" class="app-sidebar-item active">

                    <i class="bi bi-grid"></i>

                    <span>Dashboard</span>

                </a>
                {{-- @php
                $isAcademicActive = request()->routeIs([
                'admin.*',
                ]);
                @endphp --}}
                <a href="{{route('admin.users')}}"
                    class="app-sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Users</span>
                </a>

                <a href="#" class="app-sidebar-item">

                    <i class="bi bi-sun"></i>

                    <span>Today</span>

                </a>

                <a href="#" class="app-sidebar-item">

                    <i class="bi bi-calendar-event"></i>

                    <span>Upcoming</span>

                </a>

                <a href="#" class="app-sidebar-item">

                    <i class="bi bi-check2-circle"></i>

                    <span>Completed</span>

                </a>

                <a href="#" class="app-sidebar-item">

                    <i class="bi bi-star"></i>

                    <span>Favorites</span>

                </a>

            </nav>

            {{-- LISTS --}}
            <div class="small text-uppercase text-muted fw-semibold mt-5 mb-2">
                Lists
            </div>

            <nav class="d-flex flex-column gap-1 pb-3">

                <a href="#" class="app-sidebar-item">

                    <span class="app-dot bg-primary"></span>

                    <span>Personal</span>

                </a>

                <a href="#" class="app-sidebar-item">

                    <span class="app-dot bg-success"></span>

                    <span>Work</span>

                </a>

                <a href="#" class="app-sidebar-item">

                    <span class="app-dot bg-warning"></span>

                    <span>School</span>

                </a>

            </nav>

        </div>

    </div>

    {{-- FOOTER --}}
    <div class="p-3 border-top sidebar-footer mt-auto">

        <button class="btn app-btn-primary w-100 rounded-4">

            <i class="bi bi-plus-lg"></i>

            New Todo

        </button>

    </div>

</div>