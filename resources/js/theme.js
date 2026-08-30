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

// BFCache restoration: `pageshow` fires on every load (normal AND
// bfcache-restored), but `event.persisted` is only true for the latter --
// a page pulled back out of bfcache resumes exactly as it was frozen,
// including whatever .dark class was applied before the visitor navigated
// away. If they changed the theme (or the OS flipped light/dark while
// "System" was selected) on another page in between, that frozen snapshot
// is now stale. Re-running applyTheme() re-reads localStorage and
// re-evaluates media.matches fresh, so it self-corrects for every case
// (Light<->Dark, System<->Light, and an OS change during System) with the
// same logic already used on normal load -- no separate BFCache-specific
// theme logic needed. Gated on `persisted` specifically so this never runs
// (and can never introduce a flash) on a normal fresh load, where the
// inline anti-FOUC script + the DOMContentLoaded handler above have
// already applied the correct theme before this fires.
window.addEventListener('pageshow', (event) => {
    if (event.persisted) applyTheme(currentPreference());
});
