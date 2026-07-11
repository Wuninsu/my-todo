const STORAGE_KEY = 'todo-theme';

export function initializeTheme() {
    const serverTheme = document.documentElement.getAttribute('data-theme');
    const theme = serverTheme || localStorage.getItem(STORAGE_KEY) || 'light';
    applyTheme(theme);

    // Theme toggle button
    const themeToggleBtn = document.querySelector('[data-theme-toggle]');

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', toggleTheme);
    }
}

export function toggleTheme() {
    const currentTheme =
        document.documentElement.getAttribute('data-theme');

    const newTheme =
        currentTheme === 'dark' ? 'light' : 'dark';

    applyTheme(newTheme);
}

export function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);

    localStorage.setItem(STORAGE_KEY, theme);

    updateThemeIcon(theme);
}

function updateThemeIcon(theme) {
    const icon = document.querySelector('[data-theme-icon]');

    if (!icon) return;

    icon.className =
        theme === 'dark'
            ? 'bi bi-sun'
            : 'bi bi-moon-stars';
}