// Admin workspace: microinteractions (mobile sidebar drawer, delete
// confirmation dialog, auto-submitting category filter) plus the shared
// async-action feedback utility used across Admin (V1.2 UX Pass 2). No
// entrance/reveal motion, no continuous animation — see
// PORTFOLIO_ART_DIRECTION.md's Admin motion hierarchy (Batch 8A brief,
// Section 34).

// --- Shared button loading / success state (Sections 16-27) ------------
//
// Defined at top level (not inside DOMContentLoaded) so it exists as soon
// as this script runs — admin.js is always the first of the four Admin
// entries in @vite(), and module scripts execute in order before any of
// them fire DOMContentLoaded, so project-assistant.js / gallery-reorder.js
// can rely on window.AdminFeedback being ready inside their own
// DOMContentLoaded handlers without a new Vite entry or an import (the
// established gotcha in this project: a new JS file must be registered as
// its own Vite entry, so sharing via window avoids adding one just for
// this).
window.AdminFeedback = (() => {
    function buildSpinner() {
        const span = document.createElement('span');
        span.className = 'admin-spinner';
        span.setAttribute('aria-hidden', 'true');
        return span;
    }

    /** Puts a button into its loading state: spinner + task-specific text,
     *  disabled, aria-busy, width preserved so the label swap doesn't
     *  reflow the row (Section 16). */
    function setActionLoading(el, loadingText) {
        if (!el) return;
        if (el.dataset.originalText === undefined) {
            el.dataset.originalText = el.textContent;
        }
        if (!el.style.minWidth) {
            el.style.minWidth = `${el.offsetWidth}px`;
        }
        el.disabled = true;
        el.setAttribute('aria-busy', 'true');
        el.classList.add('is-loading');
        el.classList.remove('is-success');
        el.replaceChildren(buildSpinner(), document.createTextNode(loadingText));
    }

    /** Returns a button to its normal, clickable, original-label state —
     *  the recovery path for both a completed success flash and an error
     *  (errors are shown in the section, not on the button itself; the
     *  button just becomes clickable again, Section 25). */
    function resetAction(el) {
        if (!el) return;
        el.disabled = false;
        el.removeAttribute('aria-busy');
        el.classList.remove('is-loading', 'is-success');
        if (el.dataset.originalText !== undefined) {
            el.textContent = el.dataset.originalText;
            delete el.dataset.originalText;
        }
        el.style.minWidth = '';
    }

    /** Brief, contextual success label directly on the button (e.g. "Cover
     *  updated", "Applied") — never a generic "Success!" (Section 20). By
     *  default reverts to the original label after holdMs; pass
     *  thenReset:false for a terminal state (e.g. "Applied" that should
     *  stay disabled). */
    function setActionSuccess(el, text, { holdMs = 1400, thenReset = true } = {}) {
        if (!el) return;
        el.classList.remove('is-loading');
        el.removeAttribute('aria-busy');
        el.classList.add('is-success');
        el.textContent = text;
        if (thenReset) {
            el.disabled = false;
            window.setTimeout(() => resetAction(el), holdMs);
        }
    }

    /**
     * Provider-usage audit (V1.3, Section 8): after a rate-limit response,
     * the triggering button goes back to its normal label (the error
     * itself is already visible in the section, not on the button) but
     * stays disabled for a restrained cooldown — no countdown UI, it just
     * quietly becomes clickable again once the cooldown elapses. This
     * exists to stop a frustrated repeat-click from adding to the same
     * RPM burst that just caused the 429, not to punish the user.
     */
    function cooldown(el, ms) {
        if (!el) return;
        resetAction(el);
        el.disabled = true;
        const until = Date.now() + ms;
        el.dataset.cooldownUntil = String(until);
        window.setTimeout(() => {
            if (Number(el.dataset.cooldownUntil) === until) {
                el.disabled = false;
                delete el.dataset.cooldownUntil;
            }
        }, ms);
    }

    return {
        setActionLoading, resetAction, setActionSuccess, cooldown,
    };
})();

