<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        {{ $title ?? 'Welcome' }}
    </title>

    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])

    @livewireStyles

</head>

<body>


    {{-- RIGHT SIDE --}}
    <div class="auth-main mb-5">

        {{-- TOPBAR --}}
        <div class="auth-topbar">
            <button class="btn app-icon-btn" data-theme-toggle>
                <i class="bi bi-moon-stars" data-theme-icon></i>
            </button>
        </div>

        {{-- CONTENT --}}
        <div class="container">
            <div class="app-card p-4">
                {{-- LOGO --}}
                <div class="mb-2">
                    <div class="auth-logo mx-auto">
                        <i class="bi bi-check2-square"></i>
                    </div>

                </div>

                {{-- HERO --}}
                <div class="text-center">

                    {{-- BADGE --}}
                    <div class="mb-4">

                        <span class="auth-badge">

                            Offline First Productivity

                        </span>

                    </div>

                    {{-- TITLE --}}
                    <h1 class="welcome-title mb-2">

                        Organize your tasks
                        smarter and faster.

                    </h1>

                    {{-- DESCRIPTION --}}
                    <p class="welcome-description mx-auto mb-3">

                        TodoFlow helps you manage todos,
                        sync across devices, and stay productive
                        even when you're offline.

                    </p>

                    {{-- ACTIONS --}}
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mb-3">

                        @guest

                        <a href="{{ route('register') }}" wire:navigate
                            class="btn app-btn-primary btn-lg rounded-4 px-4">

                            <i class="bi bi-person-plus"></i>

                            Get Started

                        </a>

                        <a href="{{ route('login') }}" wire:navigate class="btn btn-light btn-lg rounded-4 px-4">

                            <i class="bi bi-box-arrow-in-right"></i>

                            Login

                        </a>

                        @else

                        <a href="/dashboard" wire:navigate class="btn app-btn-primary btn-lg rounded-4 px-4">

                            <i class="bi bi-grid"></i>

                            Go To Dashboard

                        </a>

                        @endguest

                    </div>

                </div>

                {{-- FEATURES --}}
                <div class="row g-4 mt-2">

                    {{-- FEATURE --}}
                    <div class="col-md-4">

                        <div class="app-card p-4 h-100 text-center">

                            <div class="welcome-feature-icon mx-auto mb-4">

                                <i class="bi bi-wifi-off"></i>

                            </div>

                            <h5 class="fw-bold mb-3">

                                Offline First

                            </h5>

                            <p class="text-muted mb-0">

                                Continue managing your tasks
                                even without internet connection.

                            </p>

                        </div>

                    </div>

                    {{-- FEATURE --}}
                    <div class="col-md-4">

                        <div class="app-card p-4 h-100 text-center">

                            <div class="welcome-feature-icon mx-auto mb-4 success">

                                <i class="bi bi-lightning-charge"></i>

                            </div>

                            <h5 class="fw-bold mb-3">

                                Fast Experience

                            </h5>

                            <p class="text-muted mb-0">

                                Powered by Livewire 4 for
                                modern realtime interactions.

                            </p>

                        </div>

                    </div>

                    {{-- FEATURE --}}
                    <div class="col-md-4">

                        <div class="app-card p-4 h-100 text-center">

                            <div class="welcome-feature-icon mx-auto mb-4 warning">

                                <i class="bi bi-phone"></i>

                            </div>

                            <h5 class="fw-bold mb-3">

                                Responsive Design

                            </h5>

                            <p class="text-muted mb-0">

                                Beautiful experience across
                                desktop, tablet, and mobile.

                            </p>

                        </div>

                    </div>

                </div>

            </div>
        </div>

    </div>
    @livewireScripts

</body>

</html>