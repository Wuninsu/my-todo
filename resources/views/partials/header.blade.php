<header class="app-header app-glass">

    <div class="d-flex align-items-center justify-content-between">

        {{-- LEFT --}}
        <div class="d-flex align-items-center gap-3">

            {{-- MOBILE MENU --}}
            <button class="btn d-lg-none app-icon-btn" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">

                <i class="bi bi-list"></i>

            </button>

            <div>

                <h5 class="fw-bold mb-0">
                    Dashboard
                </h5>

                <div class="small">
                    Manage your tasks efficiently
                </div>

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="d-flex align-items-center gap-2">

            {{-- THEME --}}

            <button class="btn app-icon-btn" data-theme-toggle>
                <i class="bi bi-moon-stars" data-theme-icon></i>
            </button>
            {{-- PROFILE --}}
            {{-- <button class="btn app-icon-btn">

                <i class="bi bi-person-circle"></i>

            </button> --}}

            <a href="{{ route('profile') }}" wire:navigate
                class="app-icon-btn">
                <i class="bi bi-person-circle"></i>
            </a>

        </div>

    </div>

</header>