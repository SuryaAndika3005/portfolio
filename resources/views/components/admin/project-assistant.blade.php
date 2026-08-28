@props(['project' => null])

{{-- AI Workspace (V1.1, renamed/trimmed for the V1.2 UX Refinement). Only
     the conversational tasks live here now — Understand (Analyze/Reply)
     and Draft Review (Generate Draft/Refine/Apply). Tool Suggestions,
     Cover Ranking, Gallery Order, Quality Review, and SEO moved to their
     contextual homes in _form.blade.php (Section 12) — but every one of
     them still reads its endpoint URL from this same panel's data
     attributes below, so nothing here was removed from the DOM, only the
     five result/trigger sections that used to render inside it (Section
     48: relocating triggers, not duplicating endpoint logic).
     All the actual Gemini work happens server-side via
     ProjectAssistantController; this file and its companion
     project-assistant.js only render state and make same-origin fetch()
     calls with the CSRF token already in <head> (see _layout). Never
     renders AI text as raw HTML — every dynamic value is set via
     textContent/value in JS, never innerHTML (Section 81). --}}

<button type="button" id="assistant-trigger" class="btn btn-secondary btn--compact" aria-haspopup="true" aria-expanded="false" aria-controls="assistant-panel">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="4" y1="7" x2="20" y2="7"></line><circle cx="9" cy="7" r="2" fill="currentColor" stroke="none"></circle>
        <line x1="4" y1="12" x2="20" y2="12"></line><circle cx="15" cy="12" r="2" fill="currentColor" stroke="none"></circle>
        <line x1="4" y1="17" x2="20" y2="17"></line><circle cx="11" cy="17" r="2" fill="currentColor" stroke="none"></circle>
    </svg>
    AI Workspace
</button>

<div id="assistant-backdrop" class="assistant-backdrop"></div>

@php
    $assistantRoute = fn (string $name) => $project
        ? route("admin.projects.assistant.project-{$name}", $project)
        : route("admin.projects.assistant.{$name}");
