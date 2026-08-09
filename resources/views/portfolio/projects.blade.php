<x-layout title="Project Archive | Surya Andika" :show-back="true">

    @php
        // ->toBase() strips the Eloquent Collection wrapper, whose get()/except()
        // are overridden for primary-key lookups and would misbehave on the
        // string-slug keys groupBy() produces here.
        $grouped = $projects->groupBy(fn($project) => $project->category->slug ?? 'other')->toBase();
        $graphicDesign = $grouped->get('graphic-design', collect());
        $uiux = $grouped->get('uiux-design', collect());
        $itDev = $grouped->get('it-development', collect());
        $otherGroups = $grouped->except(['graphic-design', 'uiux-design', 'it-development']);
    @endphp

    <header class="reveal pt-16 pb-16 px-8 lg:px-20 max-w-[1600px] mx-auto text-center">
        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-slate-900 mb-6 tracking-tight">
            Project <span
                class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Archive.</span>
        </h1>
        <p class="text-lg lg:text-xl text-slate-500 max-w-2xl mx-auto leading-relaxed">
            A complete collection of my digital journeys, from code architecture to classy visual explorations.
        </p>
    </header>

    <main class="max-w-[1600px] mx-auto px-8 lg:px-20 pb-32">

        @if ($graphicDesign->isNotEmpty() || $uiux->isNotEmpty() || $itDev->isNotEmpty())
            <div class="reveal flex flex-wrap justify-center gap-2 bg-slate-100 p-1.5 rounded-full border border-slate-200/60 shadow-inner w-fit mx-auto mb-20"
                role="navigation" aria-label="Jump to project category">
                @if ($graphicDesign->isNotEmpty())
                    <a href="#graphic-design"
                        class="px-6 py-2.5 text-sm font-semibold rounded-full text-slate-600 hover:text-white hover:bg-blue-600 transition-colors duration-300">Visual
                        Design</a>
                @endif
                @if ($uiux->isNotEmpty())
                    <a href="#uiux-design"
                        class="px-6 py-2.5 text-sm font-semibold rounded-full text-slate-600 hover:text-white hover:bg-violet-600 transition-colors duration-300">UI/UX</a>
                @endif
                @if ($itDev->isNotEmpty())
                    <a href="#it-development"
                        class="px-6 py-2.5 text-sm font-semibold rounded-full text-slate-600 hover:text-white hover:bg-emerald-600 transition-colors duration-300">Web
                        &amp; App Dev</a>
                @endif
            </div>
        @endif

        @if ($graphicDesign->isEmpty() && $uiux->isEmpty() && $itDev->isEmpty() && $otherGroups->isEmpty())
            <div class="py-20 text-center">
                <p class="text-slate-400 font-medium">No projects available at the moment.</p>
            </div>
        @endif

        {{-- Section 1: Visual & Graphic Design. Clean poster grid, no chrome. --}}
        @if ($graphicDesign->isNotEmpty())
            <section id="graphic-design" class="reveal mb-28 scroll-mt-28">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                    <div>
                        <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest">01. Visual
                            Design</span>
                        <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">Visual &amp; Graphic Design</h3>
                    </div>
                    <p class="text-slate-500 max-w-md">Bold visual identities, brand campaigns, and print-ready
                        compositions.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($graphicDesign as $project)
                        @php($tools = array_filter(array_map('trim', explode(',', $project->tools ?? ''))))
                        <a href="{{ route('portfolio.show', $project->id) }}" style="--reveal-delay: {{ $loop->index * 60 }}ms"
                            class="reveal group block">
                            <div
                                class="relative aspect-[4/5] rounded-[2rem] overflow-hidden bg-slate-100 shadow-sm group-hover:shadow-xl group-hover:ring-2 group-hover:ring-blue-400/40 transition-all duration-500 ease-out border border-slate-200/50">
                                <img src="{{ asset('storage/' . $project->image_path) }}" loading="lazy" decoding="async"
                                    class="lazy-fade w-full h-full object-cover object-top transform group-hover:scale-105 transition-transform duration-500 ease-out"
                                    alt="{{ $project->title }} preview">
                            </div>

                            <h4
                                class="text-xl font-bold text-slate-900 mt-5 group-hover:text-blue-600 transition-colors duration-300">
                                {{ $project->title }}</h4>
                            <p class="text-sm text-slate-500 mt-1">{{ $project->description ?? 'Graphic Design' }}</p>
                        
        @if (count($tools))
            <div class="flex flex-wrap gap-2 mt-4">
                @foreach ($tools as $tool)
                    <span class="bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full">
                        {{ $tool }}
                    </span>
                @endforeach
            </div>
        @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Section 2: UI/UX Case Studies. Framed prototype cards, top-cropped flow preview + tool chips. --}}
        @if ($uiux->isNotEmpty())
        <section id="uiux-design" class="reveal mb-28 scroll-mt-28">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                <div>
                    <span class="text-xs font-extrabold text-violet-600 uppercase tracking-widest">02. Product
                        Design</span>
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">UI/UX Case Studies</h3>
                </div>
                <p class="text-slate-500 max-w-md">End-to-end product flows: research, wireframing, and interactive
                    prototypes.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                @foreach ($uiux as $project)
                @php($tools = array_filter(array_map('trim', explode(',', $project->tools ?? ''))))
                <a href="{{ route('portfolio.show', $project->id) }}" style="--reveal-delay: {{ $loop->index * 80 }}ms"
                    class="reveal group block">
                    <x-browser-chrome accent="uiux" label="Figma Prototype">
                        <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                            <img src="{{ asset('storage/' . $project->image_path) }}" loading="lazy" decoding="async"
                                class="lazy-fade w-full h-full object-cover object-top transform group-hover:scale-105 transition-transform duration-500 ease-out"
                                alt="{{ $project->title }} preview">
                        </div>
                    </x-browser-chrome>

                    <h4
                        class="text-xl font-bold text-slate-900 mt-5 group-hover:text-violet-600 transition-colors duration-300">
                        {{ $project->title }}</h4>
                    <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $project->description }}</p>

                    @if (count($tools))
                        <div class="flex flex-wrap gap-2 mt-4">
                            @foreach ($tools as $tool)
                                <span
                                    class="bg-violet-50 text-violet-700 text-xs font-semibold px-3 py-1 rounded-full">{{ $tool }}</span>
                            @endforeach
                        </div>
                    @endif
                </a>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Section 3: Web & App Development. Literal browser-chrome frame, wide screenshot crop + tool chips. --}}
        @if ($itDev->isNotEmpty())
        <section id="it-development" class="reveal mb-16 scroll-mt-28">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                <div>
                    <span class="text-xs font-extrabold text-emerald-600 uppercase tracking-widest">03.
                        Engineering</span>
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">Web &amp; App Development</h3>
                </div>
                <p class="text-slate-500 max-w-md">Responsive, production-ready builds, from pixel-perfect integration
                    to scalable architecture.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                @foreach ($itDev as $project)
                @php($tools = array_filter(array_map('trim', explode(',', $project->tools ?? ''))))
                <a href="{{ route('portfolio.show', $project->id) }}" style="--reveal-delay: {{ $loop->index * 80 }}ms"
                    class="reveal group block">
                    <x-browser-chrome accent="it" label="{{ Str::slug($project->title) }}.app">
                        <div class="aspect-[16/10] overflow-hidden bg-slate-100">
                            <img src="{{ asset('storage/' . $project->image_path) }}" loading="lazy" decoding="async"
                                class="lazy-fade w-full h-full object-cover object-top transform group-hover:scale-105 transition-transform duration-500 ease-out"
                                alt="{{ $project->title }} preview">
                        </div>
                    </x-browser-chrome>

                    <h4
                        class="text-xl font-bold text-slate-900 mt-5 group-hover:text-emerald-600 transition-colors duration-300">
                        {{ $project->title }}</h4>
                    <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $project->description }}</p>

                    @if (count($tools))
                        <div class="flex flex-wrap gap-2 mt-4">
                            @foreach ($tools as $tool)
                                <span
                                    class="bg-emerald-50 text-emerald-700 text-xs font-semibold px-3 py-1 rounded-full">{{ $tool }}</span>
                            @endforeach
                        </div>
                    @endif
                </a>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Any category outside the 3 core disciplines (e.g. Fotografi, Modeling): same poster treatment, neutral
        accent. --}}
        @foreach ($otherGroups as $slug => $items)
            <section class="reveal mb-16 scroll-mt-28">
                <div class="mb-10">
                    <span
                        class="text-xs font-extrabold text-slate-500 uppercase tracking-widest">{{ $items->first()->category->name ?? 'Other Work' }}</span>
                    <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">
                        {{ $items->first()->category->name ?? 'Other Work' }}</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($items as $project)
                        <a href="{{ route('portfolio.show', $project->id) }}" style="--reveal-delay: {{ $loop->index * 60 }}ms"
                            class="reveal group block">
                            <div
                                class="relative aspect-[4/5] rounded-[2rem] overflow-hidden bg-slate-100 shadow-sm group-hover:shadow-xl group-hover:ring-2 group-hover:ring-slate-400/40 transition-all duration-500 ease-out border border-slate-200/50">
                                <img src="{{ asset('storage/' . $project->image_path) }}" loading="lazy" decoding="async"
                                    class="lazy-fade w-full h-full object-cover object-top transform group-hover:scale-105 transition-transform duration-500 ease-out"
                                    alt="{{ $project->title }} preview">
                            </div>
                            <h4
                                class="text-xl font-bold text-slate-900 mt-5 group-hover:text-slate-600 transition-colors duration-300">
                                {{ $project->title }}</h4>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </main>

</x-layout>