// Dark mode — init ASAP to prevent flash of wrong theme
(function () {
    try {
        const saved      = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (saved === 'dark' || (!saved && prefersDark)) {
            document.documentElement.classList.add('dark');
        }
    } catch (e) {}
})();

import './bootstrap';
