{{-- Title/description reuse the hero's own existing copy verbatim (the
     subheading line for the title, the intro paragraph for the
     description) rather than inventing new positioning language -- see
     the SEO pass report for why. Routed through __() (: prefix, not a
     literal string attribute) so the browser tab title and meta
     description actually switch locale too -- found missing entirely
     during the Language Content Completion pass's EN/ID sweep. --}}
<x-layout :hide-footer="true"
    :title="__('Surya Andika — Graphic Designer & Informatics Student')"
    :meta-description="__('I work across visual design, UI/UX, web development, and applied AI/ML, combining creative thinking with a growing technical foundation.')">

    @push('json-ld')
        {{-- Person + WebSite: the only two entities this portfolio has real
             data for. sameAs is limited to the LinkedIn URL already linked
             in the footer -- no invented social profiles. @id gives this
             Person a stable identity a structured-data consumer can match
             against the same @id used for CreativeWork.creator on every
             project detail page (show.blade.php), rather than each page
             minting an ambiguous, un-linked Person stub of its own. --}}
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Person',
                    '@id' => route('home') . '#person',
                    'name' => 'Surya Andika',
                    'url' => route('home'),
                    'jobTitle' => 'Graphic Designer & Informatics Student',
                    'sameAs' => ['https://linkedin.com/in/surya-andika'],
                ],
                [
                    '@type' => 'WebSite',
                    'name' => 'Surya Andika',
                    'url' => route('home'),
                ],
            ],
        ]" />
    @endpush

    <header id="top" class="relative max-w-[1600px] mx-auto px-8 lg:px-20 pt-32 lg:pt-40 pb-24 lg:pb-32">
        <div
            class="grid grid-cols-1 min-[960px]:grid-cols-12 items-center gap-12 md:gap-14 min-[960px]:gap-10 lg:gap-16">
            <div class="min-[960px]:col-span-6 lg:col-span-7">
                <p style="--reveal-delay: 0ms"
                    class="reveal text-eyebrow font-bold uppercase tracking-[0.25em] text-slate-500 dark:text-slate-400 mb-4">
                    {{ __(':name · :location', ['name' => 'Surya Andika', 'location' => __('Padang, Indonesia')]) }}
                </p>

                <h1 style="--reveal-delay: 80ms"
                    class="reveal text-[clamp(3.5rem,6vw,6.5rem)] font-extrabold leading-[1.05] tracking-tight text-slate-900 dark:text-white mb-6">
                    {!! __('Designing visuals.<br>Building :digital experiences.', ['digital' => '<span class="text-primary-fg">'.__('digital').'</span>']) !!}
                </h1>

                <p style="--reveal-delay: 160ms" class="reveal text-subheading font-bold text-slate-700 dark:text-slate-200 mb-5">
                    {{ __('Graphic Designer & Informatics Student') }}
                </p>

                <p style="--reveal-delay: 220ms" class="reveal text-body text-slate-500 dark:text-slate-400 max-w-xl mb-10">
                    {{ __('I work across visual design, UI/UX, web development, and applied AI/ML, combining creative thinking with a growing technical foundation.') }}
                </p>

                <div style="--reveal-delay: 300ms" class="reveal flex flex-wrap items-center gap-x-8 gap-y-4">
                    <a href="#projects" class="group btn btn-primary">
                        {{ __('Explore Work') }}
                        <svg class="w-4 h-4 group-hover:translate-x-1 motion-reduce:transform-none transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                    <a href="{{ asset('storage/projects/CV.pdf') }}" target="_blank" rel="noopener"
                        class="group btn-text dark:text-white dark:hover:!text-primary-fg">
                        {{ __('View Resume') }}
                        <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 motion-reduce:transform-none"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M7 17L17 7M17 7H8M17 7v9"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <div style="--reveal-delay: 420ms" class="reveal reveal-portrait min-[960px]:col-span-6 lg:col-span-5">
                <div class="relative max-w-sm mx-auto min-[960px]:mx-0 min-[960px]:ml-auto lg:max-w-[420px]">
                    <div class="absolute -inset-6 bg-primary-soft/70 rounded-[2.75rem] -z-10" aria-hidden="true"></div>
                    <div
                        class="rounded-[var(--radius-lg)] overflow-hidden border border-border-light shadow-2xl shadow-slate-200/50 dark:shadow-black/40 aspect-[4/5]">
                        <img src="{{ asset('storage/projects/dika.webp') }}" alt="{{ __('Portrait of Surya Andika') }}"
                            class="w-full h-full object-cover" decoding="async" fetchpriority="high">
                    </div>
                    <p class="mt-4 flex items-center gap-3 text-meta font-bold uppercase tracking-[0.25em] text-slate-400 dark:text-slate-500">
                        <span class="w-8 h-px bg-primary" aria-hidden="true"></span>
                        {{ __('01 / Portfolio') }}
                    </p>
                </div>
            </div>
        </div>
    </header>

    {{-- Featured Projects (Editorial Layout V2 + Media Art Direction): the
         first real per-project list on the homepage -- Selected Works
         below is category-grouped, not an ordered highlight reel. Source
         is PortfolioController::buildFeaturedProjects() (is_highlighted +
         featured_order, falling back to latest published projects when
         nothing is highlighted yet).

         GRID (unchanged from Editorial Layout V2): hierarchy is assigned
         by POSITION in that ordered list (slot 1 = hero, slot 2 =
         secondary, slots 3+ = supporting tier), never by project identity.
         This only makes sense as a 5-item, 2-row (2 + 3) composition; if
         the Featured count ever changes from 5, this section's hierarchy
         needs re-art-direction, the same way any real editorial layout
         would (a generic N-column bento grid was deliberately rejected in
         favor of this).

         MEDIA CANVAS (unchanged from the Media Art Direction pass): every
         card still gets a CONTROLLED OUTER CANVAS with a fixed aspect
         ratio driven by its ROW, not by the source image's own dimensions
         -- row 1 (hero + secondary) uses a wide "hero" canvas; row 2's
         three supporting cards all share one canvas ratio, so their outer
         heights -- and caption baselines -- stay identical regardless of
         what's inside. That part of the earlier pass already solved the
         row-2 baseline problem and is untouched here.

         MEDIA FILL (this pass, Full-Bleed Cover pass): the previous pass
         then placed the real asset INSIDE that canvas with object-contain
         (+ inset padding, + a chrome bar for 'browser') -- correct for
         baseline alignment, but it left Money Tracker/Yasmin visually
         small inside a lot of empty mat space, and the browser frame's
         chrome bar ate into the image area. This pass keeps the exact
         same canvas, drops the padding/chrome entirely, and switches
         every content type to object-cover so the real asset fills 100%
         of its canvas -- a Featured card is a teaser, not the complete
         work; the uncropped source is still exactly one click away on
         Project Detail/Gallery, untouched by this pass.

         A CONTENT-TYPE variant (from category + the cover's own actual
         aspect ratio, read once via getimagesize(), never from project
         ID) decides only the crop's object-position -- a generic rule per
         type, not a per-project tuned value:
           - 'browser': a landscape web/app screenshot -- object-top, so
             the crop keeps page identity/KPIs/primary chart (near the top
             of any dashboard) and drops whatever falls below the fold.
           - 'phone': a portrait-shaped UI/UX cover -- object-position
             biased toward the upper third (not pure top), since a mobile
             screen's hero metric sits at the very top but a bit more
             context (the next section down) reads better than a hard
             top-crop; this is a generic "portrait interface" rule, not
             Money-Tracker-specific, even though Money Tracker is
             currently the only project it applies to.
           - 'artwork': Graphic Design -- object-top, since promotional
             artwork conventionally puts the headline/hero visual at the
             top and secondary contact/footer details at the bottom.
         Any future Featured project slots into one of these three by its
         own real category + image data, automatically. --}}
    @if ($featuredProjects->isNotEmpty())
        <section id="featured" aria-labelledby="featured-heading" class="max-w-[1600px] mx-auto px-8 lg:px-20 pt-16 lg:pt-20">
            <div class="mb-10 lg:mb-14">
                <p style="--reveal-delay: 0ms"
                    class="reveal text-eyebrow font-bold uppercase tracking-[0.25em] text-primary-fg mb-4">
                    {{ __('Featured / 02') }}
                </p>
                <h2 id="featured-heading" style="--reveal-delay: 60ms"
                    class="reveal text-heading font-extrabold tracking-tight text-slate-900 dark:text-white">
                    {{ __('A few projects worth a closer look.') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-x-8 gap-y-12 lg:gap-x-8 lg:gap-y-16">
                @foreach ($featuredProjects as $i => $project)
                    @php
                        $slug = $project->category->slug ?? 'other';
                        $isHero = $loop->first;
                        $isSecondary = $loop->iteration === 2;

                        $coverPath = $project->coverImagePath();
                        $dimensions = $coverPath ? @getimagesize(public_path('storage/' . $coverPath)) : false;
                        $isPortraitCover = $dimensions && $dimensions[1] > $dimensions[0];

                        $treatment = match (true) {
                            $slug === 'graphic-design' => 'artwork',
                            $slug === 'uiux-design' && $isPortraitCover => 'phone',
                            default => 'browser',
                        };

                        // Outer canvas is a ROW decision (position),
                        // independent of $treatment (a content-type
                        // decision) -- row 1 gets a wide hero canvas, row
                        // 2's three cards converge on ONE shared canvas at
                        // md+ so their heights (and caption baselines)
                        // match exactly no matter what content type ends
                        // up inside each. Below md (mobile, single column,
                        // no sibling row to align with) portrait content
                        // -- phone/artwork -- gets a taller canvas instead
                        // of the same wide desktop one, so it isn't
                        // needlessly shrunk on the one breakpoint where
                        // extra height costs nothing (Section 13: "media
                        // may receive a taller canvas [on mobile] where
                        // portrait sources benefit"). The landscape
                        // ('browser') treatment never benefits from that,
                        // so it keeps one ratio at every breakpoint.
                        $canvasClass = match (true) {
                            $isHero || $isSecondary => 'aspect-[13/6]',
                            $treatment === 'browser' => 'aspect-[4/3]',
                            default => 'aspect-[4/5] md:aspect-[4/3]',
                        };
                        // Numeric twin of $canvasClass's ratio, needed
                        // below to reason about which way object-cover
                        // will actually crop -- not tied to row/slot, so
                        // it stays correct if the canvas ratios above are
                        // ever retuned.
                        $canvasAspectRatio = ($isHero || $isSecondary) ? 13 / 6 : 4 / 3;

                        // Full-bleed crop position, per content-type
                        // variant (a generic rule, not a per-project
                        // value). 'browser' is the interesting case:
                        // object-cover crops WIDTH (left/right) whenever
                        // the canvas is proportionally narrower than the
                        // source screenshot, which happens whenever a
                        // landscape app cover (~1.6 native aspect here)
                        // sits in a squarer canvas than that -- the
                        // supporting-row canvas (4/3 = 1.33) is exactly
                        // this case, the hero-row canvas (13/6 = 2.17) is
                        // not. Centered horizontal cropping then cuts
                        // evenly off both edges, which for a left-nav'd
                        // dashboard means losing real UI (sidebar/page
                        // heading) on the side that matters. Left-aligning
                        // the crop instead keeps that content and crops
                        // the less-important right edge -- decided purely
                        // from the two real aspect ratios being compared,
                        // never from which project or row this is, so any
                        // future landscape-app cover gets the geometrically
                        // correct choice automatically. When the canvas is
                        // instead wider than the source (the hero-row
                        // case), cropping is vertical, so top-alignment
                        // (unchanged) is what applies.
                        $imageAspectRatio = $dimensions ? $dimensions[0] / $dimensions[1] : null;
                        $objectPositionClass = match (true) {
                            $treatment === 'phone' => 'object-[center_20%]',
                            $treatment === 'browser' && $imageAspectRatio && $canvasAspectRatio < $imageAspectRatio => 'object-left-top',
                            default => 'object-top',
                        };

                        // Slot-based span (position, not identity) -- see
                        // the section comment above. At tablet (md,
                        // 2-column grid) slots 1-4 simply pair off two per
                        // row in order (no explicit span needed, a bare
                        // grid item already takes 1 of 2 columns); only
                        // the last item spans the full tablet row instead
                        // of pairing oddly with whoever preceded it, so a
                        // 5-item set never leaves an orphaned half-empty
                        // row at any breakpoint.
                        $spanClass = match (true) {
                            $isHero => 'lg:col-span-7',
                            $isSecondary => 'lg:col-span-5',
                            $loop->last => 'md:col-span-2 lg:col-span-4',
                            default => 'lg:col-span-4',
                        };

                        $titleSizeClass = $isHero ? 'text-subheading' : 'text-small';
                    @endphp
                    <a href="{{ route('portfolio.show', $project->id) }}" style="--reveal-delay: {{ $loop->index * 60 }}ms"
                        class="reveal group block {{ $spanClass }} outline-none focus-visible:ring-2 focus-visible:ring-primary/40 rounded-[var(--radius-md)]">
                        {{-- Full-bleed cover (Featured Cover Full-Bleed
                             pass): every content type now shares the exact
                             same structure -- a bordered/rounded canvas
                             with the real asset filling it via
                             object-cover, no chrome bar, no inset padding,
                             no inner mat box. Only $objectPositionClass
                             (a generic per-type rule, computed above)
                             differs between them. This is a teaser crop,
                             not the complete work -- the full uncropped
                             asset is unchanged and still one click away on
                             Project Detail / the gallery / the fullscreen
                             viewer. --}}
                        <div class="relative {{ $canvasClass }} rounded-[var(--radius-md)] border border-border-light bg-canvas overflow-hidden">
                            <img src="{{ asset('storage/' . $project->coverImagePath()) }}" loading="lazy" decoding="async"
                                class="lazy-fade w-full h-full object-cover {{ $objectPositionClass }} transform group-hover:scale-[1.015] group-focus-visible:scale-[1.015] motion-reduce:scale-100 transition-transform duration-[var(--motion-interactive)] ease-[var(--ease-interactive)]"
                                alt="{{ __(':title preview', ['title' => $project->title]) }}">
                        </div>

                        <div class="mt-4 lg:mt-5">
                            <p class="text-meta font-bold uppercase tracking-widest text-muted">
                                <span class="text-primary-fg tabular-nums">{{ sprintf('%02d', $i + 1) }}</span>
                                <span class="mx-1 text-soft-muted">/</span>{{ __($project->category->name ?? '') }}
                            </p>
                            <h3 class="{{ $titleSizeClass }} font-bold text-ink mt-1.5 inline-flex items-center gap-1.5">
                                {{ $project->title }}
                                <svg class="w-4 h-4 shrink-0 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 motion-reduce:transition-none transition-[opacity,transform] duration-200" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </h3>
                            @if ($isHero && $project->localized('role'))
                                <p class="text-small text-muted mt-1">{{ $project->localized('role') }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- About: editorial narrative + metadata, single-statement-led, not a
         second "narrow label / wide content" split (Hero and Selected
         Works already use variations of that shape). Statement runs wide
         (max-w-[1000px]); narrative and metadata sit side by side beneath
         it in a flex row (stacked on mobile) rather than a boxed <dl> --
         Location/Study only, no re-quoted current-employment fact (that
         lives in Experience, not here). --}}
    <section id="about" aria-labelledby="about-heading" class="max-w-[1600px] mx-auto px-8 lg:px-20 py-16 lg:py-20">
        <p style="--reveal-delay: 0ms"
            class="reveal text-eyebrow font-bold uppercase tracking-[0.25em] text-primary-fg mb-6">
            {{ __('About / 03') }}
        </p>

        <h2 id="about-heading" style="--reveal-delay: 60ms"
            class="reveal text-heading font-extrabold tracking-tight text-slate-900 dark:text-white max-w-[1000px] mb-8">
            {{ __('Design came first. Technology expanded the way I create.') }}
        </h2>

        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
            <p style="--reveal-delay: 120ms" class="reveal text-body text-slate-500 dark:text-slate-400 leading-relaxed max-w-[720px] flex-1">
                {{ __('I began in graphic design, learning to think in terms of clarity, composition, and how ideas read visually. Studying Informatics later pushed that instinct into more technical territory, but the way I approach a new problem still starts the same way it always did.') }}
            </p>

            <div class="hidden lg:block w-px bg-border-light shrink-0" style="height: 140px;" aria-hidden="true"></div>

            <div style="--reveal-delay: 180ms" class="reveal flex flex-row lg:flex-col gap-8 lg:gap-6 shrink-0">
                <div>
                    <p class="text-meta font-bold uppercase tracking-widest text-muted mb-1">{{ __('Location') }}</p>
                    <p class="text-small font-semibold text-slate-900 dark:text-white">{{ __('Padang, Indonesia') }}</p>
                </div>
                <div>
                    <p class="text-meta font-bold uppercase tracking-widest text-muted mb-1">{{ __('Study') }}</p>
                    <p class="text-small font-semibold text-slate-900 dark:text-white">{{ __('Informatics') }},<br class="lg:hidden">
                        {{ __('Andalas University') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Selected Works: the signature three-panel hover accordion. Header
         compressed to eyebrow + one-line heading (no supporting paragraph
         -- the accordion demonstrates the work directly). Interaction
         itself (hover-expand, instant vertical collapsed-title rotation,
         image auto-cycle) lives entirely in project-accordion.js + the
         .accordion-* rules in app.css -- untouched here. --}}
    <section id="projects" aria-labelledby="works-heading" class="max-w-[1600px] mx-auto px-8 lg:px-20 py-16 lg:py-20">
        <div class="mb-8 lg:mb-10">
            <p style="--reveal-delay: 0ms"
                class="reveal text-eyebrow font-bold uppercase tracking-[0.25em] text-primary-fg mb-4">
                {{ __('Selected Works / 04') }}
            </p>
            <h2 id="works-heading" style="--reveal-delay: 60ms"
                class="reveal text-heading font-extrabold tracking-tight text-slate-900 dark:text-white">
                {{ __('A selection of work across design and technology.') }}
            </h2>
        </div>

        @php
            // label is sourced from the actual Category model (by slug),
            // same as the archive page's chapters -- not a third
            // independently hardcoded copy of the category name (Global
            // Language Catalog System, category-consistency fix). tagline
            // and badge stay hand-authored, presentational-only fields.
            $categoryNames = $categories->keyBy('slug');
            $accordionPanels = [
                ['slug' => 'graphic-design', 'label' => __($categoryNames->get('graphic-design')->name ?? 'Graphic Design'), 'tagline' => __('Visual identities, campaigns, and communication.'), 'badge' => 'bg-primary/90'],
                ['slug' => 'uiux-design', 'label' => __($categoryNames->get('uiux-design')->name ?? 'UI/UX Design'), 'tagline' => __('Interfaces, flows, and product experiences.'), 'badge' => 'bg-violet-600/90'],
                ['slug' => 'it-development', 'label' => __($categoryNames->get('it-development')->name ?? 'IT & Development'), 'tagline' => __('Digital products from interface to implementation.'), 'badge' => 'bg-emerald-600/90'],
            ];
            // ->toBase() strips the Eloquent Collection wrapper, whose
            // get()/except() are overridden for primary-key lookups and
            // would misbehave on the string-slug keys groupBy() produces
            // here.
            $accordionGrouped = $projects->groupBy(fn ($project) => $project->category->slug ?? 'other')->toBase();
        @endphp

        @if ($accordionGrouped->isEmpty())
            <p class="text-body text-slate-400 dark:text-slate-500 py-16 text-center">{{ __('No projects yet.') }}</p>
        @else
        <div id="works-accordion" style="--reveal-delay: 220ms" class="reveal flex flex-col lg:flex-row gap-4 lg:h-[600px]">
            @foreach ($accordionPanels as $panel)
                @php
                    // Capped at 4 slides: keeping every image in a category
                    // stacked and painting simultaneously (up to 7 for
                    // Graphic Design) was part of what made hovering this
                    // row feel heavy. coverImagePath() prefers a project's
                    // dedicated accordion cover when one is set, falling
                    // back to its main image_path otherwise.
                    $items = $accordionGrouped->get($panel['slug'], collect());
                    if ($items->isEmpty()) continue;
                    $slides = $items->take(4);
                @endphp
                <div data-accordion-panel tabindex="0"
                    class="accordion-panel group/panel relative min-h-[280px] lg:min-h-0 rounded-[var(--radius-lg)] overflow-hidden bg-dark cursor-pointer outline-none focus-visible:ring-2 focus-visible:ring-primary/60">
                    @foreach ($slides as $i => $project)
                        <img data-slide src="{{ asset('storage/' . $project->coverImagePath()) }}" loading="lazy" decoding="async"
                            class="absolute inset-0 w-full h-full object-cover object-top {{ $i === 0 ? 'is-active' : '' }}"
                            alt="{{ $project->title }}">
                    @endforeach

                    <div class="absolute inset-0 bg-gradient-to-t from-dark/90 via-dark/10 to-transparent pointer-events-none"></div>

                    <a href="{{ route('portfolio.projects') }}#{{ $panel['slug'] }}"
                        class="absolute inset-0 z-10" aria-label="{{ __('View :category projects', ['category' => $panel['label']]) }}"></a>

                    <div class="absolute inset-0 z-10 flex flex-col justify-end p-6 lg:p-8 pointer-events-none">
                        <span class="accordion-count inline-flex items-center w-fit {{ $panel['badge'] }} text-white text-meta font-bold uppercase tracking-widest px-3 py-1 rounded-[var(--radius-sm)] mb-3">
                            {{ trans_choice('messages.works_count', $items->count(), ['count' => $items->count()]) }}
                        </span>
                        <h3 class="accordion-title font-extrabold text-white">{{ $panel['label'] }}</h3>
                        <p class="accordion-tagline text-small text-white/70 mt-2 max-w-xs">{{ $panel['tagline'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <div class="text-center mt-10 lg:mt-12">
            <a href="{{ route('portfolio.projects') }}" class="group btn-text dark:text-white dark:hover:!text-primary-fg">
                {{ __('Explore all works') }}
                <svg class="w-4 h-4 group-hover:translate-x-1 motion-reduce:transform-none transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </section>

    {{-- Skills: full-width interactive capability bands on a dark section
         -- no background word (removed; competed with Selected Works for
         "the page's one big visual moment"). Each row's hover/focus is one
         coordinated system: category icon shifts to primary + translates,
         primary text brightens, secondary text gains contrast, tool tiles
         lift in visibility, a thin leading rule appears, and the row's own
         background makes a barely-there tone shift -- all via .skill-group
         in app.css, not per-property inline logic here. --}}
    <section id="skills" aria-labelledby="skills-heading" class="bg-section-skills py-16 lg:py-20">
        <div class="max-w-[1600px] mx-auto px-8 lg:px-20">
            <div class="mb-10 lg:mb-14">
                <p style="--reveal-delay: 0ms"
                    class="reveal text-eyebrow font-bold uppercase tracking-[0.25em] text-primary-fg mb-4">
                    {{ __('Skills / 05') }}
                </p>
                <h2 id="skills-heading" style="--reveal-delay: 60ms"
                    class="reveal text-heading font-extrabold tracking-tight text-white">
                    {{ __('The tools behind the work.') }}
                </h2>
            </div>

            <div class="flex flex-col gap-3 lg:gap-4">
                @foreach ($skillGroups as $i => $group)
                    <div style="--reveal-delay: {{ 120 + $i * 60 }}ms" tabindex="0"
                        class="skill-group reveal group/skill grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-8 items-center px-4 lg:px-6 py-6 lg:py-7 outline-none">
                        <div class="lg:col-span-3 flex items-center gap-4">
                            <span aria-hidden="true"
                                class="w-10 h-10 shrink-0 flex items-center justify-center rounded-[var(--radius-sm)] text-white/70 group-hover/skill:text-primary-fg group-focus-visible/skill:text-primary-fg transition-[color,transform] duration-[var(--motion-fast)] ease-[var(--ease-interactive)] group-hover/skill:translate-x-1 group-focus-visible/skill:translate-x-1">
                                {!! $group['icon'] !!}
                            </span>
                            <span class="text-eyebrow font-bold uppercase tracking-[0.2em] text-white/70 group-hover/skill:text-slate-500 group-focus-visible/skill:text-slate-500 transition-colors duration-[var(--motion-fast)] ease-[var(--ease-interactive)]">{{ __($group['label']) }}</span>
                        </div>

                        <div class="lg:col-span-6">
                            @foreach ($group['primary'] as $line)
                                <p class="text-subheading font-bold text-white group-hover/skill:text-slate-900 group-focus-visible/skill:text-slate-900 transition-colors duration-[var(--motion-fast)] ease-[var(--ease-interactive)]">
                                    {{ __($line) }}
                                </p>
                            @endforeach
                            @foreach ($group['secondary'] as $line)
                                <p class="text-small text-white/60 group-hover/skill:text-slate-500 group-focus-visible/skill:text-slate-500 transition-colors duration-[var(--motion-fast)] ease-[var(--ease-interactive)] mt-1">
                                    {{ __($line) }}
                                </p>
                            @endforeach
                        </div>

                        <div class="lg:col-span-3 flex flex-wrap gap-2 lg:justify-end">
                            @foreach ($group['tools'] as $tool)
                                <span title="{{ $tool['name'] }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-[var(--radius-sm)] bg-white/10 group-hover/skill:bg-primary-soft group-focus-visible/skill:bg-primary-soft transition-colors duration-[var(--motion-fast)] ease-[var(--ease-interactive)]">
                                    <span aria-hidden="true" style="--tool-icon: url('{{ $tool['icon'] }}')"
                                        class="skill-tool-icon w-5 h-5 opacity-60 group-hover/skill:opacity-100 group-focus-visible/skill:opacity-100 group-hover/skill:!bg-primary group-focus-visible/skill:!bg-primary transition-[opacity,background-color] duration-[var(--motion-fast)]"></span>
                                    <span class="sr-only">{{ $tool['name'] }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Editorial Career Archive: three distinct visual grammars instead of
         one shared timeline system. Professional Work uses an oversized
         year as its own visual anchor (no dots, no vertical stem line) with
         role/company/duration set beside it. Education is a solid
         primary-blue insert -- deliberately different material/weight so a
         single entry doesn't imitate a multi-entry timeline. Leadership &
         Organizations runs full-width beneath as a numbered two-column
         index. Every anchor year/GPA value is a presentation-only
         extraction from the existing stored duration/description strings,
         never new data. --}}
    <section id="experience" aria-labelledby="experience-heading" class="max-w-[1600px] lg:px-20 mx-auto px-8 py-20 lg:py-24">
        <div class="mb-12 lg:mb-16">
            <p style="--reveal-delay: 0ms"
                class="reveal text-eyebrow font-bold uppercase tracking-[0.25em] text-primary-fg mb-4">
                {{ __('Experience / 06') }}
            </p>
            <h2 id="experience-heading" style="--reveal-delay: 60ms"
                class="reveal text-heading font-extrabold tracking-tight text-slate-900 dark:text-white">
                {{ __('A record of work and study.') }}
            </h2>
        </div>

        @php
            // Professional Work: chronological, current-first (see
            // Experience::sortChronologically) -- independent of whatever
            // order $experiences itself arrived in. Leadership deliberately
            // keeps that original order untouched (the previously approved
            // public presentation order), not re-sorted by date.
            $professionalEntries = \App\Models\Experience::sortChronologically($experiences->where('category', 'Professional Work')->values());
            $leadershipEntries = $experiences->reject(fn ($item) => $item->category === 'Professional Work')->values();
            $educationEntries = [
                (object) [
                    'role' => __('Bachelor of Informatics'),
                    'company' => __('Andalas University'),
                    'duration' => '2023 - Present',
                    'description' => 'Current GPA 3.57',
                ],
            ];
            $formatDuration = fn (string $duration) => str_replace([' - ', 'Present'], [' · ', __('Present')], $duration);
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-x-16 gap-y-14 mb-14 lg:mb-16">
            <div class="lg:col-span-7">
                {{-- Professional Work: the year is the row's own visual
                     anchor (72-104px at desktop) rather than a small marker
                     beside a timeline dot. Descriptions (when present)
                     expand in place via experience-toggle.js; entries with
                     no stored description render no toggle at all. --}}
                <h3 style="--reveal-delay: 120ms"
                    class="reveal text-eyebrow font-bold uppercase tracking-[0.25em] text-slate-400 dark:text-slate-500 mb-2">
                    {{ __('Professional Work') }}
                </h3>
                @foreach ($professionalEntries as $item)
                    @php
                        $hasDescription = filled($item->description);
                        $isCurrent = str_contains($item->duration, 'Present');
                        preg_match('/\d{4}/', $item->duration, $yearMatch);
                        $anchorYear = $yearMatch[0] ?? $item->duration;
                    @endphp
                    <div style="--reveal-delay: {{ 160 + $loop->index * 60 }}ms"
                        class="reveal group/row border-t border-slate-200 dark:border-white/10 hover:border-slate-300 dark:hover:border-white/20 transition-colors duration-300 {{ $loop->last ? 'border-b' : '' }}">
                        <button type="button" @if ($hasDescription) data-experience-toggle
                            aria-controls="professional-detail-{{ $item->id }}" aria-expanded="false" @endif
                            class="w-full text-left grid grid-cols-[auto_1fr] gap-x-5 lg:gap-x-8 items-start py-8 lg:py-10 {{ $hasDescription ? 'cursor-pointer' : 'cursor-default' }}">
                            {{-- text-muted, not text-soft-muted -- this year label is real
                                 content (not a decorative watermark like the archive page's
                                 aria-hidden "01"/"02"/"03" numerals, which legitimately keep
                                 text-soft-muted at 15% opacity). text-soft-muted at full
                                 opacity measures ~2.45:1 against this section's background,
                                 under WCAG AA even at this large size (3:1); text-muted clears
                                 it comfortably. --}}
                            <span class="text-[3rem] sm:text-[3.75rem] lg:text-[4.5rem] xl:text-[6.5rem] leading-[0.85] font-extrabold tabular-nums transition-colors duration-300 {{ $isCurrent ? 'text-primary-fg' : 'text-muted group-hover/row:text-primary-fg' }}">
                                {{ $anchorYear }}
                            </span>
                            <div class="pt-2 lg:pt-4 group-hover/row:translate-x-[3px] motion-reduce:transform-none transition-transform duration-300">
                                <div class="flex items-start justify-between gap-4">
                                    <h4 class="text-subheading font-bold text-slate-900 dark:text-white leading-snug">{{ $item->role }}</h4>
                                    @if ($hasDescription)
                                        <span data-toggle-icon
                                            class="text-lg leading-none text-slate-400 dark:text-slate-500 group-hover/row:text-slate-600 dark:group-hover/row:text-slate-300 transition-colors duration-300 shrink-0 mt-1">+</span>
                                    @endif
                                </div>
                                <p class="text-small text-slate-500 dark:text-slate-400 mt-1">{{ $item->company }}</p>
                                <p class="text-meta text-slate-400 dark:text-slate-500 mt-1.5">{{ $formatDuration($item->duration) }}</p>
                            </div>
                        </button>
                        @if ($hasDescription)
                            <div id="professional-detail-{{ $item->id }}" class="experience-detail">
                                <p class="text-small text-slate-500 dark:text-slate-400 leading-relaxed max-w-xl pb-8">{{ $item->description }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="lg:col-span-5 lg:self-start">
                {{-- Education: a solid primary-blue insert, not a pale card
                     imitating the Professional list. GPA is parsed out of
                     the stored description text (a presentation-only
                     extraction, not new data) and rendered only when the
                     parse actually finds a value -- no invented "/4.00"
                     scale is appended, since the stored source never states
                     one. --}}
                <h3 style="--reveal-delay: {{ 220 + $professionalEntries->count() * 60 }}ms"
                    class="reveal text-eyebrow font-bold uppercase tracking-[0.25em] text-slate-400 dark:text-slate-500 mb-2">
                    {{ __('Education') }}
                </h3>
                @foreach ($educationEntries as $item)
                    @php
                        preg_match('/\d{4}/', $item->duration, $eduYearMatch);
                        $eduAnchorYear = $eduYearMatch[0] ?? $item->duration;
                        preg_match('/GPA\s*([\d.]+)/i', (string) $item->description, $eduGpaMatch);
                        $eduGpa = $eduGpaMatch[1] ?? null;
                    @endphp
                    <div style="--reveal-delay: {{ 260 + $professionalEntries->count() * 60 + $loop->index * 60 }}ms"
                        class="reveal bg-primary rounded-[var(--radius-md)] p-8 lg:p-10">
                        <div class="flex items-start justify-between gap-3 mb-6">
                            <p class="text-[2.5rem] leading-none font-bold text-white tabular-nums">
                                {{ $eduAnchorYear }}
                            </p>
                            <svg aria-hidden="true" class="w-6 h-6 text-white/70 shrink-0" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M22 10 12 5 2 10l10 5 10-5Z" />
                                <path d="M6 12v5c0 1.5 2.5 3 6 3s6-1.5 6-3v-5" />
                            </svg>
                        </div>
                        <h4 class="text-subheading font-bold text-white leading-snug">{{ $item->role }}</h4>
                        <p class="text-small font-semibold text-white/80 mt-1">{{ $item->company }}</p>
                        <p class="text-meta text-white/60 mt-1.5">{{ $formatDuration($item->duration) }}</p>

                        @if ($eduGpa)
                            <div class="mt-6 pt-5 border-t border-white/20">
                                <p class="text-meta font-bold uppercase tracking-widest text-white/60 mb-1">
                                    {{ __('GPA') }}
                                </p>
                                <p class="text-body font-bold text-white">{{ $eduGpa }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Leadership & Organizations: full section width, read
             column-first rather than row-first -- the collection is split
             into two chunks (left gets the first ceil(n/2) entries, right
             gets the rest), each rendered as its own stacked column, so the
             numbering reads 01-04 down the left column then 05-07 down the
             right, not interleaved row by row. Numbers are computed here,
             purely for display -- never stored. --}}
        <div>
            <h3 style="--reveal-delay: {{ 320 + $professionalEntries->count() * 60 + count($educationEntries) * 60 }}ms"
                class="reveal text-eyebrow font-bold uppercase tracking-[0.25em] text-slate-400 dark:text-slate-500 mb-6">
                {{ __('Leadership & Organizations') }}
            </h3>

            @php
                $leadershipNumbered = $leadershipEntries->values()->map(fn ($item, $i) => (object) [
                    'item' => $item,
                    'number' => $i + 1,
                ]);
                $leadershipHalf = (int) ceil($leadershipNumbered->count() / 2);
                $leadershipColumns = [
                    $leadershipNumbered->slice(0, $leadershipHalf)->values(),
                    $leadershipNumbered->slice($leadershipHalf)->values(),
                ];
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12">
                @foreach ($leadershipColumns as $column)
                    <div>
                        @foreach ($column as $entry)
                            @php $item = $entry->item; @endphp
                            <div style="--reveal-delay: {{ 380 + $professionalEntries->count() * 60 + count($educationEntries) * 60 + ($entry->number - 1) * 40 }}ms"
                                class="reveal group/org grid grid-cols-12 gap-3 items-baseline py-3.5 lg:py-4 border-t border-slate-200 dark:border-white/10 {{ $loop->last ? 'border-b' : '' }} hover:border-slate-300 dark:hover:border-white/20 transition-colors duration-300">
                                <span class="col-span-1 text-meta font-bold text-slate-300 dark:text-slate-600 tabular-nums">{{ sprintf('%02d', $entry->number) }}</span>
                                <span class="col-span-2 sm:col-span-4 lg:col-span-4 text-meta text-slate-400 dark:text-slate-500 group-hover/org:text-primary-fg transition-colors duration-300 tabular-nums">{{ $formatDuration($item->duration) }}</span>
                                <div class="col-span-9 sm:col-span-7 lg:col-span-7 group-hover/org:translate-x-[3px] motion-reduce:transform-none transition-transform duration-300">
                                    <p class="text-small font-bold text-slate-900 dark:text-white leading-snug truncate">{{ $item->role }}</p>
                                    <p class="text-meta text-slate-400 dark:text-slate-500 truncate">{{ $item->company }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Contact: closing beat, direct-contact-first. Email is the sole
         primary action; WhatsApp + LinkedIn are secondary, real, tappable
         channels one tier below; the form is a quieter alternative, not
         hidden. The footer (copyright, social row, location) is fully
         integrated here -- this is the homepage's one and only closing
         region, no second <x-footer> beneath it (hideFooter on <x-layout>). --}}
    <section id="contact" aria-labelledby="contact-heading" class="bg-section-contact py-20 lg:py-24">
        <div class="max-w-[1600px] mx-auto px-8 lg:px-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
                <div class="lg:col-span-7">
                    <p style="--reveal-delay: 0ms"
                        class="reveal text-eyebrow font-bold uppercase tracking-[0.25em] text-primary-fg mb-6">
                        {{ __('Contact / 07') }}
                    </p>
                    <h2 id="contact-heading" style="--reveal-delay: 60ms"
                        class="reveal text-display font-extrabold tracking-tight text-white leading-[1.05] mb-8 max-w-3xl">
                        {{ __("Let's build something worth showing.") }}
                    </h2>

                    <div style="--reveal-delay: 120ms" class="reveal mb-8">
                        <p class="text-meta font-bold uppercase tracking-widest text-dark-muted mb-2">{{ __('Email') }}</p>
                        <a href="mailto:{{ config('portfolio.contact_email') }}" class="btn btn-primary btn-primary--inverted">
                            {{ config('portfolio.contact_email') }}
                        </a>
                    </div>

                    <div style="--reveal-delay: 180ms" class="reveal flex flex-wrap gap-4">
                        @if (config('portfolio.whatsapp_number'))
                            <a href="https://wa.me/{{ config('portfolio.whatsapp_number') }}" target="_blank" rel="noopener"
                                class="btn btn-secondary btn-secondary--on-dark">
                                WhatsApp &middot; {{ config('portfolio.whatsapp_display') }}
                            </a>
                        @endif
                        <a href="https://linkedin.com/in/surya-andika" target="_blank" rel="noopener"
                            class="btn btn-secondary btn-secondary--on-dark">
                            LinkedIn
                        </a>
                    </div>
                </div>

                <div style="--reveal-delay: 220ms" class="reveal lg:col-span-5">
                    <p class="text-eyebrow font-bold uppercase tracking-[0.2em] text-dark-muted mb-4">{{ __('Or send a message') }}</p>
                    @if (session('success'))
                        <p class="text-small text-emerald-400 mb-4">{{ session('success') }}</p>
                    @endif
                    @if (session('error'))
                        <p class="text-small text-red-400 mb-4">{{ session('error') }}</p>
                    @endif
                    <form id="contact-form" method="POST" action="{{ route('contact.send') }}" class="space-y-4" novalidate>
                        @csrf
                        <div>
                            <label for="contact-name" class="sr-only">{{ __('Name') }}</label>
                            <input id="contact-name" type="text" name="name" placeholder="{{ __('Name') }}" value="{{ old('name') }}"
                                class="w-full rounded-[var(--radius-md)] bg-white/[0.04] border border-white/15 px-4 py-3 text-small text-white placeholder:text-dark-muted focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/30 transition-colors duration-[var(--motion-fast)]">
                            @error('name') <p class="text-meta text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="contact-email" class="sr-only">{{ __('Email') }}</label>
                            <input id="contact-email" type="email" name="email" placeholder="{{ __('Email') }}" value="{{ old('email') }}"
                                class="w-full rounded-[var(--radius-md)] bg-white/[0.04] border border-white/15 px-4 py-3 text-small text-white placeholder:text-dark-muted focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/30 transition-colors duration-[var(--motion-fast)]">
                            @error('email') <p class="text-meta text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="contact-message" class="sr-only">{{ __('Message') }}</label>
                            <textarea id="contact-message" name="message" rows="4" placeholder="{{ __('Message') }}"
                                class="w-full rounded-[var(--radius-md)] bg-white/[0.04] border border-white/15 px-4 py-3 text-small text-white placeholder:text-dark-muted focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/30 transition-colors duration-[var(--motion-fast)]">{{ old('message') }}</textarea>
                            @error('message') <p class="text-meta text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button id="contact-submit" type="submit" class="btn btn-secondary btn-secondary--on-dark w-full justify-center">
                            {{ __('Send Message') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Quiet closing strip -- restored to a compact bar per the "revert
         Footer" correction (the earlier editorial three-zone version with
         the oversized watermark and full nav cluster was reverted; see
         CONTACT_FOOTER_REFINEMENT_REPORT.md for that superseded design).
         bg-dark-surface is untouched by the Skills/Contact color-hierarchy
         patch, so this keeps its exact prior dark-mode tone. Bottom
         padding stays a little larger than the top so the fixed
         Preferences FAB has real clearance below this row once the page
         is scrolled fully down. --}}
    <footer class="bg-dark-surface border-t border-border-dark">
        <div class="max-w-[1600px] mx-auto px-8 lg:px-20 pt-8 lg:pt-10 pb-14 lg:pb-16">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row items-center gap-1 sm:gap-3 text-center sm:text-left">
                    <a href="{{ route('home') }}" class="text-base font-black tracking-tighter text-white">
                        SURYA<span class="text-primary">ANDIKA</span>
                    </a>
                    <span class="hidden sm:inline text-dark-muted" aria-hidden="true">&middot;</span>
                    <p class="text-meta text-dark-muted">&copy; {{ date('Y') }} {{ __('Surya Andika. All rights reserved.') }}</p>
                </div>

                <nav aria-label="{{ __('Footer') }}" class="flex items-center gap-6">
                    <a href="https://linkedin.com/in/surya-andika" target="_blank" rel="noopener" class="text-small font-semibold text-dark-muted hover:text-white transition-colors duration-[var(--motion-fast)]">LinkedIn</a>
                    <a href="https://instagram.com" target="_blank" rel="noopener" class="text-small font-semibold text-dark-muted hover:text-white transition-colors duration-[var(--motion-fast)]">Instagram</a>
                    <a href="#top" class="text-small font-semibold text-dark-muted hover:text-white transition-colors duration-[var(--motion-fast)]">{{ __('Back to top') }} &uarr;</a>
                </nav>
            </div>
        </div>
    </footer>

    @push('scripts')
        @vite(['resources/js/project-accordion.js', 'resources/js/experience-toggle.js', 'resources/js/home-nav-scrollspy.js'])
    @endpush

</x-layout>
