// Language BFCache sync: the server session (see SetLocale middleware /
// LocaleController) remains the single source of truth for locale -- this
// script never translates anything client-side. It only detects one failure
// mode: a page restored from BFCache resumes the HTML exactly as it was
// rendered (and frozen) at navigation-away time, including whatever locale
// was active THEN. If the visitor switches language on a different page and
// then presses Back, that frozen snapshot is now stale relative to the
// current session locale. When that mismatch is detected, this reloads the
// page once so Laravel re-renders it through SetLocale with the current
// session locale -- never a client-side text swap, never a query parameter.
const STORAGE_KEY = 'lastKnownLocale';

function currentDocumentLocale() {
    return document.documentElement.dataset.locale || null;
}

function rememberCurrentLocale() {
    const locale = currentDocumentLocale();
    if (!locale) return;
    try {
        localStorage.setItem(STORAGE_KEY, locale);
    } catch (e) {
        // Storage unavailable (private browsing, quota) -- sync just can't
        // happen this visit; the server-rendered locale for THIS request is
        // still correct either way.
    }
}

// A genuine fresh render (this line only ever executes on real script
// evaluation, never on a BFCache restore, since a restored page resumes
// its already-frozen JS state rather than re-running top-level code)
// reflects the current session locale -- record it as the last known-good
// value every time.
rememberCurrentLocale();

window.addEventListener('pageshow', (event) => {
    if (!event.persisted) return;

    const snapshotLocale = currentDocumentLocale();

    let lastKnown = null;
    try {
        lastKnown = localStorage.getItem(STORAGE_KEY);
    } catch (e) {
        // Storage unavailable -- nothing to compare against, so leave the
        // snapshot as-is rather than reload on a guess.
    }

    // Reload-loop safety: this branch only ever runs when `persisted` is
    // true, and `location.reload()` always performs a full fresh
    // navigation -- never itself a BFCache restoration -- so the resulting
    // page's own pageshow always has `persisted === false` and this check
    // is skipped on it. One mismatch, at most one reload, no chain.
    if (snapshotLocale && lastKnown && snapshotLocale !== lastKnown) {
        location.reload();
    }
});
