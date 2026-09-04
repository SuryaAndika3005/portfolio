// One scheduler for the row: never concurrent independent slideshows.
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('works-accordion');
    if (!container) return;
    const panels = [...container.querySelectorAll('[data-accordion-panel]')];
    const decks = panels.map(panel => ({ slides: [...panel.querySelectorAll('[data-slide]')], index: 0 }));
    const desktop = matchMedia('(min-width: 1024px) and (hover: hover) and (pointer: fine)');
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    let visible = false, expanded = false, timer = null, cursor = 0;
    const schedule = () => {
        clearTimeout(timer);
        timer = null;
        if (!visible || expanded || document.hidden || reduced.matches || !desktop.matches) return;
        timer = setTimeout(() => {
            const candidates = decks.filter(deck => deck.slides.length > 1);
            if (candidates.length) {
                const deck = candidates[cursor++ % candidates.length];
                const next = (deck.index + 1) % deck.slides.length;
                if (deck.slides[next].complete && deck.slides[next].naturalWidth) {
                    deck.slides[deck.index].classList.remove('is-active');
                    deck.slides[next].classList.add('is-active');
                    deck.index = next;
                }
            }
            schedule();
        }, 6000); // Longer than the 900ms crossfade; only one deck advances.
    };
    const expand = panel => {
        expanded = Boolean(panel);
        panels.forEach(item => {
            item.classList.toggle('is-expanded', item === panel);
            item.classList.toggle('is-collapsed', expanded && item !== panel);
        });
        schedule();
    };
    panels.forEach(panel => {
        panel.addEventListener('mouseenter', () => { if (desktop.matches) expand(panel); });
        panel.addEventListener('focusin', () => expand(panel));
    });
    container.addEventListener('mouseleave', () => {
        expand(panels.find(panel => panel.contains(document.activeElement)) ?? null);
    });
    container.addEventListener('focusout', event => {
        if (!container.contains(event.relatedTarget)) expand(null);
    });
    document.addEventListener('visibilitychange', schedule);
    reduced.addEventListener('change', schedule);
    desktop.addEventListener('change', () => expand(null));
    window.addEventListener('pagehide', () => { clearTimeout(timer); timer = null; });
    window.addEventListener('pageshow', schedule);
    if ('IntersectionObserver' in window) {
        new IntersectionObserver(entries => {
            visible = entries.some(entry => entry.isIntersecting);
            schedule();
        }, { threshold: 0.15 }).observe(container);
    }
    // Without visibility observation, keep static images instead of background work.
});
