// Project Archive: lightweight scrollspy for the chapter-navigation links
// that live inside the archive topbar variant (nav.blade.php's
// archiveChapters branch -- there's only ONE sticky bar on this page now,
// so this file no longer needs any "is it currently stuck" detection,
// just which chapter is current. IntersectionObserver only, no scroll
// listener, no per-frame layout reads.

document.addEventListener('DOMContentLoaded', () => {
    const links = Array.from(document.querySelectorAll('[data-chapter-link]'));
    if (!links.length || !('IntersectionObserver' in window)) return;

    const linksByTarget = new Map(links.map((link) => [link.dataset.target, link]));

    const sections = Array.from(document.querySelectorAll('[data-chapter-section]'))
        .filter((section) => linksByTarget.has(section.id));
    if (!sections.length) return;

    const setActive = (slug) => {
        links.forEach((link) => {
            const isActive = link.dataset.target === slug;
            link.classList.toggle('is-active', isActive);
            if (isActive) {
                link.setAttribute('aria-current', 'location');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    };

    // -80px matches the chapter sections' own scroll-mt-20 (5rem = 80px:
    // the ~60px archive topbar height plus breathing room, live-measured)
    // so an anchor-jump lands exactly where the scrollspy would already
    // consider that chapter current -- no mismatch between "click to
    // jump" and "scroll to track".
    const spyObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) setActive(entry.target.id);
            });
        },
        { rootMargin: '-80px 0px -70% 0px', threshold: 0 }
    );

    sections.forEach((section) => spyObserver.observe(section));
});
