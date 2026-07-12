<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-theme="{{ auth()->check() && auth()->user()->theme === 'dark' ? 'dark' : 'light' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body>
    <div class="app-layout">

        {{-- DESKTOP SIDEBAR --}}
        <aside class="app-sidebar d-none d-lg-flex">

            @include('partials.sidebar', ['variant' => 'desktop'])

        </aside>

        {{-- MOBILE SIDEBAR --}}
        <div class="offcanvas offcanvas-start app-mobile-sidebar" tabindex="-1" id="mobileSidebar">

            <div class="offcanvas-body p-0">

                @include('partials.sidebar', ['variant' => 'mobile'])

            </div>

        </div>

        {{-- MAIN --}}
        <main class="app-main">

            {{-- HEADER --}}
            @include('partials.header')

            {{-- CONTENT --}}
            <div class="app-content">

                {{ $slot ?? '' }}

                @yield('content')

            </div>

        </main>

    </div>

    {{-- MOBILE NAV --}}
    @include('partials.mobile-nav')

    {{-- GLOBAL TOASTS + CONFIRM DIALOG --}}
    @include('partials.toasts')
    @include('partials.confirm-dialog')

    @livewireScripts
</body>

</html>