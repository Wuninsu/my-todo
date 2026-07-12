// Import Bootstrap JS
import 'bootstrap';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import { initializeTheme, applyTheme } from './theme';
import { initializeToasts } from './toast';
import { initializeConfirm } from './confirm';

initializeTheme();

/*APP INIT*/
function initializeApp() {
    initializeTheme();
    initializeBootstrapComponents();
    initializeToasts();
    initializeConfirm();
}

/*KEYBOARD SHORTCUTS*/

function isTyping() {
    const el = document.activeElement;

    if (!el) return false;

    return ['INPUT', 'TEXTAREA', 'SELECT'].includes(el.tagName) || el.isContentEditable;
}

document.addEventListener('keydown', (e) => {
    if (isTyping()) return;

    if (e.key === '/') {
        e.preventDefault();

        const inputs = document.querySelectorAll('[data-global-search]');
        const visible = Array.from(inputs).find(el => el.offsetParent !== null) || inputs[0];

        visible?.focus();
    }

    if (e.key.toLowerCase() === 'n') {
        e.preventDefault();
        window.Livewire?.dispatch('open-create-todo');
    }
});

/*PERSISTED THEME (fired by the authenticated ThemeToggle Livewire component)*/
window.addEventListener('theme-changed', (e) => {
    applyTheme(e.detail.theme);
});

/*BOOTSTRAP COMPONENTS*/

function initializeBootstrapComponents() {

    /*TOOLTIPS*/

    document
        .querySelectorAll('[data-bs-toggle="tooltip"]')
        .forEach(el => {

            if (!bootstrap.Tooltip.getInstance(el)) {
                new bootstrap.Tooltip(el);
            }
        });

    /*POPOVERS*/

    document
        .querySelectorAll('[data-bs-toggle="popover"]')
        .forEach(el => {

            if (!bootstrap.Popover.getInstance(el)) {
                new bootstrap.Popover(el);
            }
        });
}

/*INITIAL LOAD*/
document.addEventListener(
    'DOMContentLoaded',
    initializeApp
);

/*LIVEWIRE NAVIGATION*/
document.addEventListener(
    'livewire:navigated',
    initializeApp
);

/*LIVEWIRE COMPONENT UPDATES*/
// Livewire v4 has no "livewire:update" DOM event (that was a Livewire v2
// name); the v4 equivalent for "re-run init after any component re-render,
// not just a full page navigation" is the `morphed` JS hook, fired once per
// component after its DOM has been patched.
window.Livewire?.hook('morphed', () => {
    initializeApp();
});