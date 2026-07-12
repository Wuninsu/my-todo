const TOAST_ICONS = {
    success: 'bi-check-circle-fill',
    error: 'bi-x-circle-fill',
    info: 'bi-info-circle-fill',
};

const TOAST_DURATION = 4000;

function showToast({ type = 'info', message = '' }) {
    const container = document.getElementById('app-toast-container');

    if (!container || !message) return;

    const icon = TOAST_ICONS[type] || TOAST_ICONS.info;

    const toast = document.createElement('div');
    toast.className = `app-toast app-toast-${type}`;
    toast.setAttribute('role', 'status');

    const iconEl = document.createElement('i');
    iconEl.className = `bi ${icon}`;

    const messageEl = document.createElement('span');
    messageEl.className = 'app-toast-message';
    messageEl.textContent = message;

    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'app-toast-close';
    closeBtn.setAttribute('aria-label', 'Dismiss');
    closeBtn.innerHTML = '<i class="bi bi-x"></i>';

    toast.append(iconEl, messageEl, closeBtn);
    container.appendChild(toast);

    const remove = () => {
        toast.classList.remove('app-toast-visible');
        setTimeout(() => toast.remove(), 200);
    };

    closeBtn.addEventListener('click', remove);

    requestAnimationFrame(() => toast.classList.add('app-toast-visible'));

    setTimeout(remove, TOAST_DURATION);
}

export function initializeToasts() {
    if (!window.appToast) {
        window.appToast = showToast;
        window.addEventListener('toast', (e) => window.appToast(e.detail));
    }

    // A toast flashed to the session (e.g. right before a server-side
    // redirect) is queued by the toasts partial as window.__pendingToast
    // since a live Livewire dispatch wouldn't survive the navigation.
    if (window.__pendingToast) {
        window.appToast(window.__pendingToast);
        window.__pendingToast = null;
    }
}
