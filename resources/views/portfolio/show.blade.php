@php
    // Computed before <x-layout> opens (attributes on the opening tag are
    // evaluated immediately, before any @php inside its slot would run —
    // same reasoning as projects.blade.php's $topbarChapters). Derived from
    // real project data, never invented copy: prefers the short
    // description, falls back to the Problem field (the next most
    // reader-facing sentence of the case study), then a plain
    // category-based sentence if neither exists. Always plain text (no
    // HTML) and capped for a sane meta-description length.
    $metaSource = $project->description ?: $project->problem;
    $projectMetaDescription = $metaSource
        ? Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($metaSource))), 160)
        : __(':category project by Surya Andika.', ['category' => $project->category->name ?? __('Portfolio')]);
    $projectOgImage = asset('storage/'.$project->coverImagePath());
@endphp
<x-layout :title="$project->title . ' | Surya Andika'" :meta-description="$projectMetaDescription"
    :og-image="$projectOgImage" og-type="article" :show-back="true">

    @php
        $categorySlug = $project->category->slug ?? '';
        $isUiux = $categorySlug === 'uiux-design';
        $isIt = $categorySlug === 'it-development';
        // A fixed aspect keeps the preview to a sane on-screen size instead of
        // dumping the full (sometimes 10,000px+ tall) source image inline; the
        // complete image is only ever fully shown (fit-to-screen + zoomable) in
        // the fullscreen modal's rotating deck.
        $showcaseAspectClass = $isUiux || $isIt ? 'aspect-[16/9]' : 'aspect-[4/5]';
        $showcaseWidthClass = $isUiux || $isIt ? 'max-w-4xl' : 'max-w-md lg:max-w-lg';
        // Up to 2 gallery shots peek out from behind the main preview on hover
        // (one to each side), teasing that there's more without a full second
        // grid.
        $peekImages = collect($project->galleryImages())->take(2);
        // Every image on the project (main + gallery), in the order the
        // fullscreen deck will present them; trigger buttons reference these
        // by index.
        $mainSlideSrc = $project->image_path ? asset('storage/' . $project->image_path) : null;
        $allSlides = collect([$mainSlideSrc])->filter()->merge(collect($project->galleryImages())->map(fn ($img) => asset($img)))->values();

        $hasCaseStudy = filled($project->problem) || filled($project->process) || filled($project->result);
        $hasPrevNext = $previousProject && $nextProject && $previousProject->id !== $nextProject->id;

        // Small editorial section numbers (see the case-study rhythm below)
        // only make sense if they actually count something real -- computed
        // as each optional section is confirmed present, not hardcoded, so
        // a project with only one of the two never shows a stray "02".
        $sectionIndex = 0;
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
                <p class="text-eyebrow font-bold uppercase tracking-[0.25em] text-primary mb-4">
                    {{ $project->category->name ?? __('Project') }}
                </p>
                <h1 class="text-[clamp(2rem,4.5vw,3.25rem)] font-extrabold leading-[1.05] tracking-tight text-ink">
                    {{ $project->title }}
                </h1>

                @if ($project->description)
                    <p class="text-body text-muted leading-relaxed mt-6">{{ $project->description }}</p>
                @endif

                @if ($project->role || $project->client || $project->year || $project->tools)
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-6 py-6 mt-8 border-t border-b border-border-light">
                        @if ($project->role)
                            <div>
                                <dt class="text-meta font-bold uppercase tracking-widest text-muted mb-1">{{ __('Role') }}</dt>
                                <dd class="text-small font-semibold text-ink">{{ $project->role }}</dd>
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

                    @if ($isUiux || $isIt)
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
        {{-- Project Details: Problem/Process/Result, rendered only when
             actually stored -- no invented case-study copy. Deliberately
             image-free and quiet: the page's two visual peaks are the
             primary cover above and the fullscreen gallery, so this section
             is pure editorial typography -- a horizontal three-beat story
             (Problem / Process / Result) at desktop width, stacked at
             everything narrower than lg (an exact 2-column split would
             leave the 3rd item orphaned on its own row, which reads worse
             than a clean single column). Restrained index, primary-accent
             label, text-body copy -- no cards, no background fills, no
             icons; thin lg-only column rules stand in for a fourth visual
             layer without adding weight. --}}
        @if ($hasCaseStudy)
            @php
                $sectionIndex++;
                $caseStudyItems = collect([
                    $project->problem ? ['label' => __('Problem'), 'body' => $project->problem] : null,
                    $project->process ? ['label' => __('Process'), 'body' => $project->process] : null,
                    $project->result ? ['label' => __('Result'), 'body' => $project->result] : null,
                ])->filter()->values();
            @endphp
            <div class="reveal pt-10 lg:pt-12 pb-16 lg:pb-20 border-b border-border-light">
                <p class="text-meta font-bold uppercase tracking-widest text-muted mb-2">
                    {{ __(':index / Project Details', ['index' => sprintf('%02d', $sectionIndex)]) }}
                </p>
                <h2 class="text-subheading font-extrabold tracking-tight text-ink mb-8 lg:mb-10">{{ __('Project Details') }}</h2>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-x-10 lg:gap-x-12 gap-y-10 lg:divide-x lg:divide-border-light">
                    @foreach ($caseStudyItems as $i => $item)
                        <div class="lg:px-10 lg:first:pl-0 lg:last:pr-0">
                            <h3 class="text-eyebrow font-bold uppercase tracking-[0.25em] mb-3">
                                <span class="text-primary/60">{{ sprintf('%02d', $i + 1) }} /</span>
                                <span class="text-primary">{{ Str::upper($item['label']) }}</span>
                            </h3>
                            <p class="text-body text-muted leading-relaxed">{{ $item['body'] }}</p>
                        </div>
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
                        <p class="text-body font-bold text-ink group-hover:text-primary transition-colors duration-[var(--motion-fast)] truncate">
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
                        <p class="text-body font-bold text-ink group-hover:text-primary transition-colors duration-[var(--motion-fast)] truncate">
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
            <button type="button" id="modalClose" class="pointer-events-auto absolute top-6 right-6 md:top-8 md:right-8 z-[60] text-white hover:text-blue-400 transition transform hover:rotate-90 duration-300" aria-label="{{ __('Close image preview') }}">
                <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <button type="button" id="modalPrev" class="hidden pointer-events-auto absolute left-3 md:left-6 top-1/2 -translate-y-1/2 z-[60] text-white p-2" aria-label="{{ __('Previous image') }}">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button type="button" id="modalNext" class="hidden pointer-events-auto absolute right-3 md:right-6 top-1/2 -translate-y-1/2 z-[60] text-white p-2" aria-label="{{ __('Next image') }}">
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
