// Import Bootstrap JS
import 'bootstrap';
import { Modal } from 'bootstrap';

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import { initializeTheme } from './theme';

initializeTheme();

/*APP INIT*/
function initializeApp() {
    initializeTheme();
    initializeBootstrapComponents();
}

/*BOOTSTRAP COMPONENTS*/

function initializeBootstrapComponents() {

    /*TOOLTIPS*/

    document
        .querySelectorAll('[data-bs-toggle="tooltip"]')
        .forEach(el => {

            new bootstrap.Tooltip(el);
        });

    /*POPOVERS*/

    document
        .querySelectorAll('[data-bs-toggle="popover"]')
        .forEach(el => {

            new bootstrap.Popover(el);
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