document.addEventListener('DOMContentLoaded', () => {
    // --- Mobile sidebar drawer ---
    const menuBtn = document.getElementById('admin-menu-btn');
    const backdrop = document.getElementById('admin-sidebar-backdrop');
    const closeBtn = document.getElementById('admin-sidebar-close');

    const closeDrawer = () => {
        document.body.classList.remove('admin-sidebar-open');
        menuBtn?.setAttribute('aria-expanded', 'false');
    };
    const openDrawer = () => {
        document.body.classList.add('admin-sidebar-open');
        menuBtn?.setAttribute('aria-expanded', 'true');
    };

    menuBtn?.addEventListener('click', () => {
        const isOpen = document.body.classList.contains('admin-sidebar-open');
        isOpen ? closeDrawer() : openDrawer();
    });
    backdrop?.addEventListener('click', closeDrawer);
    closeBtn?.addEventListener('click', closeDrawer);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeDrawer();
    });

    // --- Delete confirmation dialog ---
    // Each delete form carries data-confirm-title (the project's own name)
    // instead of the browser's native confirm(). The dialog is a single
    // shared <dialog> element (see admin/_layout.blade.php); its own form
    // action/method are rewritten to match whichever row triggered it, so
    // one dialog serves every row without cloning markup per-project.
    const dialog = document.getElementById('admin-delete-dialog');
    const dialogTitle = document.getElementById('admin-delete-dialog-title');
    const dialogForm = document.getElementById('admin-delete-dialog-form');

    if (dialog && dialogForm) {
        document.querySelectorAll('[data-confirm-delete]').forEach((form) => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                dialogForm.action = form.action;
                if (dialogTitle) {
                    dialogTitle.textContent = form.dataset.confirmTitle || 'this project';
                }
                dialog.showModal();
            });
        });

        dialog.addEventListener('click', (e) => {
            // Backdrop click (native <dialog> reports the click target as
            // the dialog element itself only when it lands outside the
            // rendered content box).
            const rect = dialog.getBoundingClientRect();
            const inside = e.clientX >= rect.left && e.clientX <= rect.right
                && e.clientY >= rect.top && e.clientY <= rect.bottom;
            if (!inside) dialog.close();
        });

        // Delete is a real navigation (no fetch) — the loading state just
        // needs to survive until the redirect lands, never reset (Section
        // 30). Guards against a double-click re-submitting the same
        // delete. Cancel is disabled too so it can't look like an escape
        // hatch mid-request.
        dialogForm.addEventListener('submit', () => {
            const confirmBtn = dialogForm.querySelector('button[type="submit"]');
            const cancelBtn = dialog.querySelector('.admin-dialog-actions button[type="button"]');
            window.AdminFeedback?.setActionLoading(confirmBtn, 'Deleting…');
            if (cancelBtn) cancelBtn.disabled = true;
        });
    }

    // --- Save / Create loading state for real (non-AJAX) form posts -----
    //
    // Project create/edit, Category edit, Experience create/edit are all
    // plain <form method="POST"> submits — a real page navigation, not
    // fetch(). A validation failure redirects back with a fresh page load,
    // which already clears any disabled/loading state for free, so this
    // only ever needs to set the loading state, never reset it (Section
    // 26). Explicitly skips the delete dialog and the two AJAX-driven AI
    // Workspace forms (assistant-reply-form/assistant-refine-form), which
    // already preventDefault() and manage their own state.
    const NON_LOADING_FORM_IDS = new Set(['admin-delete-dialog-form', 'assistant-reply-form', 'assistant-refine-form']);
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (form.method.toUpperCase() !== 'POST') return;
        if (NON_LOADING_FORM_IDS.has(form.id)) return;
        // Row-level delete forms never really submit here — they're
        // intercepted (preventDefault) and handed to the shared confirm
        // dialog above, which has its own "Deleting…" state. Without this
        // check, clicking a row's Delete link would briefly (and
        // wrongly) flash "Saving…" on it before the dialog even opens.
        if (form.hasAttribute('data-confirm-delete')) return;

        const submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn || submitBtn.disabled) return;

        const label = submitBtn.textContent.trim().toLowerCase();
        let loadingText = 'Saving…';
        if (label.startsWith('create')) {
            loadingText = `Creating ${label.replace(/^create\s*/, '') || 'item'}…`;
        }

        window.AdminFeedback?.setActionLoading(submitBtn, loadingText);
    });

    // --- Truthful "selected, will upload on save" note (Section 32) -----
    //
    // These file inputs are plain <input type=file>, submitted only when
    // the form is saved — nothing uploads via AJAX. Inventing an
    // "Uploading…" state would misrepresent that, so this only ever
    // reports what actually happened: a file was chosen locally.
    document.querySelectorAll('input[type="file"]').forEach((input) => {
        input.addEventListener('change', () => {
            let note = input.nextElementSibling;
            if (!note || !note.classList.contains('admin-file-selected-note')) {
                note = document.createElement('p');
                note.className = 'admin-field-hint admin-file-selected-note';
                input.insertAdjacentElement('afterend', note);
            }
            if (!input.files.length) {
                note.remove();
                return;
            }
            note.textContent = input.files.length > 1
                ? `${input.files.length} images selected · Will upload when saved`
                : `"${input.files[0].name}" selected · Will upload when saved`;
        });
    });

    // --- Category filter: auto-submits so there's no extra "Apply" button
    // for a single select; the text search still submits via its own
    // button/Enter so typing doesn't fire a request per keystroke. ---
    document.getElementById('admin-filter-category')?.addEventListener('change', (e) => {
        e.target.form?.requestSubmit();
    });
});
