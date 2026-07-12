<div id="app-toast-container" class="app-toast-container" aria-live="polite" aria-atomic="true"></div>

@if (session('toast'))
    <script>
        window.__pendingToast = @json(session('toast'));
    </script>
@endif
