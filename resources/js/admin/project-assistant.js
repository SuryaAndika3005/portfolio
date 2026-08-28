// AI Workspace (V1.1 backend, V1.2 UX Refinement front-end). Every fetch()
// call still goes through callAssistant()/postJson(), always sending the
// CSRF token from <head> — the browser never talks to Gemini directly
// (Section 0). AI text is always rendered via textContent/value, never
// innerHTML (Section 81).
//
// V1.2 UX Refinement: Tool Suggestions/Cover Ranking/Gallery Order/Quality
// Review/SEO triggers now live in _form.blade.php, contextually next to
// the field each one affects, instead of inside the AI Workspace panel.
// This file's endpoint-calling logic for each of those is UNCHANGED —
// only the element ids each handler binds to were updated to match their
// new location (Section 47: relocate, don't duplicate). All five still
// read their URL from #assistant-panel's own data attributes, which
// still exist and are unchanged, regardless of where the panel's trigger
// button physically sits on the page now.

document.addEventListener('DOMContentLoaded', () => {
    const panel = document.getElementById('assistant-panel');
    if (!panel) return; // Not every Admin page renders the Project form.

    const trigger = document.getElementById('assistant-trigger');
    const backdrop = document.getElementById('assistant-backdrop');
    const closeBtn = document.getElementById('assistant-close');
    const errorSlot = document.getElementById('assistant-error-slot');
    const loading = document.getElementById('assistant-loading');
    const loadingText = document.getElementById('assistant-loading-text');

    const stepUnderstanding = document.getElementById('assistant-step-understanding');
    const stepConversation = document.getElementById('assistant-step-conversation');
    const stepDraft = document.getElementById('assistant-step-draft');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // --- Panel open/close (real button, aria-expanded, focus-visible, Escape) ---

    let lastFocused = null;

    const openPanel = () => {
        lastFocused = document.activeElement;
        document.body.classList.add('assistant-open');
        trigger.setAttribute('aria-expanded', 'true');
        panel.querySelector('button, textarea, input, select')?.focus();
    };

    const closePanel = () => {
        document.body.classList.remove('assistant-open');
        trigger.setAttribute('aria-expanded', 'false');
        (lastFocused ?? trigger).focus();
    };

    trigger.addEventListener('click', openPanel);
    closeBtn.addEventListener('click', closePanel);
    backdrop.addEventListener('click', closePanel);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && document.body.classList.contains('assistant-open')) closePanel();
    });

    // Optional workflow nav (Sections 35-36, 70) — "Understand" opens this
    // same panel; the other four are plain anchor links to sections that
    // are already freely editable (handled by scrollToAnchor below).
    document.getElementById('workflow-nav-understand')?.addEventListener('click', openPanel);

    // --- Loading / error / duplicate-submission guard ---------------------

    let requestInFlight = false;

    const setLoading = (text) => {
        if (text) {
            loadingText.textContent = text;
            loading.hidden = false;
        } else {
            loading.hidden = true;
        }
    };

    const showError = (message) => {
        const p = document.createElement('p');
        p.className = 'assistant-error';
        p.textContent = message;
        errorSlot.replaceChildren(p);
    };

    const clearError = () => errorSlot.replaceChildren();

    /**
     * Every AI request goes through here. A single in-flight flag blocks
     * any concurrent request, enough to stop double-click/rapid-repeat
     * submissions without a separate per-button disabled/enabled dance.
     *
     * @param {HTMLElement|null} button  When given, that specific button
     *        (not just the panel's shared loading row) shows the loading
     *        state — required for the five contextual triggers (Tools/
     *        Cover/Gallery/Review/SEO) that live in the main form, not
     *        inside this panel: if the panel is closed, #assistant-loading
     *        is off-screen and clicking one of those gave zero visible
     *        feedback (V1.2 UX Pass 2, Section 92 — confirmed bug, fixed
     *        here rather than only adding a second indicator on top).
     * @param {HTMLElement|null} localErrorEl  Same reasoning applied to
     *        errors (Section 25: errors belong in the section the action
     *        happened in) — #assistant-error-slot lives inside this panel
     *        too, so the five contextual triggers also get their own
     *        local error slot next to their own button.
     */
    // Provider-usage audit (V1.3, Section 8): restrained, no dramatic
    // countdown — the button just quietly stays disabled for this long
    // after a rate-limit response before becoming clickable again.
    const RATE_LIMIT_COOLDOWN_MS = 15000;

    async function callAssistant(url, formData, loadingMessage, button = null, localErrorEl = null) {
        if (requestInFlight) return null;
        requestInFlight = true;
        clearError();
        if (localErrorEl) localErrorEl.hidden = true;
        setLoading(loadingMessage);
        if (button) window.AdminFeedback?.setActionLoading(button, loadingMessage);

        let rateLimited = false;

        try {
            const response = await fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
                body: formData,
            });

            let payload;
            try {
                payload = await response.json();
            } catch {
                throw new Error('AI Workspace returned an unexpected response. Your Project form has not been changed.');
            }

            if (!response.ok) {
                const message = response.status === 429
                    ? 'AI is temporarily unavailable due to usage limits. Your project has not been changed. Try again shortly.'
                    : (payload.message || "Couldn't complete the request. Your Project form has not been changed.");
                const error = new Error(message);
                error.status = response.status;
                throw error;
            }

            return payload;
        } catch (err) {
            const message = err.message || "Couldn't complete the request. Your Project form has not been changed.";
            showError(message);
            if (localErrorEl) {
                localErrorEl.textContent = message;
                localErrorEl.hidden = false;
            }
            rateLimited = err.status === 429;
            return null;
        } finally {
            requestInFlight = false;
            setLoading(null);
            if (button) {
                if (rateLimited) {
                    window.AdminFeedback?.cooldown(button, RATE_LIMIT_COOLDOWN_MS);
                } else {
                    window.AdminFeedback?.resetAction(button);
                }
            }
        }
    }

    const postJson = (url, obj, loadingMessage, button = null, localErrorEl = null) => {
        const fd = new FormData();
        Object.entries(obj).forEach(([key, value]) => fd.append(key, value));
        return callAssistant(url, fd, loadingMessage, button, localErrorEl);
    };

    // --- Unsaved-changes indicator (Sections 29, 36) ------------------------
    //
    // One shared pill covers every source of an unsaved edit in the Project
    // form: typed fields (native 'input'/'change' bubbling), an applied AI
    // draft field (applyToField already dispatches a bubbling 'input'), a
    // confirmed/dismissed suggested tool, and a manual or AI-applied gallery
    // reorder (gallery-reorder.js dispatches its own bubbling 'change').
    // Explicitly ignores the field-level "Refine with AI" feedback textareas
    // (.admin-refine-box) — those live inside this form's DOM but aren't
    // Project fields, so typing feedback there must not claim an unsaved
    // change.
    const projectForm = document.getElementById('admin-project-form');
    const unsavedIndicator = document.getElementById('admin-unsaved-indicator');

    function markFormDirty() {
        if (unsavedIndicator) unsavedIndicator.hidden = false;
    }

    projectForm?.addEventListener('input', (e) => {
        if (e.target.closest('.admin-refine-box')) return;
        markFormDirty();
    });
    projectForm?.addEventListener('change', (e) => {
        if (e.target.closest('.admin-refine-box')) return;
        markFormDirty();
    });

    // --- Reading the live Project form -------------------------------------

    const val = (id) => document.getElementById(id)?.value ?? '';

    function collectFacts() {
        const categorySelect = document.getElementById('category_id');
        const categoryLabel = categorySelect?.selectedOptions?.[0]?.textContent?.trim() ?? '';
        const tools = Array.from(document.querySelectorAll('input[name="tools[]"]:checked')).map((el) => el.value);

        return {
            title: val('title'),
            category: categoryLabel,
            role: val('role'),
            client: val('client'),
            year: val('year'),
            tools,
            notes: val('assistant-notes'),
        };
    }

    function appendImageInputs(formData) {
        ['image', 'cover_image'].forEach((name) => {
            const input = document.querySelector(`input[type="file"][name="${name}"]`);
            if (input?.files?.[0]) formData.append(name, input.files[0]);
        });

        const gallery = document.querySelector('input[type="file"][name="gallery[]"]');
        if (gallery?.files) {
            Array.from(gallery.files).forEach((file) => formData.append('gallery[]', file));
        }

        document.querySelectorAll('#assistant-existing-images input:checked').forEach((el) => {
            formData.append('existing_images[]', el.value);
        });
    }

    // Caps total checked existing-image boxes at 8 client-side too, so the
    // picker never silently disagrees with the server's own cap.
    document.getElementById('assistant-existing-images')?.addEventListener('change', () => {
        const boxes = Array.from(document.querySelectorAll('#assistant-existing-images input'));
        const checkedCount = boxes.filter((b) => b.checked).length;
        boxes.forEach((b) => {
            if (!b.checked) b.disabled = checkedCount >= 8;
        });
    });

    // --- Shared helpers -----------------------------------------------------

    // Gallery/cover paths are stored in two different conventions
    // (confirmed against ProjectController): gallery_images entries already
    // include a "storage/" prefix; image_path/cover_image_path don't. Both
    // can appear here, so this normalizes either into a root-relative URL.
    function pathToImgSrc(path) {
        const normalized = path.startsWith('storage/') ? path : `storage/${path}`;
        return `/${normalized}`;
    }

    // analyze()'s own array-vs-scalar FormData handling, factored out so
    // Tool Suggestions/Quality Review/SEO can reuse it.
    function factsFormData(facts) {
        const fd = new FormData();
        Object.entries(facts).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                value.forEach((v) => fd.append(`${key}[]`, v));
            } else if (value !== '' && value !== null && value !== undefined) {
                fd.append(key, value);
            }
        });
        return fd;
    }

    function renderList(containerId, listId, items) {
        const box = document.getElementById(containerId);
        const list = document.getElementById(listId);
        list.replaceChildren();
        if (items && items.length) {
            items.forEach((text) => {
                const li = document.createElement('li');
                li.textContent = text;
                list.append(li);
            });
            box.hidden = false;
        } else {
            box.hidden = true;
        }
    }

    // Field-key -> form element id (Section 69's reusable focus/scroll
    // helper). Covers the four refine-able case-study fields plus a few
    // other field keys Quality Review/Completeness might reference.
    const FIELD_ELEMENT_IDS = {
        description: 'description', problem: 'problem', process: 'process', result: 'result',
        title: 'title', category: 'category_id', role: 'role', client: 'client', year: 'year',
    };

    /**
     * Smooth-scrolls a real form field into view and applies a brief
     * (900ms) highlight class — never a flashing loop (Section 69).
     * Moves focus to the field so keyboard/screen-reader users land
     * somewhere sensible too, not just a visual scroll (Section 50).
     */
    function scrollToField(key) {
        const id = FIELD_ELEMENT_IDS[key];
        const el = id ? document.getElementById(id) : null;
        if (!el) return;

        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        el.classList.add('admin-field-highlight');
        window.setTimeout(() => el.classList.remove('admin-field-highlight'), 900);
        window.setTimeout(() => el.focus({ preventScroll: true }), 300);
    }

    // Plain anchor-section scroll for the optional workflow nav and the
    // Project Status "Open Review" button (Sections 6, 70) — restrained,
    // no scrollspy (Section 70: "do not over-engineer if it creates
    // fragility"), just a smooth jump to an existing, freely-editable id.
    document.querySelectorAll('[data-scroll-anchor]').forEach((link) => {
        link.addEventListener('click', (e) => {
            const id = link.dataset.scrollAnchor;
            const target = document.getElementById(id);
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    // --- A. Understand: Analyze ---------------------------------------------

    const STATUS_LABELS = {
        confirmed: 'Confirmed',
        observed: 'Observed',
        partial: 'Needs info',
        needs_context: 'Needs info',
        not_available: 'Not tracked',
    };

    const UNDERSTANDING_LABELS = {
        visual_direction: 'Visual direction',
        role: 'Role',
        objective: 'Objective',
        main_challenge: 'Main challenge',
        process: 'Process',
        outputs: 'Outputs',
        measured_results: 'Measured results',
    };

    function understandingRow(label, statusValue) {
        const row = document.createElement('div');
        row.className = 'assistant-understanding-row';

        const labelEl = document.createElement('span');
        labelEl.className = 'assistant-understanding-label';
        labelEl.textContent = label;

        const statusEl = document.createElement('span');
        statusEl.className = 'assistant-status';
        statusEl.dataset.status = statusValue;
        statusEl.textContent = STATUS_LABELS[statusValue] ?? statusValue;

        row.append(labelEl, statusEl);
        return row;
    }

    /**
     * Groups understanding rows by whether they're resolved
     * (confirmed/observed) or not (partial/needs_context/not_available),
     * showing unresolved ones first and prominently, with known/resolved
     * facts tucked into a collapsible <details> — Section 65's "prioritize
     * unresolved information, don't dump dozens of fact rows" applied to
     * a list that in practice only ever has 7 rows, so the grouping still
     * matters even at this small scale.
     */
    function renderUnderstanding(understanding) {
        const unresolvedBox = document.getElementById('assistant-understanding-unresolved');
        const knownBox = document.getElementById('assistant-understanding-known-rows');
        const knownDetails = document.getElementById('assistant-understanding-known');
        unresolvedBox.replaceChildren();
        knownBox.replaceChildren();

        const status = understanding?.status ?? {};
        let unresolvedCount = 0;
        let knownCount = 0;

        Object.entries(UNDERSTANDING_LABELS).forEach(([key, label]) => {
            const value = status[key];
            if (!value) return;

            const row = understandingRow(label, value);
            if (value === 'confirmed' || value === 'observed') {
                knownBox.append(row);
                knownCount++;
            } else {
                unresolvedBox.append(row);
                unresolvedCount++;
            }
        });

        knownDetails.hidden = knownCount === 0;
        knownDetails.open = false;

        const inferences = understanding?.inferences_needing_confirmation ?? [];
        const inferencesBox = document.getElementById('assistant-inferences');
        const inferencesList = document.getElementById('assistant-inferences-list');
        inferencesList.replaceChildren();
        if (inferences.length) {
            inferences.forEach((text) => {
                const li = document.createElement('li');
                li.textContent = text;
                inferencesList.append(li);
            });
            inferencesBox.hidden = false;
        } else {
            inferencesBox.hidden = true;
        }

        const readyNote = document.getElementById('assistant-understanding-ready');
        readyNote.hidden = !(unresolvedCount === 0 && inferences.length === 0);

        stepUnderstanding.hidden = false;
    }

    function renderTranscript(entries) {
        const el = document.getElementById('assistant-transcript');
        el.replaceChildren();
        (entries ?? []).forEach(({ role, text }) => {
            const div = document.createElement('div');
            div.className = role === 'user' ? 'assistant-message assistant-message--user' : 'assistant-message';
            div.textContent = text;
            el.append(div);
        });
        el.scrollTop = el.scrollHeight;
    }

    const analyzeBtn = document.getElementById('assistant-analyze-btn');
    analyzeBtn.addEventListener('click', async () => {
        const facts = collectFacts();
        const fd = new FormData();
        Object.entries(facts).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                value.forEach((v) => fd.append(`${key}[]`, v));
            } else {
                fd.append(key, value);
            }
        });
        appendImageInputs(fd);

        const result = await callAssistant(panel.dataset.analyzeUrl, fd, 'Understanding project…', analyzeBtn);
        if (!result) return;

        renderUnderstanding(result.understanding);
        renderTranscript(result.transcript);
        stepConversation.hidden = false;
    });

    // --- C. Reply ------------------------------------------------------

    document.getElementById('assistant-reply-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('assistant-reply-input');
        const message = input.value.trim();
        if (!message) return;

        const sendBtn = e.target.querySelector('button[type="submit"]');
        const result = await postJson(panel.dataset.replyUrl, { message }, 'Sending context…', sendBtn);
        if (!result) return;

        input.value = '';
        renderTranscript(result.transcript);
    });

    document.getElementById('assistant-generate-draft-btn').addEventListener('click', (e) => generateDraft(e.currentTarget));
    document.getElementById('assistant-regenerate-btn').addEventListener('click', (e) => generateDraft(e.currentTarget));

    let currentDraft = null;

    function renderDraft(draft) {
        currentDraft = draft;
        ['en', 'id'].forEach((lang) => {
            ['description', 'problem', 'process', 'result'].forEach((field) => {
                const textarea = document.querySelector(`.assistant-draft-textarea[data-field="${field}"][data-lang="${lang}"]`);
                if (textarea) textarea.value = draft[lang]?.[field] ?? '';
            });
        });
        renderList('assistant-assumptions', 'assistant-assumptions-list', draft.assumptions);
        renderList('assistant-missing', 'assistant-missing-list', draft.missing_information);
        stepDraft.hidden = false;
        stepDraft.scrollIntoView({ block: 'nearest' });
    }

    function markChangedFields(changed) {
        document.querySelectorAll('.assistant-draft-field').forEach((el) => {
            const key = `${el.dataset.lang}.${el.dataset.field}`;
            el.dataset.changed = String((changed ?? []).includes(key));
        });
    }

    async function generateDraft(button = null) {
        // Regenerating keeps the existing draft visible underneath instead
        // of blanking the section — only the triggering button's own label
        // says a new one is on the way (Section 59).
        const loadingText = button?.id === 'assistant-regenerate-btn' ? 'Updating draft…' : 'Generating draft…';
        const result = await postJson(panel.dataset.draftUrl, {}, loadingText, button);
        if (!result) return;
        renderDraft(result);
        markChangedFields([]);
    }

    // --- D. Refine -------------------------------------------------

    /**
     * @param  {string}  feedback
     * @param  {string|null}  targetFieldOverride  When set (field-level
     *         "Refine with AI"), used instead of reading the panel's own
     *         "All fields" dropdown — the field is already known, the
     *         user shouldn't have to re-pick it (Section 29).
     * @param  {string|null}  targetLanguageOverride
     * @param  {HTMLElement|null}  button  Which control triggered this, so
     *         its own loading state can show (Section 16).
     */
    async function submitRefine(feedback, targetFieldOverride = null, targetLanguageOverride = null, button = null) {
        if (!feedback) return null;
        const targetField = targetFieldOverride ?? document.getElementById('assistant-refine-field').value;
        const targetLanguage = targetLanguageOverride ?? document.getElementById('assistant-refine-language').value;
        const label = targetField ? `Refining ${targetField}…` : 'Refining draft…';

        const result = await postJson(panel.dataset.refineUrl, {
            feedback,
            target_field: targetField,
            target_language: targetLanguage,
        }, label, button);
        if (!result) return null;

        document.getElementById('assistant-refine-input').value = '';
        renderDraft(result);
        markChangedFields(result.changed_fields);
        return result;
    }

    document.getElementById('assistant-refine-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = e.target.querySelector('button[type="submit"]');
        await submitRefine(document.getElementById('assistant-refine-input').value.trim(), null, null, submitBtn);
    });

    document.querySelectorAll('.assistant-quick-feedback').forEach((btn) => {
        btn.addEventListener('click', () => submitRefine(btn.dataset.feedback, null, null, btn));
    });

    // --- Apply Draft (never auto-saves) ------------------

    const overwriteDialog = document.getElementById('assistant-overwrite-dialog');
    const overwriteMessage = document.getElementById('assistant-overwrite-message');
    let pendingApply = null;

    document.getElementById('assistant-overwrite-cancel').addEventListener('click', () => overwriteDialog.close());
    document.getElementById('assistant-overwrite-confirm').addEventListener('click', () => {
        overwriteDialog.close();
        pendingApply?.();
        pendingApply = null;
    });

    // Apply/Apply All only ever change form-state — never the database
    // (the normal Save/Create submit is still what persists them, Section
    // 29) — so the confirmation is a brief on-button flash, not the
    // "Cover updated" wording used where a click really does save
    // immediately (see applyCover below).
    function flashApplied(button) {
        window.AdminFeedback?.setActionSuccess(button, 'Applied', { holdMs: 1200 });
    }

    function applyToField(field, lang, button) {
        const target = document.getElementById(field); // #description/#problem/#process/#result
        const value = currentDraft?.[lang]?.[field];
        if (!target || value === undefined) return;

        const doApply = () => {
            target.value = value;
            target.dispatchEvent(new Event('input', { bubbles: true }));
            flashApplied(button);
        };

        if (target.value.trim() !== '' && target.value.trim() !== value.trim()) {
            overwriteMessage.textContent = `This will replace the existing "${field}" text with the ${lang.toUpperCase()} draft.`;
            pendingApply = doApply;
            overwriteDialog.showModal();
        } else {
            doApply();
        }
    }

    document.querySelectorAll('.assistant-apply-field').forEach((btn) => {
        btn.addEventListener('click', () => applyToField(btn.dataset.field, btn.dataset.lang, btn));
    });

    document.getElementById('assistant-apply-all-btn').addEventListener('click', (e) => {
        const applyAllBtn = e.currentTarget;
        const fields = ['description', 'problem', 'process', 'result'];
        const hasExisting = fields.some((f) => document.getElementById(f)?.value.trim() !== '');

        const doApplyAll = () => {
            fields.forEach((f) => {
                const target = document.getElementById(f);
                if (target && currentDraft?.en?.[f] !== undefined) {
                    target.value = currentDraft.en[f];
                    target.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
            flashApplied(applyAllBtn);
        };

        if (hasExisting) {
            overwriteMessage.textContent = 'This will replace Description, Problem, Process, and Result with the English draft.';
            pendingApply = doApplyAll;
            overwriteDialog.showModal();
        } else {
            doApplyAll();
        }
    });

    // --- Reset (clears Assistant state only — never the Project form) ---

    document.getElementById('assistant-reset-btn').addEventListener('click', async () => {
        const result = await postJson(panel.dataset.resetUrl, {}, null);
        if (!result) return;

        currentDraft = null;
        clearError();
        [stepUnderstanding, stepConversation, stepDraft].forEach((el) => { el.hidden = true; });
        document.getElementById('assistant-transcript').replaceChildren();
        document.getElementById('assistant-notes').value = '';
    });

    // --- Field-level "Refine with AI" (Sections 22-24, 68-69) ---------------

    /**
     * Opens the compact inline refine box for one field (Section 23),
     * pre-filling feedback text when it came from a Quality Review issue
     * (Section 29) instead of a blank field-level trigger. Submitting
     * calls the exact same submitRefine() the panel's own Feedback form
     * uses — no second rewriting pipeline (Section 47) — then opens the
     * AI Workspace panel and scrolls to Draft Review so the field/
     * language Apply buttons (already built, not duplicated here) are
     * right there to use.
     */
    function openRefineBox(field, prefill = '') {
        document.querySelectorAll('.admin-refine-box').forEach((box) => {
            if (box.dataset.refineBox !== field) box.hidden = true;
        });

        const box = document.querySelector(`.admin-refine-box[data-refine-box="${field}"]`);
        if (!box) return;

        const textarea = box.querySelector('textarea');
        if (prefill) textarea.value = prefill;
        box.hidden = false;
        textarea.focus();
    }

    document.querySelectorAll('.admin-refine-trigger').forEach((btn) => {
        btn.addEventListener('click', () => openRefineBox(btn.dataset.refineField));
    });

    document.querySelectorAll('.admin-refine-cancel').forEach((btn) => {
        btn.addEventListener('click', () => {
            const box = document.querySelector(`.admin-refine-box[data-refine-box="${btn.dataset.refineField}"]`);
            if (box) box.hidden = true;
        });
    });

    document.querySelectorAll('.admin-refine-submit').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const field = btn.dataset.refineField;
            const box = document.querySelector(`.admin-refine-box[data-refine-box="${field}"]`);
            const feedback = box?.querySelector('textarea')?.value.trim();

            if (!feedback) return;

            if (!currentDraft) {
                showError('Generate a draft first (open AI Workspace → Understand → Analyze Project → Generate Draft), then Refine with AI will work directly from this field.');
                openPanel();
                return;
            }
            const result = await submitRefine(feedback, field, null, btn);
            if (!result) return;

            box.hidden = true;
            openPanel();
            window.setTimeout(() => stepDraft.scrollIntoView({ block: 'center' }), 50);
        });
    });

    // --- Curate: Cover ranking (edit mode only) -----------------

    const coverBtn = document.getElementById('cover-suggest-btn');
    const coverErrorSlot = document.getElementById('cover-error-slot');
    coverBtn?.addEventListener('click', async () => {
        const fd = new FormData();
        appendImageInputs(fd);

        const result = await callAssistant(panel.dataset.coverUrl, fd, 'Evaluating cover options…', coverBtn, coverErrorSlot);
        if (!result) return;

        const box = document.getElementById('cover-candidates-result');
        box.replaceChildren();

        const RANK_LABEL = { 1: '#1 Recommended', 2: '#2 Alternative', 3: '#3 Alternative' };

        (result.rankings ?? []).forEach((candidate) => {
            const row = document.createElement('div');
            row.className = 'assistant-suggestion-row';

            const thumbWrap = document.createElement('div');
            thumbWrap.className = 'assistant-suggestion-thumb-wrap';

            if (candidate.path) {
                const img = document.createElement('img');
                img.src = pathToImgSrc(candidate.path);
                img.alt = '';
                img.className = 'assistant-suggestion-thumb';
                thumbWrap.append(img);
            }

            const text = document.createElement('div');
            text.className = 'assistant-suggestion-text';
            const name = document.createElement('p');
            name.className = 'assistant-suggestion-name';
            name.textContent = RANK_LABEL[candidate.rank] ?? `#${candidate.rank}`;
            const reason = document.createElement('p');
            reason.className = 'assistant-suggestion-reason';
            reason.textContent = candidate.reason;
            text.append(name, reason);
            thumbWrap.append(text);
            row.append(thumbWrap);

            if (candidate.path && panel.dataset.applyCoverUrl) {
                const applyBtn = document.createElement('button');
                applyBtn.type = 'button';
                applyBtn.className = 'btn-text';
                applyBtn.style.fontSize = 'var(--text-meta)';
                applyBtn.style.flexShrink = '0';
                applyBtn.textContent = 'Use as Cover';
                applyBtn.addEventListener('click', async () => {
                    // Unlike Apply Draft/Confirm Tool/Apply Suggested Order,
                    // this one endpoint really does save immediately
                    // (ProjectAssistantController::applyCover calls
                    // $project->update() directly) — so the confirmation
                    // says "Cover updated", not "Applied", and this never
                    // marks the form dirty (Section 36 — copy must match
                    // actual persistence, audited against the controller).
                    const applied = await postJson(panel.dataset.applyCoverUrl, { path: candidate.path }, 'Updating cover…', applyBtn, coverErrorSlot);
                    if (applied?.ok) {
                        window.AdminFeedback?.setActionSuccess(applyBtn, 'Cover updated', { thenReset: false });
                        applyBtn.disabled = true;
                    }
                });
                row.append(applyBtn);
            } else if (!candidate.path) {
                const note = document.createElement('p');
                note.className = 'assistant-suggestion-reason';
                note.style.flexShrink = '0';
                note.textContent = 'Save first to apply.';
                row.append(note);
            }

            box.append(row);
        });

        box.hidden = false;
    });

    // --- Curate: Tool Suggestions --------------------------------

    function alreadyConfirmedToolNames() {
        const checked = Array.from(document.querySelectorAll('input[name="tools[]"]:checked')).map((el) => el.value.toLowerCase());
        const custom = (document.querySelector('input[name="tools_custom"]')?.value ?? '')
            .split(',').map((t) => t.trim().toLowerCase()).filter(Boolean);
        return new Set([...checked, ...custom]);
    }

    // Sets the checkbox/custom-tools field programmatically, then dispatches
    // a bubbling change/input event so the Project form's own unsaved-
    // changes listener picks it up the same way a manual click or keystroke
    // would (Section 29) — no separate dirty-marking path needed here.
    function confirmSuggestedTool(name) {
        const checkbox = Array.from(document.querySelectorAll('input[name="tools[]"]'))
            .find((el) => el.value.toLowerCase() === name.toLowerCase());
        if (checkbox) {
            checkbox.checked = true;
            checkbox.dispatchEvent(new Event('change', { bubbles: true }));
            return;
        }
        const custom = document.querySelector('input[name="tools_custom"]');
        if (!custom) return;
        const existing = custom.value.split(',').map((t) => t.trim()).filter(Boolean);
        if (!existing.some((t) => t.toLowerCase() === name.toLowerCase())) {
            existing.push(name);
            custom.value = existing.join(', ');
            custom.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    const toolsErrorSlot = document.getElementById('tools-error-slot');
    document.getElementById('tools-suggest-btn')?.addEventListener('click', async (e) => {
        const btn = e.currentTarget;
        const fd = factsFormData(collectFacts());
        const result = await callAssistant(panel.dataset.toolsUrl, fd, 'Finding relevant tools…', btn, toolsErrorSlot);
        if (!result) return;

        // Defensive client-side filter in addition to the prompt-level
        // instruction — a suggestion duplicating an already-confirmed
        // tool is dropped here even if the model returned one anyway.
        const confirmed = alreadyConfirmedToolNames();
        const suggestions = (result.suggested_tools ?? []).filter((s) => !confirmed.has(s.name.toLowerCase()));

        const list = document.getElementById('tools-suggestions-result');
        list.replaceChildren();

        if (!suggestions.length) {
            const li = document.createElement('li');
            li.textContent = 'No additional tools could be confidently suggested from the current context.';
            list.append(li);
            list.hidden = false;
            return;
        }

        suggestions.forEach((s) => {
            const li = document.createElement('li');
            li.className = 'assistant-suggestion-row';

            const text = document.createElement('div');
            text.className = 'assistant-suggestion-text';
            const name = document.createElement('p');
            name.className = 'assistant-suggestion-name';
            name.textContent = s.name;
            const reason = document.createElement('p');
            reason.className = 'assistant-suggestion-reason';
            reason.textContent = s.reason;
            text.append(name, reason);

            const actions = document.createElement('div');
            actions.className = 'assistant-suggestion-actions';
            const confirmBtn = document.createElement('button');
            confirmBtn.type = 'button';
            confirmBtn.className = 'btn-text';
            confirmBtn.style.fontSize = 'var(--text-meta)';
            confirmBtn.textContent = 'Confirm';
            const dismissBtn = document.createElement('button');
            dismissBtn.type = 'button';
            dismissBtn.className = 'btn-text';
            dismissBtn.style.fontSize = 'var(--text-meta)';
            dismissBtn.textContent = 'Dismiss';

            confirmBtn.addEventListener('click', () => {
                confirmSuggestedTool(s.name);
                window.AdminFeedback?.setActionSuccess(confirmBtn, 'Confirmed', { thenReset: false });
                confirmBtn.disabled = true;
                dismissBtn.disabled = true;
            });
            dismissBtn.addEventListener('click', () => {
                dismissBtn.textContent = 'Dismissed';
                dismissBtn.disabled = true;
                confirmBtn.disabled = true;
            });

            actions.append(confirmBtn, dismissBtn);
            li.append(text, actions);
            list.append(li);
        });

        list.hidden = false;
    });

    // --- Curate: Gallery sequence suggestion (edit mode only) ----

    let pendingSuggestedOrder = null;

    const galleryErrorSlot = document.getElementById('gallery-error-slot');
    document.getElementById('gallery-suggest-btn')?.addEventListener('click', async (e) => {
        const btn = e.currentTarget;
        const orderInputs = document.querySelectorAll('#admin-gallery-order-inputs input[name="gallery_order[]"]');
        const fd = new FormData();
        Array.from(orderInputs).forEach((el) => fd.append('gallery_paths[]', el.value));

        const result = await callAssistant(panel.dataset.galleryOrderUrl, fd, 'Reviewing gallery sequence…', btn, galleryErrorSlot);
        if (!result) return;

        document.getElementById('gallery-order-applied-note').hidden = true;

        const box = document.getElementById('gallery-order-compare');
        const currentEl = document.getElementById('gallery-order-current');
        const suggestedEl = document.getElementById('gallery-order-suggested');
        const reasonsEl = document.getElementById('gallery-order-reasons');

        currentEl.replaceChildren();
        (result.current_order ?? []).forEach((path) => {
            const img = document.createElement('img');
            img.src = pathToImgSrc(path);
            img.alt = '';
            currentEl.append(img);
        });

        suggestedEl.replaceChildren();
        (result.suggested_order ?? []).forEach((item) => {
            const img = document.createElement('img');
            img.src = pathToImgSrc(item.path);
            img.alt = '';
            suggestedEl.append(img);
        });

        reasonsEl.replaceChildren();
        (result.suggested_order ?? []).forEach((item, i) => {
            if (!item.reason) return;
            const li = document.createElement('li');
            li.textContent = `${i + 1}. ${item.reason}`;
            reasonsEl.append(li);
        });

        pendingSuggestedOrder = (result.suggested_order ?? []).map((item) => item.path);
        box.hidden = false;
    });

    document.getElementById('gallery-order-apply-btn')?.addEventListener('click', (e) => {
        if (!pendingSuggestedOrder) return;
        const grid = document.getElementById('admin-gallery-grid');
        if (!grid) return;

        pendingSuggestedOrder.forEach((path) => {
            const item = grid.querySelector(`.admin-gallery-item[data-path="${CSS.escape(path)}"]`);
            if (item) grid.append(item);
        });

        // gallery-reorder.js owns the actual hidden-input/badge/button sync
        // logic (and, via that resync, the form's own unsaved-changes
        // signal — Section 29) — this only reorders the DOM nodes, then
        // asks that file to resync everything else, so the logic isn't
        // duplicated here.
        grid.dispatchEvent(new CustomEvent('gallery-order-applied'));

        window.AdminFeedback?.setActionSuccess(e.currentTarget, 'Applied', { holdMs: 1200 });
        const note = document.getElementById('gallery-order-applied-note');
        if (note) note.hidden = false;
    });

    // --- Review: Content Review -----------------------------------

    const SEVERITY_LABELS = { low: 'Low', medium: 'Medium', high: 'High' };

    const reviewErrorSlot = document.getElementById('review-error-slot');
    document.getElementById('review-run-btn')?.addEventListener('click', async (e) => {
        const btn = e.currentTarget;
        const categorySelect = document.getElementById('category_id');
        const fd = factsFormData({
            title: val('title'),
            category: categorySelect?.selectedOptions?.[0]?.textContent?.trim() ?? '',
            role: val('role'),
            description: val('description'),
            problem: val('problem'),
            process: val('process'),
            result: val('result'),
            tools: Array.from(document.querySelectorAll('input[name="tools[]"]:checked')).map((el) => el.value),
        });

        const result = await callAssistant(panel.dataset.qualityReviewUrl, fd, 'Reviewing project…', btn, reviewErrorSlot);
        if (!result) return;

        const emptyHint = document.getElementById('review-content-empty');
        if (emptyHint) emptyHint.hidden = true;

        const box = document.getElementById('review-content-result');
        box.replaceChildren();

        if (result.summary) {
            const summary = document.createElement('p');
            summary.className = 'admin-field-hint';
            summary.textContent = result.summary;
            box.append(summary);
        }

        if ((result.strengths ?? []).length) {
            const strengthsLabel = document.createElement('p');
            strengthsLabel.className = 'admin-field-hint';
            strengthsLabel.style.marginTop = '0.5rem';
            strengthsLabel.textContent = 'Strengths';
            const strengthsList = document.createElement('ul');
            strengthsList.className = 'assistant-list';
            result.strengths.forEach((text) => {
                const li = document.createElement('li');
                li.textContent = text;
                strengthsList.append(li);
            });
            box.append(strengthsLabel, strengthsList);
        }

        // Most important issues first (Section 64) — high severity, then
        // medium, then low; capped at 4 shown, the rest behind "View all".
        const REFINEABLE = ['description', 'problem', 'process', 'result'];
        const severityRank = { high: 0, medium: 1, low: 2 };
        const issues = [...(result.issues ?? [])].sort((a, b) => (severityRank[a.severity] ?? 3) - (severityRank[b.severity] ?? 3));
        const VISIBLE_ISSUES = 4;

        function issueRow(issue) {
            const row = document.createElement('div');
            row.className = 'assistant-quality-row';

            const header = document.createElement('div');
            header.className = 'assistant-quality-row-header';
            const field = document.createElement('span');
            field.className = 'assistant-quality-field';
            field.textContent = issue.field;
            const severity = document.createElement('span');
            severity.className = 'assistant-severity';
            severity.dataset.severity = issue.severity;
            severity.textContent = SEVERITY_LABELS[issue.severity] ?? issue.severity;
            header.append(field, severity);

            const message = document.createElement('p');
            message.className = 'assistant-quality-message';
            message.textContent = issue.message;

            row.append(header, message);

            if (REFINEABLE.includes(issue.field)) {
                const actions = document.createElement('div');
                actions.className = 'assistant-quality-row-actions';

                const reviewBtn = document.createElement('button');
                reviewBtn.type = 'button';
                reviewBtn.className = 'btn-text';
                reviewBtn.style.fontSize = 'var(--text-meta)';
                reviewBtn.textContent = 'Review field';
                reviewBtn.addEventListener('click', () => scrollToField(issue.field));

                const refineBtn = document.createElement('button');
                refineBtn.type = 'button';
                refineBtn.className = 'btn-text';
                refineBtn.style.fontSize = 'var(--text-meta)';
                refineBtn.textContent = 'Refine with AI';
                refineBtn.addEventListener('click', () => {
                    scrollToField(issue.field);
                    window.setTimeout(() => openRefineBox(issue.field, issue.message), 350);
                });

                actions.append(reviewBtn, refineBtn);
                row.append(actions);
            }

            return row;
        }

        const issuesEl = document.createElement('div');
        issues.slice(0, VISIBLE_ISSUES).forEach((issue) => issuesEl.append(issueRow(issue)));

        if (issues.length > VISIBLE_ISSUES) {
            const moreWrap = document.createElement('div');
            const viewAllBtn = document.createElement('button');
            viewAllBtn.type = 'button';
            viewAllBtn.className = 'btn-text';
            viewAllBtn.style.fontSize = 'var(--text-meta)';
            viewAllBtn.style.marginTop = '0.5rem';
            viewAllBtn.textContent = `View all ${issues.length} suggestions`;
            viewAllBtn.addEventListener('click', () => {
                issues.slice(VISIBLE_ISSUES).forEach((issue) => issuesEl.append(issueRow(issue)));
                moreWrap.remove();
            });
            moreWrap.append(viewAllBtn);
            issuesEl.append(moreWrap);
        }

        box.append(issuesEl);

        if ((result.recommended_actions ?? []).length) {
            const actionsLabel = document.createElement('p');
            actionsLabel.className = 'admin-field-hint';
            actionsLabel.style.marginTop = '0.5rem';
            actionsLabel.textContent = 'Recommended next steps';
            const actionsList = document.createElement('ul');
            actionsList.className = 'assistant-list';
            result.recommended_actions.forEach((text) => {
                const li = document.createElement('li');
                li.textContent = text;
                actionsList.append(li);
            });
            box.append(actionsLabel, actionsList);
        }

        box.hidden = false;

        // Reactive Project Status update (Section 6) — still zero extra
        // Gemini calls, just reflecting a result already fetched for a
        // different reason.
        const statusNextValue = document.querySelector('.admin-status-next-value');
        if (statusNextValue && (result.issues ?? []).length > 0) {
            statusNextValue.textContent = 'Review content';
        }
    });

    // --- Ready: Search & Sharing (suggestion only) -----------------------

    const seoErrorSlot = document.getElementById('seo-error-slot');
    document.getElementById('seo-generate-btn')?.addEventListener('click', async (e) => {
        const btn = e.currentTarget;
        const categorySelect = document.getElementById('category_id');
        const fd = factsFormData({
            title: val('title'),
            category: categorySelect?.selectedOptions?.[0]?.textContent?.trim() ?? '',
            role: val('role'),
            description: val('description'),
            problem: val('problem'),
            process: val('process'),
            result: val('result'),
        });

        const result = await callAssistant(panel.dataset.seoUrl, fd, 'Generating search & sharing suggestion…', btn, seoErrorSlot);
        if (!result) return;

        document.getElementById('seo-preview-title').textContent = result.title ?? '';
        document.getElementById('seo-preview-description').textContent = result.meta_description ?? '';
        document.getElementById('seo-preview-social').textContent = result.social_description ?? '';
        renderList('seo-preview-notes', 'seo-preview-notes', result.notes);
        document.getElementById('seo-preview-result').hidden = false;
    });
});
