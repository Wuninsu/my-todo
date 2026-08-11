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

    {{-- GLOBAL TOASTS + CONFIRM DIALOG --}}
    @include('partials.toasts')
    @include('partials.confirm-dialog')

    @livewireScripts

</body>

</html>