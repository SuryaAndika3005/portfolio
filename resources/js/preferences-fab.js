// Floating Preferences control: one popover for Language + Theme, opened
// from a single FAB. Owns only the popover's open/close/focus/keyboard
// behavior and its visibility integration with the fullscreen gallery
// modal and the mobile nav drawer -- it does not duplicate any
// localization or theme logic. Language links are plain <a href> to
// LocaleController (see preferences-fab.blade.php), so switching locale is
// just a normal navigation. Theme buttons use the same [data-theme-option]
// contract as before, so theme.js's own querySelectorAll wiring picks them
// up automatically with zero changes there.

document.addEventListener('DOMContentLoaded', () => {
    const fab = document.getElementById('prefsFab');
    const btn = document.getElementById('prefsFabBtn');
    const popover = document.getElementById('prefsFabPopover');
    if (!fab || !btn || !popover) return;

    // --- First-visit discovery hint --------------------------------------
    // A dedicated flag, deliberately separate from the 'theme'/'locale'
    // storage keys -- "chose Dark" or "chose ID" must never be read as
    // "already discovered Preferences" (see the brief's explicit warning).
    // Shown only when that flag has never been set; dismissed by an
    // explicit close, by opening the FAB, or after a restrained timeout --
    // any of the three permanently sets the flag so it never reappears.
    const HINT_SEEN_KEY = 'prefsHintSeen';
    const hint = document.getElementById('prefsFabHint');
    const hintDismiss = document.getElementById('prefsFabHintDismiss');
    let hintTimeoutId = null;

    const dismissHint = () => {
        if (!hint || hint.hidden) return;
        hint.hidden = true;
        if (hintTimeoutId) {
            clearTimeout(hintTimeoutId);
            hintTimeoutId = null;
        }
        try {
            localStorage.setItem(HINT_SEEN_KEY, '1');
        } catch (e) {
            // Storage unavailable -- hint just won't stay dismissed across
            // reloads for this visitor, not worth failing louder over.
        }
    };

    if (hint) {
        let alreadySeen = false;
        try {
            alreadySeen = localStorage.getItem(HINT_SEEN_KEY) === '1';
        } catch (e) {
            // Treat as unseen if storage can't be read.
        }

        if (!alreadySeen) {
            hint.hidden = false;
            hintTimeoutId = setTimeout(dismissHint, 7000);
            hintDismiss?.addEventListener('click', (e) => {
                e.stopPropagation();
                dismissHint();
            });
        }
    }

    const isOpen = () => !popover.hidden;

    const openPopover = () => {
        dismissHint();
        popover.hidden = false;
        btn.setAttribute('aria-expanded', 'true');
        popover.querySelector('a, button')?.focus();
    };

    const closePopover = (returnFocus = false) => {
        if (!isOpen()) return;
        popover.hidden = true;
        btn.setAttribute('aria-expanded', 'false');
        if (returnFocus) btn.focus();
    };

    btn.addEventListener('click', () => {
        if (isOpen()) {
            closePopover();
        } else {
            openPopover();
        }
    });

    // Click outside the FAB/popover closes it.
    document.addEventListener('click', (e) => {
        if (isOpen() && !fab.contains(e.target)) closePopover();
    });

    // Escape closes and returns focus to the trigger.
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen()) closePopover(true);
    });

    // --- Visibility integration -----------------------------------------
    // Suppressed (hidden, not just visually covered -- see the
    // .prefs-fab--suppressed rule in app.css) whenever the fullscreen
    // image modal or the mobile nav drawer is open, so it never floats
    // above either and never sits in tab order behind them. Watched via
    // MutationObserver on each element's own existing open/close class
    // toggle -- no changes to image-modal.js or app.js were needed.
    const modal = document.getElementById('imageModal');
    const mobileMenu = document.getElementById('mobile-menu');

    const isModalOpen = () => !!modal && !modal.classList.contains('hidden');
    const isMobileMenuOpen = () => !!mobileMenu && mobileMenu.classList.contains('flex');

    const updateSuppression = () => {
        const suppressed = isModalOpen() || isMobileMenuOpen();
        fab.classList.toggle('prefs-fab--suppressed', suppressed);
        if (suppressed) closePopover();
    };

    if (modal) {
        new MutationObserver(updateSuppression).observe(modal, { attributes: true, attributeFilter: ['class'] });
    }
    if (mobileMenu) {
        new MutationObserver(updateSuppression).observe(mobileMenu, { attributes: true, attributeFilter: ['class'] });
    }
    updateSuppression();
});
