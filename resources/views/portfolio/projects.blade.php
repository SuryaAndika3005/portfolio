@php

    $grouped = $projects->groupBy(fn($project) => $project->category->slug ?? 'other')->toBase();

    $categoryNames = $categories->keyBy('slug');
    // 'it-development' is the historical slug, kept as-is (not renamed to
    // e.g. 'web-systems') after the Archive Taxonomy Restructure split it
    // away from the new 'ai-data' category -- show.blade.php's
    // $isUiux/$isTechnical image treatment and this page's own grouping
    // both key off the slug directly, so changing it would be a silent
    // breaking change for zero user-facing benefit (the display NAME is
    // what visitors see, and that's already "Web & Systems"). See
    // CategoryController's class docblock for the same reasoning applied
    // to why Admin can't rename a slug at all.
    $chapters = collect([
        ['slug' => 'graphic-design', 'label' => __($categoryNames->get('graphic-design')->name ?? 'Graphic Design'), 'shortLabel' => __('Design')],
        ['slug' => 'uiux-design', 'label' => __($categoryNames->get('uiux-design')->name ?? 'UI/UX Design'), 'shortLabel' => __('UI/UX')],
        ['slug' => 'it-development', 'label' => __($categoryNames->get('it-development')->name ?? 'Web & Systems'), 'shortLabel' => __('Web & Systems')],
        ['slug' => 'ai-data', 'label' => __($categoryNames->get('ai-data')->name ?? 'AI & Data'), 'shortLabel' => __('AI & Data')],
    ])->filter(fn($c) => $grouped->get($c['slug'], collect())->isNotEmpty())->values();

    // Fed to <x-layout>, which threads it to <x-nav> for the archive
    // topbar's chapter links.
    $topbarChapters = $chapters->isNotEmpty()
        ? $chapters->map(fn($chapter, $i) => [
            'slug' => $chapter['slug'],
            'label' => $chapter['label'],
            'shortLabel' => Str::upper($chapter['shortLabel']),
            'index' => sprintf('%02d', $i + 1),
        ])->all()
        : null;

    $otherGroups = $grouped->except(['graphic-design', 'uiux-design', 'it-development', 'ai-data']);

    // Authentic output crops for AI & Data; other entries use coverImagePath().
    $rawCovers = [
        21 => 'projects/vision-ai/recognition-scanner-raw.webp',
        24 => 'projects/webgis/trend-chart-raw.webp',
    ];
