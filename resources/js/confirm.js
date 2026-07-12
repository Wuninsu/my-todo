// `wire:navigate` swaps the entire <body> for a fresh one on every
// navigation (document.body.replaceWith(newBody)), so any DOM element
// reference captured once at init time goes stale after the first
// navigation. Everything here either looks elements up fresh at call time
// or binds listeners to `document` itself (which is never replaced), so it
// keeps working across any number of wire:navigate transitions without
// needing a hard refresh.

function getBackdrop() {
    return document.getElementById('appConfirmBackdrop');
}

export function initializeConfirm() {
    if (window.appConfirm) return;

    let pending = null;

    function close() {
        getBackdrop()?.classList.remove('app-confirm-visible');
        pending = null;
    }

    function accept() {
        const toRun = pending;
        close();

        if (toRun) {
            window.Livewire?.dispatch(toRun.onConfirm, toRun.onConfirmParams || {});
        }
    }

    document.addEventListener('click', (e) => {
        if (e.target.closest('[data-confirm-accept]')) {
            accept();
        } else if (e.target.closest('[data-confirm-cancel]')) {
            close();
        } else if (e.target.id === 'appConfirmBackdrop') {
            close();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && getBackdrop()?.classList.contains('app-confirm-visible')) {
            close();
        }
    });

    window.appConfirm = function ({
        title = 'Are you sure?',
        message = '',
        confirmText = 'Confirm',
        danger = true,
        onConfirm,
        onConfirmParams = {},
    }) {
        if (!onConfirm) return;

        const backdrop = getBackdrop();

        if (!backdrop) return;

        const iconEl = backdrop.querySelector('[data-confirm-icon]');
        const titleEl = backdrop.querySelector('[data-confirm-title]');
        const messageEl = backdrop.querySelector('[data-confirm-message]');
        const acceptBtn = backdrop.querySelector('[data-confirm-accept]');

        titleEl.textContent = title;
        messageEl.textContent = message;
        acceptBtn.textContent = confirmText;
        acceptBtn.classList.toggle('btn-danger', danger);
        acceptBtn.classList.toggle('app-btn-primary', !danger);
        iconEl.classList.toggle('app-confirm-icon-neutral', !danger);
        iconEl.innerHTML = danger
            ? '<i class="bi bi-exclamation-triangle-fill"></i>'
            : '<i class="bi bi-question-circle-fill"></i>';

        pending = { onConfirm, onConfirmParams };
        backdrop.classList.add('app-confirm-visible');
    };
}
