// Fullscreen GSAP slider for the project detail page. Every project image
// (main showcase + gallery) is pre-rendered as a persistent slide in
// #modalTrack; opening/navigating just tweens the track's x position instead
// of swapping a single <img> src, so GSAP drives an actual slide transition.
// Each image fits the screen by default (no forced scrolling) — scroll, a
// single tap, or drag zooms in and pans, so tall/small composite exports
// stay readable without dominating the layout at full size.
import gsap from 'gsap';

document.addEventListener('DOMContentLoaded', () => {
    // --- Peek stack on the detail-page showcase preview ---
    // The two tucked-behind gallery cards are the card's only hover effect
    // (no CTA text/dim overlay on the main image), so they need to read as a
    // deliberate "pop" rather than a plain fade — GSAP's back-out easing
    // gives that little overshoot instead of CSS's flat ease-out.
    const peekWrapper = document.querySelector('[data-peek-wrapper]');
    if (peekWrapper) {
        const peeks = Array.from(peekWrapper.querySelectorAll('[data-peek]'));
        if (peeks.length && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            peekWrapper.addEventListener('mouseenter', () => {
                gsap.to(peeks, {
                    opacity: 1,
                    scale: 1,
                    duration: 0.45,
                    ease: 'back.out(1.3)',
                    stagger: 0.06,
                    overwrite: 'auto',
                });
            });
            peekWrapper.addEventListener('mouseleave', () => {
                gsap.to(peeks, {
                    opacity: 0,
                    scale: 0.9,
                    duration: 0.25,
                    ease: 'power2.in',
                    overwrite: 'auto',
                });
            });
        }
    }

    // --- Fullscreen slider ---
    const modal = document.getElementById('imageModal');
    const track = document.getElementById('modalTrack');
    const closeBtn = document.getElementById('modalClose');
    const prevBtn = document.getElementById('modalPrev');
    const nextBtn = document.getElementById('modalNext');
    const counter = document.getElementById('modalCounter');
    const zoomHint = document.getElementById('modalZoomHint');

    if (!modal || !track) return;

    const slides = Array.from(track.children);
    const triggers = Array.from(document.querySelectorAll('[data-modal-trigger]'));
    if (!slides.length || !triggers.length) return;

    const hasMultiple = slides.length > 1;
    let currentIndex = 0;

    // --- Zoom/pan, scoped per slide ---
    // Listeners are on the whole slide (not just the <img>) because a
    // fit-to-screen image — especially a very tall UI/UX composite shrunk
    // down to a thin vertical strip — can leave most of the slide as empty
    // space around it; zooming needs to work no matter where in the slide
    // the mouse is, not just on the handful of pixels the image occupies.
    const ZOOM_MIN = 1;
    const ZOOM_MAX = 4;
    const zoomState = new WeakMap();

    const stateFor = (img) => {
        if (!zoomState.has(img)) zoomState.set(img, { scale: 1, x: 0, y: 0 });
        return zoomState.get(img);
    };

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
    };

    const resetZoom = (img) => applyZoom(img, 1, 0, 0, false);
    const resetAllZoom = () => slides.forEach((slide) => {
        const img = slide.querySelector('[data-zoom-img]');
        if (img) resetZoom(img);
    });

    slides.forEach((slide) => {
        const img = slide.querySelector('[data-zoom-img]');
        if (!img) return;

        // Zoom centered on the cursor: convert a viewport point to a delta
        // against the image's current pan so the point under the cursor
        // stays put as the scale changes.
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

        slide.addEventListener('wheel', (e) => {
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

        slide.addEventListener('pointerdown', (e) => {
            startX = e.clientX;
            startY = e.clientY;
            wasDragging = false;
            const state = stateFor(img);
            if (state.scale <= 1) return;
            dragging = true;
            originX = state.x;
            originY = state.y;
            slide.setPointerCapture(e.pointerId);
            img.style.cursor = 'grabbing';
        });

        slide.addEventListener('pointermove', (e) => {
            // Movement only counts as a drag while actually panning (scale >
            // 1 and the pointer is down) — checking it unconditionally would
            // also catch the ordinary cursor jitter between mousedown and
            // mouseup of a plain click, which was silently swallowing the
            // very next tap-to-zoom-out click every time.
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
            if (e && slide.hasPointerCapture?.(e.pointerId)) slide.releasePointerCapture(e.pointerId);
        };
        slide.addEventListener('pointerup', endDrag);
        slide.addEventListener('pointercancel', endDrag);

        // A single tap/click toggles zoom (in on the tapped point, or back
        // out if already zoomed) — but only when it wasn't actually a drag.
        slide.addEventListener('click', (e) => {
            if (wasDragging) {
                wasDragging = false;
                return;
            }
            const state = stateFor(img);
            if (state.scale > 1) {
                applyZoom(img, 1, 0, 0);
            } else {
                zoomAt(e.clientX, e.clientY, 2.4);
            }
        });
    });

    // --- Slide navigation ---
    const updateChrome = () => {
        if (counter) {
            counter.textContent = `${currentIndex + 1} / ${slides.length}`;
            counter.classList.toggle('hidden', !hasMultiple);
        }
        prevBtn?.classList.toggle('hidden', !hasMultiple);
        nextBtn?.classList.toggle('hidden', !hasMultiple);
    };

    const goTo = (index, animate = true) => {
        currentIndex = (index + slides.length) % slides.length;
        resetAllZoom();
        gsap.to(track, {
            x: -currentIndex * window.innerWidth,
            duration: animate ? 0.6 : 0,
            ease: 'power3.inOut',
        });
        updateChrome();
    };

    const openModal = (index) => {
        goTo(index, false);
        modal.classList.remove('hidden');
        modal.classList.add('block');
        document.body.style.overflow = 'hidden';
        zoomHint?.classList.remove('hidden');
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('block');
        document.body.style.overflow = 'auto';
        resetAllZoom();
    };

    triggers.forEach((trigger) => {
        const index = parseInt(trigger.dataset.slideIndex ?? '0', 10);
        trigger.addEventListener('click', () => openModal(index));
    });

    prevBtn?.addEventListener('click', () => goTo(currentIndex - 1));
    nextBtn?.addEventListener('click', () => goTo(currentIndex + 1));

    closeBtn?.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', (e) => {
        if (modal.classList.contains('hidden')) return;
        if (e.key === 'Escape') closeModal();
        if (e.key === 'ArrowLeft') goTo(currentIndex - 1);
        if (e.key === 'ArrowRight') goTo(currentIndex + 1);
    });
    window.addEventListener('resize', () => {
        if (!modal.classList.contains('hidden')) goTo(currentIndex, false);
    });
});
