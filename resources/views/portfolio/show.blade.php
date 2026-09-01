@php

    $metaSource = $project->localized('description') ?: $project->localized('problem');
    $projectMetaDescription = $metaSource
        ? Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($metaSource))), 160)
        : __(':category project by Surya Andika.', ['category' => $project->category->name ?? __('Portfolio')]);
    // coverImagePath() can be null for a project with neither cover_image_path
    // nor image_path set -- falls back to the same site-level social image
    // <x-layout> itself defaults to, rather than emitting a broken
    // asset('storage/') URL with nothing after the trailing slash.
    $projectOgImage = $project->coverImagePath()
        ? asset('storage/' . $project->coverImagePath())
        : asset('storage/projects/dika.webp');
@endphp
<x-layout :title="$project->title . ' | Surya Andika'" :meta-description="$projectMetaDescription"
    :og-image="$projectOgImage" og-type="article" :show-back="true">

    @php
        // Keywords: only real, existing per-project data (tools + category),
        // never invented terms -- omitted entirely when a project has
        // neither.
        $keywordParts = collect([$project->category->name ?? null])
            ->merge($project->tools ? array_map('trim', explode(',', $project->tools)) : [])
            ->filter()
            ->unique()
            ->values();
    @endphp
    @push('json-ld')
        {{-- CreativeWork (not SoftwareApplication) is deliberately generic --
             this portfolio spans graphic design, UI/UX, web development, and
             AI projects, and pretending every one of them is installable
             software would be dishonest schema. No dateCreated: the DB only
             ever holds a bare year (e.g. "2026"), and while a reduced-
             precision "YYYY" is technically valid ISO 8601, Google's own
             structured-data guidance for CreativeWork date fields expects a
             full date/datetime -- a bare year is liable to be read as weak
             or invalid by real consumers. Omitting the field is honest;
             fabricating "2026-01-01" (a specific day nobody claimed) is not,
             so that option is off the table too. --}}
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@graph' => [
                array_filter([
                    '@type' => 'CreativeWork',
                    'name' => $project->title,
                    'description' => $projectMetaDescription,
                    'url' => route('portfolio.show', $project->id),
                    'image' => $projectOgImage,
                    'creator' => [
                        '@type' => 'Person',
                        '@id' => route('home') . '#person',
                        'name' => 'Surya Andika',
                        'url' => route('home'),
                    ],
                    'keywords' => $keywordParts->isNotEmpty() ? $keywordParts->implode(', ') : null,
                ]),
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => route('home')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => __('Projects'), 'item' => route('portfolio.projects')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $project->title, 'item' => route('portfolio.show', $project->id)],
                    ],
                ],
            ],
        ]" />
    @endpush

    @php
        $categorySlug = $project->category->slug ?? '';
        $isUiux = $categorySlug === 'uiux-design';
        // Web & Systems (slug 'it-development', kept for backward
        // compatibility -- see projects.blade.php's $chapters comment) and
        // AI & Data (slug 'ai-data') both get the same wide,
        // browser-chrome-framed treatment below: they were one category
        // ("IT & Development") until the Archive Taxonomy Restructure split
        // it in two, and the detail-page hero treatment was never about
        // which of the two a project ended up in -- it's about "this is an
        // application/interface/model-output screenshot", which both
        // halves still are.
        $isTechnical = in_array($categorySlug, ['it-development', 'ai-data'], true);
        // A fixed aspect keeps the preview to a sane on-screen size instead of
        // dumping the full (sometimes 10,000px+ tall) source image inline; the
        // complete image is only ever fully shown (fit-to-screen + zoomable) in
        // the fullscreen modal's rotating deck.
        $showcaseAspectClass = $isUiux || $isTechnical ? 'aspect-[16/9]' : 'aspect-[4/5]';
        $showcaseWidthClass = $isUiux || $isTechnical ? 'max-w-4xl' : 'max-w-md lg:max-w-lg';
        // Up to 2 gallery shots peek out from behind the main preview on hover
        // (one to each side), teasing that there's more without a full second
        // grid.
        $peekImages = collect($project->galleryImages())->take(2);
        // Every image on the project (main + gallery), in the order the
        // fullscreen deck will present them; trigger buttons reference these
        // by index.
        $mainSlideSrc = $project->image_path ? asset('storage/' . $project->image_path) : null;
        $allSlides = collect([$mainSlideSrc])->filter()->merge(collect($project->galleryImages())->map(fn ($img) => asset($img)))->values();

        $hasCaseStudy = filled($project->localized('problem')) || filled($project->localized('process')) || filled($project->localized('result'));
        $hasGallery = ! empty($project->galleryImages());
        $hasPrevNext = $previousProject && $nextProject && $previousProject->id !== $nextProject->id;
    @endphp

    {{-- Editorial case study: asymmetric intro (title/summary/metadata left,
         primary visual right, ~41/59 -- editorial, not centered), then a
         stacked rhythm below (Project Details only) built from sections the
         project actually has data for. Overview text stays in a constrained
         reading column, never stretched across the full 1600px canvas. The
         intro visual is the page's single gallery entry point -- see the
         primary-visual block below -- so there is no second, duplicate
         gallery section further down the page. --}}
    <header class="reveal max-w-[1600px] mx-auto px-8 lg:px-20 pt-16 lg:pt-20 pb-10 lg:pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start">
            <div class="lg:col-span-5">
                {{-- Category name is shared taxonomy UI copy, not project
                     content, so it goes through __() like any other static
                     string (Section 6, Global Language Catalog System) --
                     it just isn't routed through localized(), which is
                     reserved for the 5 per-project fields. Existing
                     lang/id.json keys already cover it for two of the
                     three categories in real use; a category name with no
                     matching key (e.g. "IT & Development", which the
                     archive page's own hardcoded chapter label spells
                     differently as "Web & App Development") simply
                     renders unchanged, exactly like before this pass. --}}
                <p class="text-eyebrow font-bold uppercase tracking-[0.25em] text-primary-fg mb-4">
                    {{ $project->category->name ? __($project->category->name) : __('Project') }}
                </p>
                <h1 class="text-[clamp(2rem,5vw,var(--text-display))] font-extrabold leading-[1.05] tracking-tight text-ink">
                    {{ $project->title }}
                </h1>

                @if ($project->localized('description'))
                    <p class="text-body text-muted leading-relaxed mt-6">{{ $project->localized('description') }}</p>
                @endif

                {{-- client/year/tools are never routed through localized() --
                     technology and company names stay as written regardless
                     of locale (Section 3 of the batch brief this implements). --}}
                @if ($project->localized('role') || $project->client || $project->year || $project->tools)
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-6 py-6 mt-8 border-t border-b border-border-light">
                        @if ($project->localized('role'))
                            <div>
                                <dt class="text-meta font-bold uppercase tracking-widest text-muted mb-1">{{ __('Role') }}</dt>
                                <dd class="text-small font-semibold text-ink">{{ $project->localized('role') }}</dd>
                            </div>
                        @endif
                        @if ($project->client)
                            <div>
                                <dt class="text-meta font-bold uppercase tracking-widest text-muted mb-1">{{ __('Client') }}</dt>
                                <dd class="text-small font-semibold text-ink">{{ $project->client }}</dd>
                            </div>
                        @endif
                        @if ($project->year)
                            <div>
                                <dt class="text-meta font-bold uppercase tracking-widest text-muted mb-1">{{ __('Year') }}</dt>
                                <dd class="text-small font-semibold text-ink">{{ $project->year }}</dd>
                            </div>
                        @endif
                        @if ($project->tools)
                            <div>
                                <dt class="text-meta font-bold uppercase tracking-widest text-muted mb-1">{{ __('Tools') }}</dt>
                                <dd class="text-small font-semibold text-ink">{{ $project->tools }}</dd>
                            </div>
                        @endif
                    </dl>
                @endif

                {{-- No project-link CTAs render here: the current Project
                     schema has no stored live-demo/repository/prototype
                     URL for any project (verified against the model/
                     migrations), so there is nothing real to link to.
                     Nothing is invented in its place. --}}
            </div>

            {{-- Primary visual: the project's one hero moment, filling its
                 column rather than the full page width, AND the page's
                 single gallery entry point -- clicking it opens the same
                 fullscreen Visual Deck that used to also be duplicated in a
                 lower "Visual Showcase" section (removed; see Project
                 Details below). Category-aware -- artwork-first (no chrome)
                 for Graphic Design, interface/implementation-first
                 (browser-chrome, reused unmodified) for UI/UX and Web/App.
                 Also slide 0 of the fullscreen deck. `group` here (not on
                 the button itself) so the hover response below covers the
                 whole stacked composition -- main cover and peek cards --
                 as one unit. No visible gallery-count label: the stacked
                 peek cards themselves already communicate that more images
                 exist, and the count still reaches assistive tech via the
                 button's own aria-label below (not shown visually). Hover
                 uses --motion-fast (200ms) instead of --motion-interactive
                 (350ms), so entering/leaving reads as immediate feedback
                 ("this can be opened") rather than a showcase reveal. --}}
            <div class="lg:col-span-7 group">
                <div data-peek-wrapper class="relative py-3 {{ $showcaseWidthClass }} mx-auto">
                    {{-- Peek stack: up to 2 gallery shots genuinely stacked behind the
                         main preview, one to each side -- visible at rest like a real
                         fanned card stack, so gallery depth already reads before any
                         hover happens. A restrained, fast hover response (translate/
                         rotate only, no scale/tilt/glow) reinforces that the whole
                         stack is one clickable, explorable thing. Purely decorative
                         (pointer-events-none) so they never steal the click. --}}
                    @if ($peekImages->count() >= 1)
                        <div data-peek
                            class="absolute inset-y-3 left-0 w-[78%] rounded-[var(--radius-lg)] overflow-hidden shadow-lg border-4 border-surface pointer-events-none scale-95 -rotate-6 transition-transform duration-[var(--motion-fast)] ease-[var(--ease-interactive)] group-hover:-rotate-[7deg] group-hover:-translate-x-1">
                            <img src="{{ asset($peekImages[0]) }}" class="w-full h-full object-cover object-top brightness-90"
                                alt="">
                        </div>
                    @endif
                    @if ($peekImages->count() >= 2)
                        <div data-peek
                            class="absolute inset-y-3 right-0 w-[78%] rounded-[var(--radius-lg)] overflow-hidden shadow-lg border-4 border-surface pointer-events-none scale-95 rotate-6 transition-transform duration-[var(--motion-fast)] ease-[var(--ease-interactive)] group-hover:rotate-[7deg] group-hover:translate-x-1">
                            <img src="{{ asset($peekImages[1]) }}" class="w-full h-full object-cover object-top brightness-90"
                                alt="">
                        </div>
                    @endif

                    @if ($isUiux || $isTechnical)
                        <x-browser-chrome :accent="$isUiux ? 'uiux' : 'it'"
                            :label="$isUiux ? $project->title : Str::slug($project->title) . '.app'"
                            class="relative z-10 !rounded-[var(--radius-lg)] transition-transform duration-[var(--motion-fast)] ease-[var(--ease-interactive)] group-hover:-translate-y-[2px]">
                            <button type="button" class="relative w-full text-left cursor-pointer block outline-none focus-visible:ring-2 focus-visible:ring-primary/40 {{ $showcaseAspectClass }}"
                                data-modal-trigger data-slide-index="0"
                                aria-label="{{ $allSlides->count() > 1 ? __('View :title main image fullscreen, :count visuals in gallery', ['title' => $project->title, 'count' => $allSlides->count()]) : __('View :title main image fullscreen', ['title' => $project->title]) }}">
                                <img src="{{ asset('storage/' . $project->image_path) }}" decoding="async"
                                    class="lazy-fade w-full h-full object-cover object-top" alt="{{ __(':title main visual', ['title' => $project->title]) }}">
                            </button>
                        </x-browser-chrome>
                    @else
                        <button type="button"
                            class="relative z-10 rounded-[var(--radius-lg)] overflow-hidden shadow-xl border border-border-light bg-surface cursor-pointer block w-full outline-none focus-visible:ring-2 focus-visible:ring-primary/40 transition-transform duration-[var(--motion-fast)] ease-[var(--ease-interactive)] group-hover:-translate-y-[2px] {{ $showcaseAspectClass }}"
                            data-modal-trigger data-slide-index="0"
                            aria-label="{{ $allSlides->count() > 1 ? __('View :title main image fullscreen, :count visuals in gallery', ['title' => $project->title, 'count' => $allSlides->count()]) : __('View :title main image fullscreen', ['title' => $project->title]) }}">
                            <img src="{{ asset('storage/' . $project->image_path) }}" decoding="async"
                                class="lazy-fade w-full h-full object-cover object-top" alt="{{ __(':title main visual', ['title' => $project->title]) }}">
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-[1600px] mx-auto px-8 lg:px-20">
        {{-- Case study (Project Detail Layout V2): stacked editorial
             sections instead of the old three-way equal columns. That grid
             read fine visually but didn't scale -- a long Process section
             (routinely the meatiest of the three) forced Problem/Result
             into a mismatched height, and body copy had to squeeze into a
             narrow third of the page. Each section now gets the full
             reading measure (capped at max-w-[720px], never the full
             viewport), its own 01/02/03 identity, and reads top-to-bottom
             like the rest of the page instead of side-by-side. One shared
             soft container (not three floating cards -- Section 14 of the
             brief this implements is explicit that a case study isn't a
             dashboard) with thin dividers between sections stands in for
             the old column rules. Icons are decorative only (aria-hidden)
             and never substitute for the text label. A field this project
             doesn't have (e.g. no Result yet) simply isn't in
             $caseStudyItems -- never a fabricated "02" gap. --}}
        @if ($hasCaseStudy)
            @php
                $caseStudyItems = collect([
                    $project->localized('problem') ? ['label' => __('Problem'), 'body' => $project->localized('problem'), 'icon' => 'problem'] : null,
                    $project->localized('process') ? ['label' => __('Process'), 'body' => $project->localized('process'), 'icon' => 'process'] : null,
                    $project->localized('result') ? ['label' => __('Result'), 'body' => $project->localized('result'), 'icon' => 'result'] : null,
                ])->filter()->values();
            @endphp
            <div class="reveal py-10 lg:py-14">
                <h2 class="sr-only">{{ __('Case Study') }}</h2>
                <div class="bg-surface border border-border-light rounded-[var(--radius-lg)] divide-y divide-border-light">
                    @foreach ($caseStudyItems as $i => $item)
                        <section class="p-8 sm:p-10 lg:p-12 grid grid-cols-1 lg:grid-cols-[13rem_1fr] gap-x-12 gap-y-5">
                            <div class="flex items-center gap-3 lg:flex-col lg:items-start lg:gap-4">
                                <span aria-hidden="true"
                                    class="inline-flex items-center justify-center w-10 h-10 shrink-0 rounded-full bg-primary-soft text-primary-fg">
                                    @switch($item['icon'])
                                        @case('problem')
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="13"/><line x1="12" y1="16.5" x2="12" y2="16.51"/></svg>
                                            @break
                                        @case('process')
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4v5h5"/><path d="M20 20v-5h-5"/><path d="M4.5 9a8 8 0 0 1 13.9-4.2L20 9"/><path d="M19.5 15a8 8 0 0 1-13.9 4.2L4 15"/></svg>
                                            @break
                                        @case('result')
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 17 9 11 13 15 21 7"/><polyline points="15 7 21 7 21 13"/></svg>
                                    @endswitch
                                </span>
                                <h3 class="text-eyebrow font-bold uppercase tracking-[0.25em]">
                                    <span class="text-primary-fg/60">{{ sprintf('%02d', $i + 1) }} /</span>
                                    <span class="text-primary-fg">{{ Str::upper($item['label']) }}</span>
                                </h3>
                            </div>
                            <div class="max-w-[720px]">
                                <p class="text-body text-muted leading-relaxed whitespace-pre-line">{{ $item['body'] }}</p>
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Project Gallery: a generic label on purpose (Section 19 of the
             brief) -- "System Screenshots" would read oddly on a graphic-
             design or branding project, and the content architecture has
             no per-project section-name field to draw a context-specific
             label from anyway. Reuses the SAME $allSlides/fullscreen deck
             the hero visual already opens (no second modal system, no
             duplicated gallery dataset) -- these thumbnails are just more
             entry points into it, offset by the gallery's own position in
             $allSlides (index 1+, since index 0 is the hero/main image).
             Renders nothing at all when the project has no gallery images
             (Section 18) -- no empty grid, no placeholder tiles. --}}
        @if ($hasGallery)
            <div class="reveal py-10 lg:py-14 border-t border-border-light">
                <h2 class="text-eyebrow font-bold uppercase tracking-[0.25em] text-primary-fg mb-8 lg:mb-10">
                    {{ __('Project Gallery') }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                    @foreach ($project->galleryImages() as $i => $image)
                        <button type="button"
                            class="group relative aspect-[4/3] rounded-[var(--radius-md)] overflow-hidden border border-border-light bg-canvas outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                            data-modal-trigger data-slide-index="{{ $i + 1 }}"
                            aria-label="{{ __('View gallery image :n of :title fullscreen', ['n' => $i + 1, 'title' => $project->title]) }}">
                            <img src="{{ asset($image) }}" loading="lazy" decoding="async"
                                class="w-full h-full object-cover object-top transition-transform duration-[var(--motion-fast)] ease-[var(--ease-interactive)] group-hover:scale-[1.03]"
                                alt="{{ __(':title, gallery image :n', ['title' => $project->title, 'n' => $i + 1]) }}">
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Previous/Next: scoped to the current project's own category,
             ordered the same way the archive/homepage already order
             everything (see PortfolioController@show). Only rendered when
             there are at least 3 siblings in the category -- with exactly
             2, "previous" and "next" would both resolve to the same other
             project, which would just look like a duplicate link. Small
             cover thumbnails (coverImagePath(), same fallback as
             everywhere else) give each direction a visual anchor without
             becoming a giant card or a slider. --}}
        @if ($hasPrevNext)
            <nav aria-label="{{ __('More projects in this category') }}"
                class="reveal py-12 lg:py-16 border-b border-border-light grid grid-cols-1 sm:grid-cols-2 gap-8">
                <a href="{{ route('portfolio.show', $previousProject->id) }}" class="group flex items-center gap-4">
                    <div class="w-16 h-16 shrink-0 rounded-[var(--radius-sm)] overflow-hidden bg-canvas">
                        <img src="{{ asset('storage/' . $previousProject->coverImagePath()) }}" loading="lazy" decoding="async"
                            class="w-full h-full object-cover object-top" alt="">
                    </div>
                    <div class="min-w-0">
                        <p class="text-meta font-bold uppercase tracking-widest text-muted mb-1">&larr; {{ __('Previous Project') }}</p>
                        <p class="text-body font-bold text-ink group-hover:text-primary-fg transition-colors duration-[var(--motion-fast)] truncate">
                            {{ $previousProject->title }}
                        </p>
                    </div>
                </a>
                <a href="{{ route('portfolio.show', $nextProject->id) }}" class="group flex items-center gap-4 sm:flex-row-reverse sm:text-right">
                    <div class="w-16 h-16 shrink-0 rounded-[var(--radius-sm)] overflow-hidden bg-canvas">
                        <img src="{{ asset('storage/' . $nextProject->coverImagePath()) }}" loading="lazy" decoding="async"
                            class="w-full h-full object-cover object-top" alt="">
                    </div>
                    <div class="min-w-0">
                        <p class="text-meta font-bold uppercase tracking-widest text-muted mb-1">{{ __('Next Project') }} &rarr;</p>
                        <p class="text-body font-bold text-ink group-hover:text-primary-fg transition-colors duration-[var(--motion-fast)] truncate">
                            {{ $nextProject->title }}
                        </p>
                    </div>
                </a>
            </nav>
        @endif

        {{-- Closing CTA: one universal invitation (not category-specific
             copy). Primary action goes to Contact, secondary offers a way
             back into the rest of the work -- both real, existing
             destinations, no placeholder buttons. Light-toned to match
             the rest of this page rather than repeating Homepage
             Contact's dark closing beat on every single project page.
             The plain shared footer renders immediately below via
             <x-layout> (hideFooter is not set) -- one CTA, one footer,
             nothing competing. --}}
        <section class="reveal py-20 lg:py-24 text-center">
            <h2 class="text-subheading lg:text-heading font-extrabold tracking-tight text-ink mb-4">
                {{ __('Interested in working together?') }}
            </h2>
            <p class="text-body text-muted max-w-md mx-auto mb-8">
                {{ __('Reach out to talk about a project, or see more of the work first.') }}
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('home') }}#contact" class="btn btn-primary">{{ __('Get in Touch') }}</a>
                <a href="{{ route('portfolio.projects') }}" class="btn btn-secondary">{{ __('View All Projects') }}</a>
            </div>
        </section>
    </main>

    <div id="imageModal" class="hidden fixed inset-0 z-[100] overflow-hidden" role="dialog" aria-modal="true" aria-label="{{ __('Image preview') }}">
        {{-- Dedicated backdrop layer — the ONLY element that closes the modal
             on a plain click (see image-modal.js). It sits behind everything
             else (z-0); #modalContent above it never sets pointer-events:none
             on itself, so nothing in it can accidentally "miss" and fall
             through to this layer. --}}
        <div id="modalBackdrop" class="absolute inset-0 z-0 bg-slate-950/95 backdrop-blur-sm"></div>

        {{-- Positioning wrapper only — deliberately pointer-events-none so it
             never itself intercepts a click; every actually-interactive piece
             inside (buttons, the deck) opts back in with pointer-events-auto.
             That leaves a real, always-reachable margin of backdrop around
             the deck (see #modalDeck's inset below) without needing to
             infer "is this click on empty space" anywhere. --}}
        <div id="modalContent" class="absolute inset-0 z-10 pointer-events-none">
            {{-- Touch targets (Final QA fix): each button's inset position is
                 pulled in by exactly its own added padding, so the VISIBLE
                 icon renders at the identical pixel position as before --
                 only the invisible tappable box around it grows, to >=44x44
                 CSS px at every breakpoint (modalClose: 32+16=48 base,
                 40+16=56 md+; modalPrev/Next: 20+24=44 base exact,
                 24+24=48 md+). No visible circle/background is added, so
                 nothing looks visually bulkier. --}}
            <button type="button" id="modalClose" class="pointer-events-auto absolute top-4 right-4 md:top-6 md:right-6 p-2 z-[60] text-white hover:text-blue-400 transition transform hover:rotate-90 duration-300" aria-label="{{ __('Close image preview') }}">
                <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <button type="button" id="modalPrev" class="hidden pointer-events-auto absolute left-2 md:left-5 top-1/2 -translate-y-1/2 p-3 z-[60] text-white" aria-label="{{ __('Previous image') }}">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button type="button" id="modalNext" class="hidden pointer-events-auto absolute right-2 md:right-5 top-1/2 -translate-y-1/2 p-3 z-[60] text-white" aria-label="{{ __('Next image') }}">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </button>

            {{-- Editorial rotating deck: every project image is a persistent, absolutely
                 stacked card ($allSlides — the same data the intro visual's trigger
                 indexes into, no second gallery dataset). visual-deck.js positions them by
                 offset from the active index — active centered/full-color/scale 1, others
                 grayscale/scaled-down/rotated to each side — and image-modal.js layers
                 per-active-image zoom/pan and the modal's open/close/trigger/keyboard
                 behavior on top. See image-modal.js for how the two are wired together.
                 Inset (not full-bleed) on purpose: the margin outside this box is never
                 covered by it, so #modalBackdrop underneath stays genuinely reachable —
                 clicking there closes the modal; clicking anywhere in this box (a card or
                 not) never does. Cards keep their normal on-screen size/position (they're
                 sized in vw/vh, not relative to this box), so the inset doesn't change the
                 deck's appearance. --}}
            <div id="modalDeck" class="pointer-events-auto absolute inset-6 sm:inset-10 md:inset-14 lg:inset-20">
                <x-visual-deck :slides="$allSlides" :title="$project->title" />
            </div>

            <span id="modalZoomHint" class="hidden md:block pointer-events-none absolute bottom-6 right-6 md:right-8 z-[60] text-white/50 text-[11px] font-semibold uppercase tracking-wider">{{ __('Scroll or tap to zoom') }}</span>

            <span id="modalCounter" class="hidden pointer-events-none absolute bottom-6 left-1/2 -translate-x-1/2 z-[60] text-white/50 text-[11px] font-semibold tracking-[0.15em] tabular-nums"></span>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/image-modal.js')
    @endpush

</x-layout>
