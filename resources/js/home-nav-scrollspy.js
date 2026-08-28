// Homepage nav: lightweight scrollspy for the Home/About/Projects/Skills
// links (both the desktop floating pill and the mobile dropdown share the
// same [data-nav-link]/[data-target] contract, so one observer drives
// both). Mirrors archive-nav.js's IntersectionObserver-only approach
// exactly -- no scroll listener, no per-frame layout reads. Presentation
// only: styling for .is-active lives in app.css (.nav-link.is-active and
// #mobile-menu a.is-active), not here.
//
// Defensive by construction: nav.blade.php's default branch (the one with
// these links) only renders its full link set on pages that actually have
// #top/#about/#projects/#skills -- in practice just the homepage -- so
// this quietly no-ops anywhere those sections don't exist, the same way
// archive-nav.js no-ops off the Archive page.

document.addEventListener('DOMContentLoaded', () => {
    const links = Array.from(document.querySelectorAll('[data-nav-link]'));
    if (!links.length || !('IntersectionObserver' in window)) return;

    const linksByTarget = new Map();
    links.forEach((link) => {
        const target = link.dataset.target;
        if (!linksByTarget.has(target)) linksByTarget.set(target, []);
        linksByTarget.get(target).push(link);
    });

    const sections = Array.from(document.querySelectorAll('#top, #about, #projects, #skills'))
        .filter((section) => linksByTarget.has(section.id));
    if (!sections.length) return;

    const setActive = (id) => {
        linksByTarget.forEach((linksForTarget, target) => {
            const isActive = target === id;
            linksForTarget.forEach((link) => {
                link.classList.toggle('is-active', isActive);
                if (isActive) {
                    link.setAttribute('aria-current', 'location');
                } else {
                    link.removeAttribute('aria-current');
                }
            });
        });
    };

    // -96px approximates the default (non-compact) topbar's own rendered
    // height (py-5 + brand/link row) so the "current" section roughly
    // matches what's actually visible under the fixed nav -- same
    // live-measured-estimate approach archive-nav.js uses for its own
    // (shorter) topbar variant.
    const spyObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) setActive(entry.target.id);
            });
        },
        { rootMargin: '-96px 0px -70% 0px', threshold: 0 }
    );

    sections.forEach((section) => spyObserver.observe(section));
});
