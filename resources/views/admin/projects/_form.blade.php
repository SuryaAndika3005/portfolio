@csrf

@php
    $allTools = config('portfolio.tool_options');
    $selectedTools = collect(explode(',', old('tools', $project->tools ?? '')))->map(fn ($t) => trim($t))->filter();
    // "Other tools" custom-text fallback: previously only ever read from
    // old('tools_custom'), which is empty on a normal (non-validation-
    // error) page load -- so opening Edit on any project whose tools
    // include something outside the fixed checkbox list, then saving
    // without manually retyping those into this field, silently dropped
    // them from the stored tools string. Falls back to whichever of the
    // project's actual tools aren't one of the fixed checkboxes, so the
    // field now reflects what's really saved.
    $customToolsFallback = $selectedTools->diff($allTools)->implode(', ');
    $roleOptions = ['Graphic Designer', 'UI/UX Designer', 'Web & App Developer', 'Mobile Developer', 'Video Editor', 'Photographer', 'Model'];
    $clientOptions = ['Personal / Commercial', 'Akademik', 'Sports & Event', '523 Studio', 'Alir Pictures'];
    $currentYear = (int) date('Y');
    $hasCustomCover = ! empty($project) && filled($project->cover_image_path);
    $hasCoverSourceImages = ! empty($project) && (filled($project->image_path) || ! empty($project->galleryImages()));
@endphp

{{-- Optional workflow nav (V1.2 UX Refinement, Sections 34-36, 70) --
     orientation/anchor navigation, not a wizard: every link just scrolls
     to a section that's already freely editable, nothing is locked behind
     completing a previous step. "Understand" opens the AI Workspace panel
     instead of scrolling, since Analyze/Reply live inside it, not in the
     normal page flow. Edit-page only -- Review/Ready don't exist yet on
     Create (Section 44). Typography-only active/hover state, no pills,
     no stepper circles (Section 36). --}}
