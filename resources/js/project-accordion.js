// Homepage "Selected Works" horizontal accordion.
// Each panel auto-cycles a background slideshow of that category's project
// images at all times. On desktop (the lg breakpoint enables the row layout
// in CSS), hovering a panel expands it and collapses its siblings; leaving
// the whole accordion resets everyone back to equal width.

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('works-accordion');
    if (!container) return;

    const panels = Array.from(container.querySelectorAll('[data-accordion-panel]'));
    if (!panels.length) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!reduceMotion) {
        panels.forEach((panel) => {
            const slides = Array.from(panel.querySelectorAll('[data-slide]'));
            if (slides.length < 2) return;

            let index = 0;
            // Slight random offset per panel so they don't all cross-fade in unison.
            const interval = 3200 + Math.random() * 900;

            setInterval(() => {
                slides[index].classList.remove('is-active');
                index = (index + 1) % slides.length;
                slides[index].classList.add('is-active');
            }, interval);
        });
    }

    const setExpanded = (activePanel) => {
        panels.forEach((panel) => {
            panel.classList.toggle('is-expanded', panel === activePanel);
            panel.classList.toggle('is-collapsed', Boolean(activePanel) && panel !== activePanel);
        });
    };

    panels.forEach((panel) => {
        panel.addEventListener('mouseenter', () => setExpanded(panel));
        panel.addEventListener('focus', () => setExpanded(panel));
    });

    container.addEventListener('mouseleave', () => setExpanded(null));
    container.addEventListener('focusout', (e) => {
        if (!container.contains(e.relatedTarget)) setExpanded(null);
    });
});
