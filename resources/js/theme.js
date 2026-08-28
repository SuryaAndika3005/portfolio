// Light / Dark / System theme control. The no-flash class application
// itself happens in an inline <script> at the very top of <head> (see
// layout.blade.php) so it runs before first paint; this file only wires up
// the visible toggle buttons and keeps them in sync afterward. Deliberately
// framework-free -- three buttons, one localStorage key, no theme library.

const STORAGE_KEY = 'theme';
const media = window.matchMedia('(prefers-color-scheme: dark)');

// First-time visitors (no stored preference at all) default to Light, not
// System/OS -- only an explicit saved "system" choice should ever follow
// prefers-color-scheme. Must stay in lockstep with the inline anti-FOUC
// script in layout.blade.php, which applies the exact same rule before
// this file even loads.
function currentPreference() {
    const stored = localStorage.getItem(STORAGE_KEY);
    return stored === 'light' || stored === 'dark' || stored === 'system' ? stored : 'light';
}

function isDarkFor(preference) {
    return preference === 'dark' || (preference === 'system' && media.matches);
}

function applyTheme(preference) {
    document.documentElement.classList.toggle('dark', isDarkFor(preference));

    document.querySelectorAll('[data-theme-option]').forEach((button) => {
        const isActive = button.dataset.themeOption === preference;
        button.setAttribute('aria-pressed', String(isActive));
    });
}

document.addEventListener('DOMContentLoaded', () => {
    applyTheme(currentPreference());

    document.querySelectorAll('[data-theme-option]').forEach((button) => {
        button.addEventListener('click', () => {
            const preference = button.dataset.themeOption;
            try {
                localStorage.setItem(STORAGE_KEY, preference);
            } catch (e) {
                // Storage unavailable (private browsing, quota) -- theme
                // still applies for this page view, just won't persist.
            }
            applyTheme(preference);
        });
    });

    // Live-follow OS changes only while "System" is the active choice --
    // an explicit Light/Dark pick should never silently flip back.
    media.addEventListener('change', () => {
        if (currentPreference() === 'system') applyTheme('system');
    });
});