@if (! empty($project))
    <nav class="admin-workflow-nav" aria-label="Project workflow">
        <button type="button" id="workflow-nav-understand" class="admin-workflow-nav-item">Understand</button>
        <a href="#workspace-curate" class="admin-workflow-nav-item" data-scroll-anchor="workspace-curate">Curate</a>
        <a href="#workspace-write" class="admin-workflow-nav-item" data-scroll-anchor="workspace-write">Write</a>
        <a href="#workspace-review" class="admin-workflow-nav-item" data-scroll-anchor="workspace-review">Review</a>
        <a href="#workspace-ready" class="admin-workflow-nav-item" data-scroll-anchor="workspace-ready">Ready</a>
    </nav>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    {{-- Main content: ~66% desktop. --}}
    <div class="lg:col-span-8 space-y-6">

        <x-admin.form-section title="Basic Information">
            <div class="space-y-5">
                <x-admin.field label="Title" for="title" name="title">
                    <input id="title" type="text" name="title" value="{{ old('title', $project->title ?? '') }}" required
                           class="admin-input {{ $errors->has('title') ? 'has-error' : '' }}">
                </x-admin.field>

                <x-admin.field label="Category" for="category_id" name="category_id">
                    <select id="category_id" name="category_id" required class="admin-input {{ $errors->has('category_id') ? 'has-error' : '' }}">
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $project->category_id ?? '') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </x-admin.field>

                <x-admin.field label="Short Description" for="description" name="description"
                    hint="One or two sentences. Shown on the project grid card, and as a fallback if the case study fields below are left empty.">
                    <textarea id="description" name="description" rows="3" class="admin-input">{{ old('description', $project->description ?? '') }}</textarea>
                </x-admin.field>
                <x-admin.refine-trigger field="description" />
            </div>
        </x-admin.form-section>

        <x-admin.form-section title="Project Details">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <x-admin.field label="Role" for="role">
                    <input id="role" type="text" name="role" list="role-options" value="{{ old('role', $project->role ?? '') }}"
                           placeholder="Pick or type your own" class="admin-input">
                    <datalist id="role-options">
                        @foreach ($roleOptions as $option) <option value="{{ $option }}"> @endforeach
                    </datalist>
                </x-admin.field>

                <x-admin.field label="Client" for="client">
                    <input id="client" type="text" name="client" list="client-options" value="{{ old('client', $project->client ?? '') }}"
                           placeholder="Pick or type your own" class="admin-input">
                    <datalist id="client-options">
                        @foreach ($clientOptions as $option) <option value="{{ $option }}"> @endforeach
                    </datalist>
                </x-admin.field>

                <x-admin.field label="Year" for="year">
                    <select id="year" name="year" class="admin-input">
                        <option value="">Select year</option>
                        @for ($y = $currentYear + 1; $y >= $currentYear - 8; $y--)
                            <option value="{{ $y }}" @selected(old('year', $project->year ?? '') == $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </x-admin.field>
            </div>

            <x-admin.field label="Tools">
                <div class="flex flex-wrap gap-2 border border-border-light rounded-[var(--radius-sm)] p-3">
                    @foreach ($allTools as $tool)
                        <label class="admin-checkbox-tag">
                            <input type="checkbox" name="tools[]" value="{{ $tool }}" class="rounded border-border-light" @checked($selectedTools->contains($tool))>
                            {{ $tool }}
                        </label>
                    @endforeach
                </div>
                <input type="text" name="tools_custom" placeholder="Other tools not listed (comma separated)" value="{{ old('tools_custom', $customToolsFallback) }}"
                       class="admin-input mt-2">

                {{-- Curate: Tool Suggestions, moved from the old Assistant
                     toolbox to sit directly under the field it affects
                     (Section 13). Works on Create too -- suggestTools()
                     only needs form context, no saved Project. --}}
                <div class="admin-contextual-trigger">
                    <button type="button" id="tools-suggest-btn" class="btn-text">Suggest tools</button>
                </div>
                <p id="tools-error-slot" class="assistant-error" role="alert" hidden></p>
                <ul id="tools-suggestions-result" class="assistant-list" aria-live="polite" hidden></ul>
            </x-admin.field>
        </x-admin.form-section>

        <div id="workspace-write">
            <x-admin.form-section title="Case Study">
                <p class="admin-field-hint -mt-2 mb-4">Fill these in to show recruiters and clients how you think, not just what it looks like. Leave blank to skip; the detail page falls back to the short description above.</p>
                <div class="space-y-5">
                    <div>
                        <x-admin.field label="Problem" for="problem" hint="What challenge or goal was this project solving for the client/brand?">
                            <textarea id="problem" name="problem" rows="3" class="admin-input">{{ old('problem', $project->problem ?? '') }}</textarea>
                        </x-admin.field>
                        <x-admin.refine-trigger field="problem" />
                    </div>
                    <div>
                        <x-admin.field label="Process" for="process" hint="How did you approach it? Key decisions, iterations, tools used along the way.">
                            <textarea id="process" name="process" rows="3" class="admin-input">{{ old('process', $project->process ?? '') }}</textarea>
                        </x-admin.field>
                        <x-admin.refine-trigger field="process" />
                    </div>
                    <div>
                        <x-admin.field label="Result" for="result" hint="What was the outcome? Delivered assets, client feedback, measurable impact if any.">
                            <textarea id="result" name="result" rows="3" class="admin-input">{{ old('result', $project->result ?? '') }}</textarea>
                        </x-admin.field>
                        <x-admin.refine-trigger field="result" />
                    </div>
                </div>
            </x-admin.form-section>
        </div>

    </div>

    {{-- Secondary: media + status + review + sharing, ~33% desktop. --}}
    <div class="lg:col-span-4 space-y-6">

        @if (! empty($project))
            <x-admin.form-section title="Project Status">
                <x-admin.project-status :project="$project" />
            </x-admin.form-section>
        @endif

        {{-- is_published gates every public listing surface AND the
             project's own detail page (404 when false — see
             PortfolioController@show). Defaults to checked for a new
             project ($project is null) so the common case (publish
             immediately) needs no extra click; an existing project shows
             its real stored value. Same absent-checkbox-must-still-save
             reasoning as Featured below applies here too (see
             ProjectRequest::prepareForValidation). --}}
        <x-admin.form-section title="Visibility">
            <label class="admin-checkbox-tag flex items-center gap-2">
                <input type="checkbox" name="is_published" value="1" class="rounded border-border-light"
                       @checked(old('is_published', $project->is_published ?? true))>
                Published (visible on the public site)
            </label>
        </x-admin.form-section>

        {{-- is_highlighted/featured_order now drive the homepage's Featured
             Projects section (Curation Pass 01) -- exposed here so editing
             a project never silently un-features it (the form only ever
             submits what's rendered; an absent checkbox would reset
             is_highlighted to false on save). --}}
        <x-admin.form-section title="Featured">
            <div class="space-y-4">
                <label class="admin-checkbox-tag flex items-center gap-2">
                    <input type="checkbox" name="is_highlighted" value="1" class="rounded border-border-light"
                           @checked(old('is_highlighted', $project->is_highlighted ?? false))>
                    Show in homepage Featured Projects
                </label>

                <x-admin.field label="Featured Order" for="featured_order"
                    hint="Lower numbers appear first. Only matters while Featured is checked.">
                    <input id="featured_order" type="number" name="featured_order" min="1" step="1"
                           value="{{ old('featured_order', $project->featured_order ?? '') }}"
                           class="admin-input {{ $errors->has('featured_order') ? 'has-error' : '' }}">
                </x-admin.field>
            </div>
        </x-admin.form-section>

        <div id="workspace-curate">
            <x-admin.form-section title="Media">
                <div class="space-y-6">
                    <x-admin.field label="Main Project Visual" name="image"
                        hint="The hero image on the project detail page, and the fallback used everywhere a cover image is needed.">
                        @if (! empty($project) && $project->image_path)
                            <img src="{{ asset('storage/' . $project->image_path) }}" class="w-full aspect-video object-cover rounded-[var(--radius-sm)] mb-2 border border-border-light">
                        @endif
                        <input type="file" name="image" accept="image/*" class="admin-input">
                        @if (! empty($project))
                            <p class="admin-field-hint mt-1 mb-0">Leave empty to keep the current image.</p>
                        @endif
                    </x-admin.field>

                    <div class="border-t border-border-light pt-5">
                        <x-admin.field label="Archive Cover" name="cover_image"
                            hint="Optional override used only for the homepage/archive preview. Leave empty to keep using the Main Project Visual above.">
                            @if ($hasCustomCover)
                                <img src="{{ asset('storage/' . $project->cover_image_path) }}" class="w-full aspect-video object-cover rounded-[var(--radius-sm)] mb-2 border border-border-light">
                            @elseif (! empty($project) && $project->image_path)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $project->image_path) }}" class="w-full aspect-video object-cover rounded-[var(--radius-sm)] border border-border-light opacity-70">
                                    <p class="admin-field-hint mt-1 mb-0">Using main project image (no override set).</p>
                                </div>
                            @endif
                            <input type="file" name="cover_image" accept="image/*" class="admin-input">
                            @if ($hasCustomCover)
                                <label class="flex items-center gap-2 text-small text-muted mt-2">
                                    <input type="checkbox" name="remove_cover_image" value="1" class="rounded border-border-light">
                                    Remove override and fall back to the main image
                                </label>
                            @endif

                            {{-- Curate: Cover ranking ("Find best cover"),
                                 moved from the old Assistant toolbox to sit
                                 directly under Archive Cover (Sections
                                 15-16). Edit-only, same as before -- ranking
                                 fresh unsaved files has nowhere to Apply to
                                 yet (Section 26 preserves that limitation
                                 rather than working around it). --}}
                            @if (! empty($project))
                                <div class="admin-contextual-trigger">
                                    <button type="button" id="cover-suggest-btn" class="btn-text" @disabled(! $hasCoverSourceImages)>Find best cover</button>
                                </div>
                                @unless ($hasCoverSourceImages)
                                    <p class="admin-field-hint">Save project images first to get cover recommendations.</p>
                                @endunless
                                <p id="cover-error-slot" class="assistant-error" role="alert" hidden></p>
                                <div id="cover-candidates-result" aria-live="polite" hidden></div>
                            @endif
                        </x-admin.field>
                    </div>

                    <div class="border-t border-border-light pt-5">
                        <x-admin.field label="Gallery" name="gallery.*"
                            hint="Extra images shown in the Visual Showcase on the detail page, in the order shown below. Select multiple files at once to upload them together — new uploads are added to the end.">
                            @if (! empty($project) && ! empty($project->galleryImages()))
                                <div id="admin-gallery-grid" class="admin-gallery-grid mb-3">
                                    @foreach ($project->galleryImages() as $image)
                                        <div class="admin-gallery-item" draggable="true" data-path="{{ $image }}">
                                            <img src="{{ asset($image) }}" alt="">
                                            <span class="admin-gallery-order" aria-hidden="true">{{ $loop->iteration }}</span>
                                            <span class="admin-gallery-drag-handle" aria-hidden="true" title="Drag to reorder">&#8942;&#8942;</span>
                                            <div class="admin-gallery-controls">
                                                <button type="button" class="admin-gallery-move" data-direction="up" aria-label="Move image up">&#8593;</button>
                                                <button type="button" class="admin-gallery-move" data-direction="down" aria-label="Move image down">&#8595;</button>
                                                <label class="admin-gallery-remove-toggle" aria-label="Remove image">
                                                    <input type="checkbox" name="remove_gallery[]" value="{{ $image }}">
                                                    <span aria-hidden="true">&times;</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="admin-gallery-order-inputs">
                                    @foreach ($project->galleryImages() as $image)
                                        <input type="hidden" name="gallery_order[]" value="{{ $image }}">
                                    @endforeach
                                </div>
                                <p id="admin-gallery-live-region" class="sr-only" role="status" aria-live="polite"></p>
                                <p class="admin-field-hint">Drag a thumbnail, or use the arrow buttons, to reorder. Check an image to remove it when you save.</p>

                                {{-- Curate: Gallery sequence suggestion,
                                     moved from the old Assistant toolbox to
                                     sit directly under the gallery it
                                     affects (Sections 17-19). Manual
                                     reorder above stays exactly where it
                                     was -- only the AI suggestion trigger
                                     relocated. --}}
                                <div class="admin-contextual-trigger">
                                    <button type="button" id="gallery-suggest-btn" class="btn-text">Suggest sequence</button>
                                </div>
                                <p id="gallery-error-slot" class="assistant-error" role="alert" hidden></p>
                                <div id="gallery-order-compare" aria-live="polite" hidden>
                                    <div class="assistant-order-columns">
                                        <div>
                                            <p class="admin-field-hint" style="margin-bottom:0.375rem;">Current order</p>
                                            <div id="gallery-order-current" class="assistant-order-thumbs"></div>
                                        </div>
                                        <div>
                                            <p class="admin-field-hint" style="margin-bottom:0.375rem;">Suggested order</p>
                                            <div id="gallery-order-suggested" class="assistant-order-thumbs"></div>
                                        </div>
                                    </div>
                                    <ul id="gallery-order-reasons" class="assistant-list"></ul>
                                    <button type="button" id="gallery-order-apply-btn" class="btn btn-secondary btn--compact">Apply Suggested Order</button>
                                    <p id="gallery-order-applied-note" class="admin-applied-note" hidden>Suggested sequence applied. Save changes to keep this order.</p>
                                </div>
                            @endif
                            <input type="file" name="gallery[]" accept="image/*" multiple class="admin-input">
                        </x-admin.field>
                    </div>
                </div>
            </x-admin.form-section>
        </div>

        @if (! empty($project))
            <div id="workspace-review">
                <x-admin.form-section title="Project Review">
                    <div class="space-y-5">
                        <div>
                            <p class="admin-field-label mb-2">Structure</p>
                            <x-admin.project-completeness :project="$project" />
                        </div>
                        <div class="border-t border-border-light pt-5">
                            <p class="admin-field-label mb-1">Content</p>
                            <p id="review-content-empty" class="admin-field-hint mb-2">Checks clarity and factual grounding in the current case-study text. Not reviewed yet.</p>
                            <button type="button" id="review-run-btn" class="btn-text">Review project</button>
                            <p id="review-error-slot" class="assistant-error" role="alert" hidden></p>
                            <div id="review-content-result" aria-live="polite" hidden></div>
                        </div>
                    </div>
                </x-admin.form-section>
            </div>

            <div id="workspace-ready">
                <x-admin.form-section title="Search & Sharing">
                    <p class="admin-field-hint -mt-2 mb-3">Preview how this project may appear when shared or found in search.</p>
                    <button type="button" id="seo-generate-btn" class="btn-text">Generate suggestion</button>
                    <p id="seo-error-slot" class="assistant-error" role="alert" hidden></p>
                    <div id="seo-preview-result" aria-live="polite" hidden>
                        <p class="assistant-seo-label">Search title <span class="assistant-seo-badge">Suggestion only</span></p>
                        <p id="seo-preview-title" class="assistant-seo-preview-title"></p>
                        <p class="assistant-seo-label">Meta description</p>
                        <p id="seo-preview-description" class="assistant-seo-preview-description"></p>
                        <p class="assistant-seo-label">Social description</p>
                        <p id="seo-preview-social" class="assistant-seo-preview-description"></p>
                        <ul id="seo-preview-notes" class="assistant-list" hidden></ul>
                        <p class="admin-field-hint mt-3">This is not saved to the project yet — copy manually if useful.</p>
                    </div>
                </x-admin.form-section>
            </div>
        @endif

    </div>
</div>

{{-- No "Highlight Settings" control here: is_highlighted/featured_order are
     real, persisted columns, but no public view reads either one (verified
     against index.blade.php/projects.blade.php/show.blade.php — see the
     Batch 8B report's CMS capability matrix). A control with no visible
     public effect isn't a real capability, so it's deferred rather than
     shown; the columns and their values are untouched. --}}

<div class="mt-8 flex items-center gap-3 flex-wrap">
    <button type="submit" class="btn btn-primary">
        {{ isset($project) ? 'Save Changes' : 'Create Project' }}
    </button>
    <a href="{{ route('admin.projects.index') }}" class="btn-text">Cancel</a>
    <span id="admin-unsaved-indicator" class="admin-unsaved-indicator" role="status" hidden>Unsaved changes</span>
</div>
