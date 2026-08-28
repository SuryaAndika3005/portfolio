// Deck positioning controller: given a set of "card" elements (each
// wrapping one image), arranges them as a rotating stack around a single
// active index — full color/centered/scale 1 in front, others grayscale/
// scaled-down/rotated to each side. Pure positioning + navigation only; it
// has no opinion on what a click/drag on the active card *means* (zoom,
// close, etc.) — that's the caller's job (image-modal.js), which is also
// the only place this deck is ever mounted (inside the fullscreen modal).
import gsap from 'gsap';

export function createDeck(root, cards, { onChange, isDragBlocked } = {}) {
    const n = cards.length;
    if (!n) return { goTo: () => {}, getIndex: () => 0 };

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    // Beyond this many positions from the active card, images are just
    // hidden (opacity 0, non-interactive, untweened) — keeps large
    // galleries from animating cards nobody can see.
    const MAX_VISIBLE_OFFSET = 3;

    const grayState = cards.map(() => ({ g: 0 }));
    let currentIndex = 0;
    let wasDragging = false;
    // How far a drag/swipe has to move before it counts as a drag rather
    // than a tap/click — small on purpose so a real drag never leaks
    // through as an accidental click (zoom/navigate/close), while a plain
    // tap still registers as one.
    const DRAG_THRESHOLD = 6;

    const wrap = (i) => ((i % n) + n) % n;

    // Shortest signed distance from the active card, wrapping around the
    // ends — so going "next" from the last image lands smoothly back at
    // the first instead of yanking across the whole deck.
    const shortestOffset = (index) => {
        let raw = index - currentIndex;
        if (raw > n / 2) raw -= n;
        if (raw < -n / 2) raw += n;
        return raw;
    };

    // Last offset actually rendered for each card, so a wrap-around
    // recompute (e.g. a card that was the deepest one on the left becoming
    // the deepest one on the right) can be detected and handled specially
    // — see the `recycled` check in layout() below.
    let prevOffsets = cards.map((_, i) => shortestOffset(i));

    const styleForOffset = (offset) => {
        const abs = Math.abs(offset);
        if (abs === 0) return { xPercent: 0, y: 0, rotate: 0, scale: 1, opacity: 1, gray: 0 };
        const dir = Math.sign(offset);
        // Slightly less rotation on narrow (mobile) viewports so the curve
        // reads as a gentle deck rather than a tilt, per motion-polish pass.
        const compact = !reducedMotion && window.innerWidth < 640;
        return {
            // Neighbors sit closer to center than before (52% vs the prior
            // 60%) so the deck reads as one overlapping stack rather than
            // three separate cards side by side.
            xPercent: dir * (52 + (abs - 1) * 38),
            y: reducedMotion ? 0 : Math.min(abs, 3) * 9,
            rotate: reducedMotion ? 0 : dir * Math.min((compact ? 2.6 : 3.5) + (abs - 1) * 1.8, compact ? 7 : 9),
            scale: Math.max(0.6, 0.84 - (abs - 1) * 0.12),
            opacity: Math.max(0.3, 0.75 - (abs - 1) * 0.2),
            gray: 100,
        };
    };

    const layout = (animate = true) => {
        const dur = reducedMotion ? 0.28 : 0.62;
        const ease = reducedMotion ? 'power1.out' : 'power3.out';

        cards.forEach((card, i) => {
            const offset = shortestOffset(i);
            const prevOffset = prevOffsets[i];
            const abs = Math.abs(offset);
            const isActive = offset === 0;

            card.setAttribute('aria-hidden', isActive ? 'false' : 'true');
            card.style.cursor = isActive ? '' : 'pointer';

            // A card whose shortest-path offset flips sign in one step
            // (e.g. -2 -> +2) is the one wrapping around the back of the
            // deck — tweening xPercent straight across would visibly drag
            // it through the center, over the active card, to the far
            // side. Instead it gets recycled: parked invisibly (via
            // gsap.set, no tween) already at its new far position, then
            // faded/scaled subtly into view from there.
            const recycled = animate && prevOffset !== 0 && offset !== 0 && Math.sign(prevOffset) !== Math.sign(offset);

            if (abs > MAX_VISIBLE_OFFSET) {
                // Keep hidden cards continuously parked at their true
                // (invisible) position too — never just frozen wherever
                // they happened to be when they last left view — so
                // whenever one does come back into range, animating out of
                // "recycled" or "still hidden" always starts from an
                // accurate spot instead of a stale one.
                const parked = styleForOffset(offset);
                gsap.set(card, { xPercent: parked.xPercent, y: parked.y, rotate: parked.rotate, scale: parked.scale, opacity: 0, pointerEvents: 'none', zIndex: 0, x: 0 });
                grayState[i].g = 100;
                card.style.filter = 'grayscale(100%)';
                prevOffsets[i] = offset;
                return;
            }

            gsap.set(card, { zIndex: 50 - abs, pointerEvents: 'auto' });
            const target = styleForOffset(offset);

            if (recycled) {
                // Teleport straight to the target position/rotation/scale
                // (only opacity animates in), so the recycle is invisible.
                gsap.set(card, { xPercent: target.xPercent, y: target.y, rotate: target.rotate, scale: target.scale * 0.94, opacity: 0, x: 0 });
                grayState[i].g = target.gray;
                card.style.filter = `grayscale(${target.gray}%)`;
                gsap.to(card, { opacity: target.opacity, scale: target.scale, duration: dur * 0.8, ease, overwrite: 'auto' });
            } else if (animate && !reducedMotion) {
                // Distinct emphasis curves per property instead of one
                // lockstep tween, so focus visibly "transfers" rather than
                // everything just sliding together: position/rotation run
                // the full duration, scale settles a touch earlier, opacity
                // is a quick fade, and grayscale is asymmetric — the card
                // losing focus desaturates fast and early, the one gaining
                // it stays muted until it's most of the way home, so the
                // two are never both fully saturated at once.
                gsap.to(card, { xPercent: target.xPercent, y: target.y, rotate: target.rotate, x: 0, duration: dur, ease, overwrite: 'auto' });
                gsap.to(card, { scale: target.scale, duration: dur * 0.85, ease, overwrite: 'auto' });
                gsap.to(card, { opacity: target.opacity, duration: dur * 0.55, ease, overwrite: 'auto' });

                const isLeavingActive = prevOffset === 0 && !isActive;
                const isBecomingActive = isActive && prevOffset !== 0;
                const grayDelay = isBecomingActive ? dur * 0.35 : 0;
                const grayDuration = isLeavingActive ? dur * 0.5 : dur - grayDelay;
                gsap.to(grayState[i], {
                    g: target.gray,
                    duration: grayDuration,
                    delay: grayDelay,
                    ease,
                    overwrite: 'auto',
                    onUpdate: () => { card.style.filter = `grayscale(${grayState[i].g}%)`; },
                });
            } else if (animate) {
                // Reduced motion: one short, simple tween — no split
                // curves, no elaborate focus-transfer choreography.
                gsap.to(card, { xPercent: target.xPercent, y: target.y, rotate: target.rotate, scale: target.scale, opacity: target.opacity, x: 0, duration: dur, ease, overwrite: 'auto' });
                gsap.to(grayState[i], {
                    g: target.gray,
                    duration: dur,
                    ease,
                    overwrite: 'auto',
                    onUpdate: () => { card.style.filter = `grayscale(${grayState[i].g}%)`; },
                });
            } else {
                gsap.set(card, { xPercent: target.xPercent, y: target.y, rotate: target.rotate, scale: target.scale, opacity: target.opacity, x: 0 });
                grayState[i].g = target.gray;
                card.style.filter = `grayscale(${target.gray}%)`;
            }

            prevOffsets[i] = offset;
        });

        onChange?.(currentIndex, animate);
    };

    const goTo = (index, animate = true) => {
        currentIndex = wrap(index);
        layout(animate);
    };

    layout(false);

    // --- Click routing: a click on the active card is left completely
    // alone (the caller's own bubble-phase listener decides what it means
    // — zoom toggle, close, etc.); a click on any other card just makes it
    // active instead. Capture phase so this always runs before the
    // caller's bubble-phase listener on the same elements. ---
    root.addEventListener('click', (e) => {
        if (wasDragging) {
            wasDragging = false;
            e.preventDefault();
            e.stopPropagation();
            return;
        }
        const card = e.target.closest('[data-deck-card]');
        if (!card) return;
        const idx = parseInt(card.dataset.index, 10);
        if (idx !== currentIndex) {
            e.preventDefault();
            e.stopPropagation();
            goTo(idx);
        }
    }, true);

    // --- Drag (mouse + touch via Pointer Events). Yields to the caller's
    // own interaction (e.g. panning an already-zoomed active image) via
    // isDragBlocked, so the two never fight over the same gesture. ---
    let dragging = false;
    let dragStartX = 0;
    let dragDelta = 0;
    let cardWidth = 1;

    root.addEventListener('pointerdown', (e) => {
        if (isDragBlocked?.(e)) return;
        dragging = true;
        wasDragging = false;
        dragStartX = e.clientX;
        dragDelta = 0;
        cardWidth = cards[currentIndex].offsetWidth || root.offsetWidth * 0.6;
        // Deliberately no setPointerCapture here: capturing on every
        // pointerdown — even a plain tap with no actual movement — makes
        // the browser retarget the click that follows to `root` itself
        // instead of whatever was actually pressed (a card/its image),
        // which broke card clicks entirely (they'd resolve to `root`, not
        // `[data-deck-card]`). Capture is only engaged once a real drag is
        // confirmed, below.
    });

    root.addEventListener('pointermove', (e) => {
        if (!dragging) return;
        dragDelta = e.clientX - dragStartX;
        const wasDraggingBefore = wasDragging;
        if (Math.abs(dragDelta) > DRAG_THRESHOLD) wasDragging = true;
        if (wasDragging && !wasDraggingBefore) {
            // Now that this is confirmed to be a real drag (not a click),
            // capture the pointer so the gesture keeps tracking correctly
            // even if it moves outside the deck's bounds.
            root.setPointerCapture?.(e.pointerId);
        }
        if (!wasDragging) return;

        // Proportional, restrained follow: the active card tracks the
        // pointer closely, neighbors trail a bit less — physical without
        // exaggerated rubber-banding.
        const shift = dragDelta * 0.9;
        cards.forEach((card, i) => {
            const off = shortestOffset(i);
            if (Math.abs(off) > MAX_VISIBLE_OFFSET) return;
            const follow = off === 0 ? 1 : 0.65;
            gsap.set(card, { x: shift * follow });
        });
    });

    const endDrag = (e) => {
        if (!dragging) return;
        dragging = false;
        root.releasePointerCapture?.(e.pointerId);
        if (!wasDragging) return;

        const threshold = Math.min(cardWidth * 0.2, 90);
        if (dragDelta <= -threshold) goTo(currentIndex + 1);
        else if (dragDelta >= threshold) goTo(currentIndex - 1);
        else layout(true);
    };
    root.addEventListener('pointerup', endDrag);
    root.addEventListener('pointercancel', endDrag);

    return { goTo, getIndex: () => currentIndex };
}