@endphp
<aside id="assistant-panel" class="assistant-panel" aria-label="AI Workspace" role="region"
    data-analyze-url="{{ $assistantRoute('analyze') }}"
    data-reply-url="{{ $assistantRoute('reply') }}"
    data-draft-url="{{ $assistantRoute('draft') }}"
    data-refine-url="{{ $assistantRoute('refine') }}"
    data-reset-url="{{ $assistantRoute('reset') }}"
    data-cover-url="{{ $assistantRoute('cover') }}"
    data-apply-cover-url="{{ $project ? route('admin.projects.assistant.apply-cover', $project) : '' }}"
    data-tools-url="{{ $assistantRoute('tools') }}"
    data-quality-review-url="{{ $assistantRoute('quality-review') }}"
    data-seo-url="{{ $assistantRoute('seo') }}"
    data-gallery-order-url="{{ $project ? route('admin.projects.assistant.gallery-order', $project) : '' }}">

    <div class="assistant-panel-header">
        <p class="assistant-panel-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="4" y1="7" x2="20" y2="7"></line><circle cx="9" cy="7" r="2" fill="currentColor" stroke="none"></circle>
                <line x1="4" y1="12" x2="20" y2="12"></line><circle cx="15" cy="12" r="2" fill="currentColor" stroke="none"></circle>
                <line x1="4" y1="17" x2="20" y2="17"></line><circle cx="11" cy="17" r="2" fill="currentColor" stroke="none"></circle>
            </svg>
            AI Workspace
        </p>
        <button type="button" id="assistant-close" class="assistant-panel-close" aria-label="Close AI Workspace">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div class="assistant-panel-body" id="assistant-body">
        <div id="assistant-error-slot" role="alert"></div>

        <div id="assistant-loading" class="assistant-loading" hidden aria-live="polite">
            <span class="assistant-loading-dot" aria-hidden="true"></span>
            <span id="assistant-loading-text">Working…</span>
        </div>

        {{-- A. UNDERSTAND (empty state) — Section 8 --}}
        <div id="assistant-step-context" class="assistant-section">
            <p class="assistant-section-title">Project Understanding</p>
            <p class="admin-field-hint">Help the assistant understand the project before generating or reviewing content. Title, Category, Role, Client, Year, and Tools are read automatically from the form below.</p>
            <textarea id="assistant-notes" rows="3" class="admin-input" placeholder="Optional — anything else worth knowing (the client's actual request, a deadline, anything not obvious from the visuals)"></textarea>

            @if ($project && (filled($project->image_path) || filled($project->cover_image_path) || ! empty($project->galleryImages())))
                <p class="admin-field-hint mt-3" style="margin-top:0.875rem;">Images to analyze (auto-selected, up to 8 — uncheck any you'd rather skip)</p>
                <div class="assistant-image-picker" id="assistant-existing-images">
                    @php $shown = 0; @endphp
                    @if ($project->image_path)
                        <label><input type="checkbox" value="{{ $project->image_path }}" checked><img src="{{ asset('storage/'.$project->image_path) }}" alt="Main visual"></label>
                        @php $shown++; @endphp
                    @endif
                    @if ($project->cover_image_path)
                        <label><input type="checkbox" value="{{ $project->cover_image_path }}" @checked($shown < 8)><img src="{{ asset('storage/'.$project->cover_image_path) }}" alt="Archive cover"></label>
                        @php $shown++; @endphp
                    @endif
                    @foreach ($project->galleryImages() as $img)
                        <label><input type="checkbox" value="{{ $img }}" @checked($shown < 8)><img src="{{ asset($img) }}" alt="Gallery image"></label>
                        @php $shown++; @endphp
                    @endforeach
                </div>
            @endif
            <p class="admin-field-hint">Newly selected Main Visual / Archive Cover / Gallery files above are included automatically too — no need to save first.</p>

            <button type="button" id="assistant-analyze-btn" class="btn btn-primary btn--compact mt-3" style="margin-top:0.75rem;">Analyze Project</button>
        </div>

        {{-- B. UNDERSTAND (result) — Section 9 --}}
        <div id="assistant-step-understanding" class="assistant-section" hidden>
            <p class="assistant-section-title">What the Assistant Understands</p>
            <div id="assistant-inferences" hidden>
                <p class="admin-field-hint" style="margin-bottom:0.375rem;">Needs confirmation</p>
                <ul class="assistant-list" id="assistant-inferences-list"></ul>
            </div>
            <div id="assistant-understanding-unresolved"></div>
            <details id="assistant-understanding-known">
                <summary class="admin-field-hint" style="cursor:pointer;">Known facts</summary>
                <div id="assistant-understanding-known-rows"></div>
            </details>
            <p id="assistant-understanding-ready" class="admin-field-hint" hidden style="margin-top:0.75rem;">Project understanding is ready. Continue curating the project, or generate a draft below.</p>
        </div>

        {{-- C. Conversation (clarifying questions/replies) --}}
        <div id="assistant-step-conversation" class="assistant-section" hidden>
            <p class="assistant-section-title">Conversation</p>
            <div id="assistant-transcript" aria-live="polite"></div>
            <form id="assistant-reply-form">
                <label for="assistant-reply-input" class="sr-only">Your reply</label>
                <textarea id="assistant-reply-input" rows="2" class="admin-input" placeholder="Answer naturally, in English or Bahasa Indonesia…"></textarea>
                <button type="submit" class="btn btn-secondary btn--compact mt-2" style="margin-top:0.5rem;">Send</button>
            </form>
            <button type="button" id="assistant-generate-draft-btn" class="btn btn-primary btn--compact mt-3" style="margin-top:0.75rem;">Generate Draft</button>
        </div>

        {{-- D. WRITE: Draft Review — Section 21 --}}
        <div id="assistant-step-draft" class="assistant-section" hidden>
            <div class="flex items-center justify-between">
                <p class="assistant-section-title" style="margin-bottom:0">Draft</p>
                <button type="button" id="assistant-regenerate-btn" class="btn-text" style="font-size:var(--text-meta)">Regenerate</button>
            </div>
            <p class="admin-field-hint">Each field applies into the one matching field in the form — choose EN or ID per field, whichever you want saved.</p>

            <div id="assistant-assumptions" hidden>
                <p class="admin-field-hint">Assumptions still relied on:</p>
                <ul class="assistant-list" id="assistant-assumptions-list"></ul>
            </div>
            <div id="assistant-missing" hidden>
                <p class="admin-field-hint">Would strengthen the case study:</p>
                <ul class="assistant-list" id="assistant-missing-list"></ul>
            </div>

            <p class="assistant-section-title mt-4" style="margin-top:1.25rem;">English</p>
            @foreach (['description' => 'Description', 'problem' => 'Problem', 'process' => 'Process', 'result' => 'Result'] as $key => $label)
                <div class="assistant-draft-field" data-field="{{ $key }}" data-lang="en">
                    <div class="assistant-draft-field-label">
                        <label class="admin-field-label" style="margin-bottom:0">{{ $label }}</label>
                        <button type="button" class="btn-text assistant-apply-field" data-field="{{ $key }}" data-lang="en" style="font-size:var(--text-meta)">Apply</button>
                    </div>
                    <textarea class="admin-input assistant-draft-textarea" data-field="{{ $key }}" data-lang="en" rows="3"></textarea>
                </div>
            @endforeach

            <p class="assistant-section-title mt-4" style="margin-top:1.25rem;">Bahasa Indonesia</p>
            @foreach (['description' => 'Deskripsi', 'problem' => 'Masalah', 'process' => 'Proses', 'result' => 'Hasil'] as $key => $label)
                <div class="assistant-draft-field" data-field="{{ $key }}" data-lang="id">
                    <div class="assistant-draft-field-label">
                        <label class="admin-field-label" style="margin-bottom:0">{{ $label }}</label>
                        <button type="button" class="btn-text assistant-apply-field" data-field="{{ $key }}" data-lang="id" style="font-size:var(--text-meta)">Apply</button>
                    </div>
                    <textarea class="admin-input assistant-draft-textarea" data-field="{{ $key }}" data-lang="id" rows="3"></textarea>
                </div>
            @endforeach

            <button type="button" id="assistant-apply-all-btn" class="btn btn-primary btn--compact">Apply All (English)</button>

            <div class="assistant-quick-actions">
                <button type="button" class="admin-checkbox-tag assistant-quick-feedback" data-feedback="Make it shorter.">Make shorter</button>
                <button type="button" class="admin-checkbox-tag assistant-quick-feedback" data-feedback="Make it sound more professional.">More professional</button>
                <button type="button" class="admin-checkbox-tag assistant-quick-feedback" data-feedback="Make it sound less formal.">Less formal</button>
                <button type="button" class="admin-checkbox-tag assistant-quick-feedback" data-feedback="Focus more on the design decisions, not just tools used.">Focus on design decisions</button>
                <button type="button" class="admin-checkbox-tag assistant-quick-feedback" data-feedback="Remove any unconfirmed assumptions.">Remove assumptions</button>
            </div>

            <form id="assistant-refine-form">
                <label for="assistant-refine-input" class="admin-field-label">Feedback</label>
                <textarea id="assistant-refine-input" rows="2" class="admin-input" placeholder='e.g. "Problem-nya terlalu dilebih-lebihkan" or "English is fine, make Indonesian more natural"'></textarea>
                <div class="flex items-center gap-2 mt-2" style="margin-top:0.5rem;">
                    <select id="assistant-refine-field" class="admin-input">
                        <option value="">All fields</option>
                        <option value="description">Description only</option>
                        <option value="problem">Problem only</option>
                        <option value="process">Process only</option>
                        <option value="result">Result only</option>
                    </select>
                    <select id="assistant-refine-language" class="admin-input">
                        <option value="">Both languages</option>
                        <option value="en">English only</option>
                        <option value="id">Indonesian only</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary btn--compact mt-2" style="margin-top:0.5rem;">Refine</button>
            </form>
        </div>
    </div>

    <div class="assistant-panel-footer">
        <button type="button" id="assistant-reset-btn" class="btn-text" style="font-size:var(--text-meta)">Reset Assistant</button>
    </div>
</aside>

<dialog id="assistant-overwrite-dialog" class="admin-dialog">
    <div class="admin-dialog-body">
        <p class="admin-dialog-title">Replace existing text?</p>
        <p class="admin-dialog-message" id="assistant-overwrite-message"></p>
        <div class="admin-dialog-actions">
            <button type="button" class="btn btn-secondary btn--compact" id="assistant-overwrite-cancel">Cancel</button>
            <button type="button" class="btn btn-primary btn--compact" id="assistant-overwrite-confirm">Apply Anyway</button>
        </div>
    </div>
</dialog>
