<button type="button" class="btn app-icon-btn" wire:click="toggle" title="Toggle theme">
    <i class="bi {{ $theme === 'dark' ? 'bi-sun' : 'bi-moon-stars' }}"></i>
</button>
