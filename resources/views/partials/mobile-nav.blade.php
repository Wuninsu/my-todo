<nav class="app-mobile-nav d-lg-none">

    <a href="{{ route('dashboard') }}" wire:navigate class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">

        <i class="bi bi-grid"></i>

        <span>Home</span>

    </a>

    <a href="#" onclick="event.preventDefault(); window.Livewire.dispatch('filter-selected', { filter: 'today' })">

        <i class="bi bi-calendar-event"></i>

        <span>Tasks</span>

    </a>

    <a href="#" onclick="event.preventDefault(); window.Livewire.dispatch('open-create-todo')">

        <i class="bi bi-plus-circle-fill"></i>

        <span>Create</span>

    </a>

    <a href="#" onclick="event.preventDefault(); window.Livewire.dispatch('filter-selected', { filter: 'favorites' })">

        <i class="bi bi-star"></i>

        <span>Favorites</span>

    </a>

    <a href="{{ route('profile') }}" wire:navigate class="{{ request()->routeIs('profile') ? 'active' : '' }}">

        <i class="bi bi-person"></i>

        <span>Profile</span>

    </a>

</nav>