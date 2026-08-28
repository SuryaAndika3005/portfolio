@props(['field'])

{{-- Field-level "Refine with AI" (V1.2 UX Refinement, Sections 22-24, 68).
     Sits near the field's own label/footer, not inside the textarea.
     Clicking opens a compact inline box (project-assistant.js) that
     collects feedback and calls the existing refine() endpoint with this
     field pre-selected — the user never has to open a generic panel and
     choose Description/Problem/Process/Result themselves. Requires a
     draft to already exist (same backend guard as before); if none does
     yet, the JS explains that rather than silently doing nothing. --}}
<div class="admin-refine-trigger-row">
    <button type="button" class="btn-text admin-refine-trigger" data-refine-field="{{ $field }}" style="font-size:var(--text-meta)">
        Refine with AI
    </button>
</div>
<div class="admin-refine-box" data-refine-box="{{ $field }}" hidden>
    <label class="admin-field-label" for="refine-feedback-{{ $field }}">What should be improved?</label>
    <textarea id="refine-feedback-{{ $field }}" class="admin-input" rows="2"
        placeholder='e.g. &quot;Make this more specific&quot; or &quot;Focus less on tools&quot;'></textarea>
    <div class="admin-refine-box-actions">
        <button type="button" class="btn-text admin-refine-cancel" data-refine-field="{{ $field }}">Cancel</button>
        <button type="button" class="btn btn-primary btn--compact admin-refine-submit" data-refine-field="{{ $field }}">Refine</button>
    </div>
</div>
