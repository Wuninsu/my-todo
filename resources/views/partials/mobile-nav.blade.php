@php
    $activeMobileFilter = request()->routeIs('dashboard') ? request()->query('filter') : null;
@endphp

<nav class="app-mobile-nav d-lg-none">

    <a href="{{ route('dashboard') }}" wire:navigate class="{{ request()->routeIs('dashboard') && ! $activeMobileFilter ? 'active' : '' }}">

        <i class="bi bi-grid"></i>

        <span>Home</span>

    </a>

    <a href="{{ route('dashboard', ['filter' => 'today']) }}" wire:navigate class="{{ $activeMobileFilter === 'today' ? 'active' : '' }}">

        <i class="bi bi-calendar-event"></i>

        <span>Tasks</span>

    </a>

    <a href="{{ route('dashboard', ['new' => 1]) }}" wire:navigate>

        <i class="bi bi-plus-circle-fill"></i>

        <span>Create</span>

    </a>

    <a href="{{ route('dashboard', ['filter' => 'favorites']) }}" wire:navigate class="{{ $activeMobileFilter === 'favorites' ? 'active' : '' }}">

        <i class="bi bi-star"></i>

        <span>Favorites</span>

    </a>

    <a href="{{ route('profile') }}" wire:navigate class="{{ request()->routeIs('profile') ? 'active' : '' }}">

        <i class="bi bi-person"></i>

        <span>Profile</span>

    </a>

</nav>