@endphp
{{-- title/meta-description routed through __() (: prefix, not a literal
string attribute) so they switch locale -- same gap and fix as the
homepage, found during the Language Content Completion pass. --}}
<x-layout :title="__('Project Archive | Surya Andika')"
    :meta-description="__('The full project archive: graphic design, UI/UX, web systems, and AI & data work by Surya Andika.')"
    :archive-chapters="$topbarChapters">

    {{-- Editorial Contact Sheet with chapter navigation integrated into the
    single archive topbar (see nav.blade.php's archiveChapters branch)
    -- there is no second sticky bar on this page. Each chapter uses a
    density/treatment matched to how that category's work is actually
    meant to be looked at: Graphic Design is artwork-first (no chrome,
    denser grid), UI/UX and Web/App are interface-first
    (browser-chrome, reused unmodified from the project detail page). --}}
    <header class="reveal pt-12 lg:pt-16 pb-12 lg:pb-16 px-8 lg:px-20 max-w-[1600px] mx-auto">
        <p class="text-eyebrow font-bold uppercase tracking-[0.25em] text-primary-fg mb-4">{{ __('Archive') }}</p>
        <h1 class="text-heading lg:text-display font-extrabold tracking-tight text-ink max-w-2xl">
            {{ __('The complete collection.') }}
        </h1>
    </header>

    <div class="max-w-[1600px] mx-auto px-8 lg:px-20 pb-24 lg:pb-32">

        @if ($chapters->isEmpty() && $otherGroups->isEmpty())
            <div class="py-20 text-center">
                <p class="text-body text-muted font-medium">{{ __('No projects available at the moment.') }}</p>
            </div>
        @endif

        {{-- Section 1: Graphic Design. Artwork-first -- no device chrome,
        the poster/asset itself is the subject. Contact-sheet density:
        up to 3 columns desktop. --}}
        @if ($grouped->get('graphic-design', collect())->isNotEmpty())
            @php $gd = $grouped->get('graphic-design'); @endphp
            <section id="graphic-design" data-chapter-section class="reveal mb-24 lg:mb-28 scroll-mt-28">
                <div class="relative mb-10">
                    <span aria-hidden="true"
                        class="absolute -top-6 lg:-top-10 left-0 text-[5rem] lg:text-[7rem] font-extrabold text-soft-muted/15 leading-none select-none">01</span>
                    <div class="relative">
                        <span
                            class="text-eyebrow font-bold uppercase tracking-widest text-primary-fg">{{ __($categoryNames->get('graphic-design')->name ?? 'Graphic Design') }}</span>
                        <h2 class="text-subheading font-extrabold text-ink mt-2">
                            {{ trans_choice('messages.projects_count', $gd->count(), ['count' => $gd->count()]) }}
                        </h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
                    @foreach ($gd as $i => $project)
                        <a href="{{ route('portfolio.show', $project->id) }}" style="--reveal-delay: {{ $loop->index * 40 }}ms"
                            class="reveal group block outline-none focus-visible:ring-2 focus-visible:ring-primary/40 rounded-[var(--radius-md)]">
                            <div class="relative aspect-[4/5] rounded-[var(--radius-md)] overflow-hidden bg-canvas">
                                <img src="{{ asset('storage/' . $project->coverImagePath()) }}" loading="lazy" decoding="async"
                                    class="lazy-fade w-full h-full object-cover object-top transform group-hover:scale-[1.015] group-focus-visible:scale-[1.015] motion-reduce:scale-100 transition-transform duration-[var(--motion-interactive)] ease-[var(--ease-interactive)]"
                                    alt="{{ __(':title preview', ['title' => $project->title]) }}">
                            </div>
                            <div class="flex items-baseline gap-2 mt-4">
                                <span
                                    class="text-meta font-bold text-primary-fg/60 tabular-nums">{{ sprintf('%02d', $i + 1) }}</span>
                                <h3
                                    class="text-small font-bold text-ink group-hover:text-primary-fg transition-colors duration-[var(--motion-fast)]">
                                    {{ $project->title }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Section 2: UI/UX Design. Interface-first -- Figma-style browser
        chrome frames each screen. --}}
        @if ($grouped->get('uiux-design', collect())->isNotEmpty())
            @php $uiux = $grouped->get('uiux-design'); @endphp
            <section id="uiux-design" data-chapter-section class="reveal mb-24 lg:mb-28 scroll-mt-28">
                <div class="relative mb-10">
                    <span aria-hidden="true"
                        class="absolute -top-6 lg:-top-10 left-0 text-[5rem] lg:text-[7rem] font-extrabold text-soft-muted/15 leading-none select-none">02</span>
                    <div class="relative">
                        <span
                            class="text-eyebrow font-bold uppercase tracking-widest text-primary-fg">{{ __($categoryNames->get('uiux-design')->name ?? 'UI/UX Design') }}</span>
                        <h2 class="text-subheading font-extrabold text-ink mt-2">
                            {{ trans_choice('messages.projects_count', $uiux->count(), ['count' => $uiux->count()]) }}
                        </h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
                    @foreach ($uiux as $i => $project)
                        <a href="{{ route('portfolio.show', $project->id) }}" style="--reveal-delay: {{ $loop->index * 60 }}ms"
                            class="reveal group block outline-none focus-visible:ring-2 focus-visible:ring-primary/40 rounded-[var(--radius-md)]">
                            <x-browser-chrome accent="uiux" :label="$project->title" class="!rounded-[var(--radius-md)]">
                                <div class="aspect-[16/9] overflow-hidden bg-canvas">
                                    <img src="{{ asset('storage/' . $project->coverImagePath()) }}" loading="lazy"
                                        decoding="async"
                                        class="lazy-fade w-full h-full object-cover object-top transform group-hover:scale-[1.015] group-focus-visible:scale-[1.015] motion-reduce:scale-100 transition-transform duration-[var(--motion-interactive)] ease-[var(--ease-interactive)]"
                                        alt="{{ __(':title preview', ['title' => $project->title]) }}">
                                </div>
                            </x-browser-chrome>
                            <div class="flex items-baseline gap-2 mt-4">
                                <span
                                    class="text-meta font-bold text-primary-fg/60 tabular-nums">{{ sprintf('%02d', $i + 1) }}</span>
                                <h3
                                    class="text-small font-bold text-ink group-hover:text-primary-fg transition-colors duration-[var(--motion-fast)]">
                                    {{ $project->title }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Section 3: Web & Systems. Implementation-first -- same
        browser-chrome treatment, wider aspect for a real product
        screenshot. Split from the former single "IT & Development"
        chapter (Archive Taxonomy Restructure) once it grew to 7
        substantially different projects; this chapter keeps the
        application/interface-driven half (SPMB, InfoKand, Dashboard
        Kreatif, Dinda POS). --}}
        @if ($grouped->get('it-development', collect())->isNotEmpty())
            @php $webSystems = $grouped->get('it-development'); @endphp
            <section id="it-development" data-chapter-section class="reveal mb-24 lg:mb-28 scroll-mt-28">
                <div class="relative mb-10">
                    <span aria-hidden="true"
                        class="absolute -top-6 lg:-top-10 left-0 text-[5rem] lg:text-[7rem] font-extrabold text-soft-muted/15 leading-none select-none">03</span>
                    <div class="relative">
                        <span
                            class="text-eyebrow font-bold uppercase tracking-widest text-primary-fg">{{ __($categoryNames->get('it-development')->name ?? 'Web & Systems') }}</span>
                        <h2 class="text-subheading font-extrabold text-ink mt-2">
                            {{ trans_choice('messages.projects_count', $webSystems->count(), ['count' => $webSystems->count()]) }}
                        </h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 lg:gap-10">
                    @foreach ($webSystems as $i => $project)
                        <a href="{{ route('portfolio.show', $project->id) }}" style="--reveal-delay: {{ $loop->index * 80 }}ms"
                            class="reveal group block outline-none focus-visible:ring-2 focus-visible:ring-primary/40 rounded-[var(--radius-md)]">
                            @if (isset($rawCovers[$project->id]))
                                {{-- Raw/full-bleed treatment: the real screenshot is the
                                     composition, no device chrome around it. --}}
                                <div class="aspect-[16/10] rounded-[var(--radius-md)] overflow-hidden bg-canvas">
                                    <img src="{{ asset('storage/' . $rawCovers[$project->id]) }}" loading="lazy"
                                        decoding="async"
                                        class="lazy-fade w-full h-full object-cover object-center transform group-hover:scale-[1.015] group-focus-visible:scale-[1.015] motion-reduce:scale-100 transition-transform duration-[var(--motion-interactive)] ease-[var(--ease-interactive)]"
                                        alt="{{ __(':title preview', ['title' => $project->title]) }}">
                                </div>
                            @else
                                <x-browser-chrome accent="it" :label="Str::slug($project->title) . '.app'"
                                    class="archive-browser-cover !rounded-[var(--radius-md)]">
                                    <div class="min-h-0 flex-1 overflow-hidden bg-canvas">
                                        <img src="{{ asset('storage/' . $project->coverImagePath()) }}" loading="lazy"
                                            decoding="async"
                                            class="lazy-fade w-full h-full object-cover object-top transform group-hover:scale-[1.015] group-focus-visible:scale-[1.015] motion-reduce:scale-100 transition-transform duration-[var(--motion-interactive)] ease-[var(--ease-interactive)]"
                                            alt="{{ __(':title preview', ['title' => $project->title]) }}">
                                    </div>
                                </x-browser-chrome>
                            @endif
                            <div class="flex items-baseline gap-2 mt-4">
                                <span
                                    class="text-meta font-bold text-primary-fg/60 tabular-nums">{{ sprintf('%02d', $i + 1) }}</span>
                                <h3
                                    class="text-small font-bold text-ink group-hover:text-primary-fg transition-colors duration-[var(--motion-fast)]">
                                    {{ $project->title }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- AI & Data uses authentic, full-bleed output with no browser frame. --}}
        @if ($grouped->get('ai-data', collect())->isNotEmpty())
            @php $aiData = $grouped->get('ai-data'); @endphp
            <section id="ai-data" data-chapter-section class="reveal mb-16 scroll-mt-28">
                <div class="relative mb-10">
                    <span aria-hidden="true"
                        class="absolute -top-6 lg:-top-10 left-0 text-[5rem] lg:text-[7rem] font-extrabold text-soft-muted/15 leading-none select-none">04</span>
                    <div class="relative">
                        <span
                            class="text-eyebrow font-bold uppercase tracking-widest text-primary-fg">{{ __($categoryNames->get('ai-data')->name ?? 'AI & Data') }}</span>
                        <h2 class="text-subheading font-extrabold text-ink mt-2">
                            {{ trans_choice('messages.projects_count', $aiData->count(), ['count' => $aiData->count()]) }}
                        </h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8 lg:gap-10">
                    @foreach ($aiData as $i => $project)
                        <a href="{{ route('portfolio.show', $project->id) }}" style="--reveal-delay: {{ $loop->index * 80 }}ms"
                            class="reveal group block outline-none focus-visible:ring-2 focus-visible:ring-primary/40 rounded-[var(--radius-md)]">
                            <div data-cover-treatment="raw" class="aspect-[16/10] rounded-[var(--radius-md)] overflow-hidden bg-canvas">
                                <img src="{{ asset('storage/' . ($rawCovers[$project->id] ?? $project->coverImagePath())) }}"
                                    loading="lazy" decoding="async"
                                    class="lazy-fade w-full h-full object-cover object-center group-hover:scale-[1.015] group-focus-visible:scale-[1.015] motion-reduce:scale-100 transition-transform duration-[var(--motion-interactive)]"
                                    alt="{{ __(':title preview', ['title' => $project->title]) }}">
                            </div>
                            <div class="flex items-baseline gap-2 mt-4">
                                <span
                                    class="text-meta font-bold text-primary-fg/60 tabular-nums">{{ sprintf('%02d', $i + 1) }}</span>
                                <h3
                                    class="text-small font-bold text-ink group-hover:text-primary-fg transition-colors duration-[var(--motion-fast)]">
                                    {{ $project->title }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Any category outside the 4 core disciplines (e.g. Fotografi,
        Modeling): same artwork-first poster treatment as Graphic
        Design, neutral accent, no topbar chapter link (not part of
        $topbarChapters above). --}}
        @foreach ($otherGroups as $slug => $items)
            <section class="reveal mb-16 scroll-mt-28">
                <div class="mb-10">
                    {{-- Same __() treatment as the category eyebrow on the
                    project detail page -- see that comment. --}}
                    <span
                        class="text-eyebrow font-bold uppercase tracking-widest text-muted">{{ $items->first()->category->name ? __($items->first()->category->name) : __('Other Work') }}</span>
                    <h2 class="text-subheading font-extrabold text-ink mt-2">
                        {{ $items->first()->category->name ? __($items->first()->category->name) : __('Other Work') }}
                    </h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
                    @foreach ($items as $project)
                        <a href="{{ route('portfolio.show', $project->id) }}" style="--reveal-delay: {{ $loop->index * 40 }}ms"
                            class="reveal group block outline-none focus-visible:ring-2 focus-visible:ring-primary/40 rounded-[var(--radius-md)]">
                            <div class="relative aspect-[4/5] rounded-[var(--radius-md)] overflow-hidden bg-canvas">
                                <img src="{{ asset('storage/' . $project->coverImagePath()) }}" loading="lazy" decoding="async"
                                    class="lazy-fade w-full h-full object-cover object-top transform group-hover:scale-[1.015] group-focus-visible:scale-[1.015] motion-reduce:scale-100 transition-transform duration-[var(--motion-interactive)] ease-[var(--ease-interactive)]"
                                    alt="{{ __(':title preview', ['title' => $project->title]) }}">
                            </div>
                            <h3
                                class="text-small font-bold text-ink group-hover:text-primary-fg transition-colors duration-[var(--motion-fast)] mt-4">
                                {{ $project->title }}
                            </h3>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    @push('scripts')
        @vite('resources/js/archive-nav.js')
    @endpush

</x-layout>
