// Fullscreen image viewer for the project detail page. The deck itself
// (positioning, drag/swipe-to-navigate, side-click navigation, wrap-around
// recycling) is entirely visual-deck.js's createDeck() — this file only
// owns modal lifecycle (open/close/trigger/keyboard), per-active-card
// zoom/pan, and the chrome around the deck (counter, prev/next buttons,
// zoom hint, focus-mode dimming). Neither file knows the other's internals
// beyond the small isDragBlocked/onChange contract wired up below.
import gsap from 'gsap';
import { createDeck } from './visual-deck.js';

document.addEventListener('DOMContentLoaded', () => {
    // --- Peek stack on the detail-page intro visual ---
    // The two tucked-behind gallery cards sit visibly fanned out at rest
    // (a real stacked-cards look, not hidden until hover) — hovering just
    // spreads the fan further outward. Purely decorative; CSS (not GSAP)
    // drives the actual hover response on the current intro composition,
    // so this only needs to sync GSAP's own transform tracking with the
    // rest state where GSAP-driven peek behavior is still present.
    const peekWrapper = document.querySelector('[data-peek-wrapper]');
    if (peekWrapper) {
        const peeks = Array.from(peekWrapper.querySelectorAll('[data-peek]'));
        if (peeks.length) {
            peeks.forEach((peek, i) => {
                gsap.set(peek, { scale: 0.95, rotate: i === 0 ? -6 : 6, x: 0, y: 0 });
            });
        }
    }

    // --- Fullscreen modal ---
    const modal = document.getElementById('imageModal');
    const deckRoot = document.getElementById('modalDeck');
    const backdrop = document.getElementById('modalBackdrop');
    const closeBtn = document.getElementById('modalClose');
    const prevBtn = document.getElementById('modalPrev');
    const nextBtn = document.getElementById('modalNext');
    const counter = document.getElementById('modalCounter');
    const zoomHint = document.getElementById('modalZoomHint');

    if (!modal || !deckRoot) return;

    const cards = Array.from(deckRoot.querySelectorAll('[data-deck-card]'));
    const triggers = Array.from(document.querySelectorAll('[data-modal-trigger]'));
    if (!cards.length || !triggers.length) return;

    const hasMultiple = cards.length > 1;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // --- Zoom/pan, scoped per active card ---
    const ZOOM_MIN = 1;
    const ZOOM_MAX = 4;
    const zoomState = new WeakMap();

    const stateFor = (img) => {
        if (!zoomState.has(img)) zoomState.set(img, { scale: 1, x: 0, y: 0 });
        return zoomState.get(img);
    };

    const isActiveCard = (card) => card.getAttribute('aria-hidden') === 'false';

    const clampPan = (img, scale, x, y) => {
        const overflowX = Math.max(0, (img.offsetWidth * scale - img.offsetWidth) / 2);
        const overflowY = Math.max(0, (img.offsetHeight * scale - img.offsetHeight) / 2);
        return {
            x: gsap.utils.clamp(-overflowX, overflowX, x),
            y: gsap.utils.clamp(-overflowY, overflowY, y),
        };
    };

    const applyZoom = (img, scale, x, y, animate = true) => {
        const clamped = clampPan(img, scale, x, y);
        const state = stateFor(img);
        state.scale = scale;
        state.x = clamped.x;
        state.y = clamped.y;
        gsap.to(img, {
            scale,
            x: clamped.x,
            y: clamped.y,
            duration: animate ? 0.35 : 0,
            ease: 'power2.out',
            overwrite: 'auto',
        });
        img.style.cursor = scale > 1 ? 'grab' : 'zoom-in';
        setFocusMode(scale > 1, img.closest('[data-deck-card]'));
    };

    const resetZoom = (img) => applyZoom(img, 1, 0, 0, false);
    const resetAllZoom = () => cards.forEach((card) => {
        const img = card.querySelector('[data-zoom-img]');
        if (img) resetZoom(img);
    });

    // --- Focus mode: while the active card is zoomed, dim everything else
    // (other deck cards, chrome) so attention stays on the zoomed detail.
    // Cleared via clearProps so the deck's own opacity control (createDeck)
    // resumes normal ownership the moment zoom resets. ---
    let focusModeActive = false;
    const setFocusMode = (active, activeCard) => {
        if (active === focusModeActive) return;
        focusModeActive = active;
        const dur = reducedMotion ? 0 : 0.3;

        cards.forEach((card) => {
            if (card === activeCard) return;
            gsap.to(card, { opacity: active ? 0 : gsap.getProperty(card, 'opacity'), duration: dur, ease: 'power2.out', overwrite: 'auto' });
        });

        [counter, prevBtn, nextBtn].forEach((el) => {
            if (!el) return;
            gsap.to(el, { opacity: active ? 0.2 : 1, duration: dur, ease: 'power2.out', overwrite: 'auto' });
        });

        if (!active) {
            // Hand opacity control on non-active cards back to createDeck's
            // own layout pass rather than leaving an explicit inline value
            // GSAP set moments ago fighting with it.
            gsap.set(cards.filter((c) => c !== activeCard), { clearProps: 'opacity' });
        }
    };

    cards.forEach((card) => {
        const img = card.querySelector('[data-zoom-img]');
        if (!img) return;

        const zoomAt = (clientX, clientY, nextScale) => {
            const state = stateFor(img);
            const rect = img.getBoundingClientRect();
            const cx = rect.left + rect.width / 2;
            const cy = rect.top + rect.height / 2;
            const ratio = nextScale / state.scale;
            const nextX = (state.x - (clientX - cx)) * ratio + (clientX - cx);
            const nextY = (state.y - (clientY - cy)) * ratio + (clientY - cy);
            applyZoom(img, nextScale, nextX, nextY);
        };

        card.addEventListener('wheel', (e) => {
            if (!isActiveCard(card)) return;
            e.preventDefault();
            const state = stateFor(img);
            const factor = e.deltaY < 0 ? 1.2 : 1 / 1.2;
            const nextScale = gsap.utils.clamp(ZOOM_MIN, ZOOM_MAX, state.scale * factor);
            zoomAt(e.clientX, e.clientY, nextScale);
        }, { passive: false });

        let dragging = false;
        let wasDragging = false;
        let startX = 0;
        let startY = 0;
        let originX = 0;
        let originY = 0;

        card.addEventListener('pointerdown', (e) => {
            if (!isActiveCard(card)) return;
            const state = stateFor(img);
            if (state.scale <= 1) return;
            startX = e.clientX;
            startY = e.clientY;
            wasDragging = false;
            dragging = true;
            originX = state.x;
            originY = state.y;
            card.setPointerCapture?.(e.pointerId);
            img.style.cursor = 'grabbing';
        });

        card.addEventListener('pointermove', (e) => {
            if (!dragging) return;
            if (Math.hypot(e.clientX - startX, e.clientY - startY) > 20) wasDragging = true;
            const state = stateFor(img);
            applyZoom(img, state.scale, originX + (e.clientX - startX), originY + (e.clientY - startY), false);
        });

        const endDrag = (e) => {
            if (!dragging) return;
            dragging = false;
            const state = stateFor(img);
            img.style.cursor = state.scale > 1 ? 'grab' : 'zoom-in';
            if (e && card.hasPointerCapture?.(e.pointerId)) card.releasePointerCapture(e.pointerId);
        };
        card.addEventListener('pointerup', endDrag);
        card.addEventListener('pointercancel', endDrag);

        // A click on the active image toggles zoom (in on the tapped
        // point, or back out if already zoomed). This listener only ever
        // actually fires for the active card's click — createDeck's own
        // capture-phase routing already intercepts and stops clicks on any
        // non-active card before they reach here. Clicking the empty
        // letterboxed space around the image (still inside the card, not
        // on the <img> itself) does nothing — only #modalBackdrop closes
        // the modal (see below), never inferred from "missed the image".
        card.addEventListener('click', (e) => {
            if (wasDragging) {
                wasDragging = false;
                return;
            }
            if (e.target !== img) return;
            const state = stateFor(img);
            if (state.scale > 1) {
                applyZoom(img, 1, 0, 0);
            } else {
                zoomAt(e.clientX, e.clientY, 2.4);
            }
        });
    });

    // --- Deck mount: positioning/drag/swipe/side-click-navigation is
    // entirely createDeck's — this file only reacts to index changes
    // (chrome) and tells the deck when NOT to treat a pointerdown as a
    // navigation drag (the active card is already zoomed in and panning). ---
    const deck = createDeck(deckRoot, cards, {
        onChange: (index) => {
            resetAllZoom();
            if (counter) {
                counter.textContent = `${index + 1} / ${cards.length}`;
                counter.classList.toggle('hidden', !hasMultiple);
            }
            prevBtn?.classList.toggle('hidden', !hasMultiple);
            nextBtn?.classList.toggle('hidden', !hasMultiple);
        },
        isDragBlocked: (e) => {
            const img = e.target.closest('[data-zoom-img]');
            if (!img) return false;
            const card = img.closest('[data-deck-card]');
            if (!card || !isActiveCard(card)) return false;
            return stateFor(img).scale > 1;
        },
    });

    // --- Modal open/close ---
    let hintShown = false;
    const showZoomHintOnce = () => {
        if (hintShown || !zoomHint || !hasMultiple) return;
        hintShown = true;
        if (reducedMotion) {
            gsap.to(zoomHint, { opacity: 0, duration: 0.5, delay: 2.2 });
            return;
        }
        gsap.fromTo(zoomHint, { opacity: 0 }, {
            opacity: 1,
            duration: 0.4,
            onComplete: () => {
                gsap.to(zoomHint, { opacity: 0, duration: 0.5, delay: 2.2 });
            },
        });
    };

    // Focus lifecycle: opening the modal must move focus into it (nothing
    // did before this — a keyboard user's focus stayed on the now-hidden-
    // behind-the-backdrop trigger, and Tab walked straight into background
    // page content despite aria-modal="true"), and closing must return
    // focus to whichever trigger opened it, not just leave it wherever the
    // in-modal Tabbing left off.
    let lastTrigger = null;

    const focusableInModal = () =>
        Array.from(modal.querySelectorAll('button:not([disabled]), [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'))
            .filter((el) => el.offsetParent !== null);

    const trapTabKey = (e) => {
        const focusables = focusableInModal();
        if (!focusables.length) return;
        const first = focusables[0];
        const last = focusables[focusables.length - 1];
        if (!focusables.includes(document.activeElement)) {
            e.preventDefault();
            first.focus();
        } else if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    };

    const openModal = (index) => {
        deck.goTo(index, false);
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        showZoomHintOnce();
        closeBtn?.focus();
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        resetAllZoom();
        setFocusMode(false, null);
        lastTrigger?.focus();
        lastTrigger = null;
    };

    triggers.forEach((trigger) => {
        const index = parseInt(trigger.dataset.slideIndex ?? '0', 10);
        trigger.addEventListener('click', () => {
            lastTrigger = trigger;
            openModal(index);
        });
    });

    prevBtn?.addEventListener('click', () => deck.goTo(deck.getIndex() - 1));
    nextBtn?.addEventListener('click', () => deck.goTo(deck.getIndex() + 1));

    closeBtn?.addEventListener('click', closeModal);

    // Explicit backdrop-only close — #modalBackdrop is the ONLY element
    // whose plain click closes the modal. #modalContent (everything else,
    // including the deck's own inset margin) is pointer-events-none except
    // for the specific interactive pieces that opt back in, so nothing can
    // accidentally "fall through" to a generic close inference.
    backdrop?.addEventListener('click', closeModal);

    document.addEventListener('keydown', (e) => {
        if (modal.classList.contains('hidden')) return;
        if (e.key === 'Escape') closeModal();
        if (e.key === 'ArrowLeft') deck.goTo(deck.getIndex() - 1);
        if (e.key === 'ArrowRight') deck.goTo(deck.getIndex() + 1);
        if (e.key === 'Tab') trapTabKey(e);
    });
});
