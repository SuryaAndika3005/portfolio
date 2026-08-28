// Manual Gallery Reorder (V1.2, Sections 6-11). Two equivalent ways to
// reorder: native HTML5 drag-and-drop on each thumbnail, and a real
// keyboard-operable Move up/down button pair — neither is the only way
// (Section 7). Every reorder action re-syncs a set of hidden
// gallery_order[] inputs from the current DOM order; that's the only
// thing actually submitted with the form (Section 9 — reordering piggy-
// backs on the normal Project save, no dedicated endpoint, no re-upload).
// The server (ProjectController::reorderedGallery) revalidates this array
// as a permutation of the project's own owned gallery paths before
// trusting it — this file only produces a convenient DOM-order list, it
// is not itself a security boundary.

document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('admin-gallery-grid');
    const orderInputsContainer = document.getElementById('admin-gallery-order-inputs');
    const liveRegion = document.getElementById('admin-gallery-live-region');
    if (!grid || !orderInputsContainer) return;

    const announce = (text) => {
        if (liveRegion) liveRegion.textContent = text;
    };

    function items() {
        return Array.from(grid.querySelectorAll('.admin-gallery-item'));
    }

    /**
     * @param {boolean} silent  True only for the initial page-load sync —
     *        every other call represents a real reorder, so it dispatches
     *        a bubbling 'change' the Project form's own unsaved-changes
     *        listener (project-assistant.js) picks up automatically,
     *        covering manual drag/button reorders and an applied AI
     *        suggestion (which also routes through here) with one signal
     *        instead of marking dirty separately in three places.
     */
    function syncOrder(silent = false) {
        const paths = items().map((el) => el.dataset.path);

        orderInputsContainer.replaceChildren();
        paths.forEach((path) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'gallery_order[]';
            input.value = path;
            orderInputsContainer.append(input);
        });

        items().forEach((el, i) => {
            const badge = el.querySelector('.admin-gallery-order');
            if (badge) badge.textContent = String(i + 1);
            const up = el.querySelector('.admin-gallery-move[data-direction="up"]');
            const down = el.querySelector('.admin-gallery-move[data-direction="down"]');
            if (up) up.disabled = i === 0;
            if (down) down.disabled = i === paths.length - 1;
        });

        if (!silent) {
            orderInputsContainer.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function moveItem(el, direction) {
        const sibling = direction === 'up' ? el.previousElementSibling : el.nextElementSibling;
        if (!sibling) return;

        if (direction === 'up') {
            grid.insertBefore(el, sibling);
        } else {
            grid.insertBefore(sibling, el);
        }

        syncOrder();
        const newIndex = items().indexOf(el) + 1;
        announce(`Image moved to position ${newIndex}.`);
        el.querySelector(`.admin-gallery-move[data-direction="${direction}"]`)?.focus();
    }

    grid.addEventListener('click', (e) => {
        const btn = e.target.closest('.admin-gallery-move');
        if (!btn) return;
        e.preventDefault();
        const item = btn.closest('.admin-gallery-item');
        if (item) moveItem(item, btn.dataset.direction);
    });

    // --- Drag and drop (pointer-capable devices only get this extra path;
    // the buttons above already work everywhere) ---------------------------

    let dragEl = null;

    grid.addEventListener('dragstart', (e) => {
        const item = e.target.closest('.admin-gallery-item');
        if (!item) return;
        dragEl = item;
        item.classList.add('is-dragging');
        e.dataTransfer.effectAllowed = 'move';
        try {
            e.dataTransfer.setData('text/plain', item.dataset.path);
        } catch (err) {
            // Some browsers require setData for drag to work at all; if it
            // throws (rare), the drag still proceeds using dragEl alone.
        }
    });

    grid.addEventListener('dragend', () => {
        dragEl?.classList.remove('is-dragging');
        items().forEach((el) => el.classList.remove('is-drag-over'));
        dragEl = null;
    });

    grid.addEventListener('dragover', (e) => {
        const target = e.target.closest('.admin-gallery-item');
        if (!target || !dragEl || target === dragEl) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';

        items().forEach((el) => el.classList.toggle('is-drag-over', el === target));

        const rect = target.getBoundingClientRect();
        const before = e.clientX - rect.left < rect.width / 2;
        grid.insertBefore(dragEl, before ? target : target.nextSibling);
    });

    grid.addEventListener('drop', (e) => {
        if (!dragEl) return;
        e.preventDefault();
        items().forEach((el) => el.classList.remove('is-drag-over'));
        syncOrder();
        const newIndex = items().indexOf(dragEl) + 1;
        announce(`Image moved to position ${newIndex}.`);
    });

    // Fired by project-assistant.js after it reorders the DOM nodes itself
    // (Apply Suggested Order, V1.2 Section 13) — this file still owns the
    // actual hidden-input/badge/button resync, so that logic isn't
    // duplicated in two places.
    grid.addEventListener('gallery-order-applied', () => {
        syncOrder();
        announce('Suggested gallery order applied.');
    });

    syncOrder(true);
});
