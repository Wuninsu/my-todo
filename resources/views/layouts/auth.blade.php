<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        {{ $title ?? 'Authentication' }}
    </title>

    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])

    @livewireStyles

</head>

<body>

    <div class="auth-layout">

        {{-- LEFT SIDE --}}
        <div class="auth-aside d-none d-lg-flex">

            <div class="auth-aside-content">

                {{-- LOGO --}}
                {{-- <div class="mb-2">
                    <div class="auth-logo">
                        <i class="bi bi-check2-square"></i>
                    </div>
                </div> --}}

                {{-- HERO --}}
                <div>

                    <span class="auth-badge">
                        Offline First
                    </span>

                    <h1 class="auth-title">
                        Stay productive even without internet.
                    </h1>

                    <p class="auth-description">
                        Organize tasks, sync across devices,
                        and manage your workflow with a
                        modern offline-first experience.
                    </p>

                </div>

                {{-- FEATURES --}}
                <div class="auth-features">

                    <div class="auth-feature">
                        <div class="auth-feature-icon">
                            <i class="bi bi-wifi-off"></i>
                        </div>

                        <div>
                            <h6>
                                Offline Sync
                            </h6>
                            <p>
                                Continue working without connection.
                            </p>
                        </div>

                    </div>

                    <div class="auth-feature">

                        <div class="auth-feature-icon">

                            <i class="bi bi-lightning-charge"></i>

                        </div>

                        <div>

                            <h6>
                                Fast Experience
                            </h6>

                            <p>
                                Instant interactions powered by Livewire.
                            </p>

                        </div>

                    </div>

                    <div class="auth-feature">

                        <div class="auth-feature-icon">

                            <i class="bi bi-phone"></i>

                        </div>

                        <div>

                            <h6>
                                Responsive Design
                            </h6>

                            <p>
                                Works beautifully on every device.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="auth-main">

            {{-- TOPBAR --}}
            <div class="auth-topbar">
                <button class="btn app-icon-btn" data-theme-toggle>
                    <i class="bi bi-moon-stars" data-theme-icon></i>
                </button>
            </div>

            {{-- CONTENT --}}
            <div class="auth-content">
                <div class="auth-card app-card">
                    {{-- LOGO --}}
                    <div class="mb-2">
                        <div class="auth-logo mx-auto">
                            <i class="bi bi-check2-square"></i>
                        </div>

                    </div>

                    {{ $slot ?? '' }}

                    @yield('content')

                </div>
            </div>

        </div>

    </div>

    @livewireScripts

</body>

</html>