<x-layout :title="$project->title . ' | Surya Andika'" :show-back="true">

    @push('styles')
        <style>
            .scrollbar-hide::-webkit-scrollbar { display: none; }
            .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
            .scrollbar-thin::-webkit-scrollbar { width: 6px; }
            .scrollbar-thin::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
        </style>
    @endpush

    <div class="pt-8"> {{-- offset for the fixed nav from x-nav --}}
        <header class="reveal pt-16 pb-16 px-8 lg:px-20 max-w-[1600px] mx-auto">
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-black text-slate-900 mb-8 leading-tight">
                {{ $project->title }}
            </h1>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 py-10 border-t border-b border-slate-200">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Role</p>
                    <p class="text-base sm:text-lg font-bold text-slate-800">{{ $project->role ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Client</p>
                    <p class="text-base sm:text-lg font-bold text-slate-800">{{ $project->client ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Year</p>
                    <p class="text-base sm:text-lg font-bold text-slate-800">{{ $project->year ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Tools</p>
                    <p class="text-base sm:text-lg font-bold text-slate-800">{{ $project->tools ?? '-' }}</p>
                </div>
            </div>
        </header>

        <main class="max-w-[1600px] mx-auto px-8 lg:px-20">
            @php
                $categorySlug = $project->category->slug ?? '';
                $isUiux = $categorySlug === 'uiux-design';
                $isIt = $categorySlug === 'it-development';
                $showcaseTools = $isIt ? array_filter(array_map('trim', explode(',', $project->tools ?? ''))) : [];
                // A fixed aspect keeps the preview to a sane on-screen size instead of
                // dumping the full (sometimes 10,000px+ tall) source image inline; the
                // complete image is only ever fully shown (fit-to-screen + zoomable) in the GSAP slider.
                $showcaseAspectClass = $isUiux || $isIt ? 'aspect-[16/9]' : 'aspect-[4/5]';
                // The main card sits inset/centered (not edge-to-edge) so the peek stack
                // below has room to show a sliver on both the left and right.
                $showcaseWidthClass = $isUiux || $isIt ? 'w-[86%] mx-auto' : 'max-w-md mx-auto';
                // Up to 2 gallery shots peek out from behind the main preview on hover (one
                // to each side), teasing that there's more without a full second grid.
                $peekImages = collect($project->galleryImages())->take(2);
                // Every image on the project (main + gallery), in the order the GSAP slider
                // will present them; trigger buttons reference these by index.
                $mainSlideSrc = $project->image_path ? asset('storage/' . $project->image_path) : null;
                $allSlides = collect([$mainSlideSrc])->filter()->merge(collect($project->galleryImages())->map(fn ($img) => asset($img)))->values();
            @endphp

            {{-- Overview (left) and Visual Showcase (right, sticky) side by side, so the
                 image no longer dominates the page on its own full-width row. --}}
            <div class="reveal py-16 lg:py-20 border-b border-slate-100 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
                <div class="lg:col-span-5">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6">Overview</h3>
                    <p class="text-slate-600 leading-relaxed text-lg sm:text-xl">{{ $project->description }}</p>

                    @if ($project->problem || $project->process || $project->result)
                        <div class="space-y-8 mt-10">
                            @if ($project->problem)
                                <div>
                                    <p class="text-xs font-bold text-red-500 uppercase tracking-widest mb-3">Problem</p>
                                    <p class="text-slate-600 leading-relaxed">{{ $project->problem }}</p>
                                </div>
                            @endif
                            @if ($project->process)
                                <div>
                                    <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-3">Process</p>
                                    <p class="text-slate-600 leading-relaxed">{{ $project->process }}</p>
                                </div>
                            @endif
                            @if ($project->result)
                                <div>
                                    <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-3">Result</p>
                                    <p class="text-slate-600 leading-relaxed">{{ $project->result }}</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <a href="{{ route('home') }}#contact" class="inline-flex items-center gap-2 mt-10 text-blue-600 font-bold hover:text-blue-800 transition-colors">
                        Discuss a similar project
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>

                <div class="lg:col-span-7 lg:self-start lg:sticky lg:top-28">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
                        <h3 class="text-2xl font-bold text-slate-900">Visual Showcase</h3>
                        @if (count($showcaseTools))
                            <div class="flex flex-wrap gap-2">
                                @foreach ($showcaseTools as $tool)
                                    <span class="bg-emerald-50 text-emerald-700 text-xs font-semibold px-3 py-1 rounded-full">{{ $tool }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div data-peek-wrapper class="relative py-3">
                        {{-- Peek stack: up to 2 gallery shots tucked behind the (now centered,
                             inset) main preview, one to each side. Positioned/rotated here in
                             Blade; image-modal.js pops them in with GSAP on hover (a proper
                             "cards popping out" reveal, not a plain CSS fade) since that's the
                             only hover effect this card gets. Purely decorative
                             (pointer-events-none) so they never steal the click. --}}
                        @if ($peekImages->count() >= 1)
                            <div data-peek class="absolute inset-y-3 left-0 w-[78%] rounded-[2rem] overflow-hidden shadow-lg border-4 border-white pointer-events-none opacity-0 scale-90 -rotate-4">
                                <img src="{{ asset($peekImages[0]) }}" class="w-full h-full object-cover object-top brightness-90" alt="">
                            </div>
                        @endif
                        @if ($peekImages->count() >= 2)
                            <div data-peek class="absolute inset-y-3 right-0 w-[78%] rounded-[2rem] overflow-hidden shadow-lg border-4 border-white pointer-events-none opacity-0 scale-90 rotate-4">
                                <img src="{{ asset($peekImages[1]) }}" class="w-full h-full object-cover object-top brightness-90" alt="">
                            </div>
                        @endif

                        @if ($isUiux || $isIt)
                            <x-browser-chrome :accent="$isUiux ? 'uiux' : 'it'" :label="$isUiux ? 'Figma Prototype' : $project->title" class="relative z-10 {{ $showcaseWidthClass }}">
                                <button type="button"
                                        class="relative w-full text-left cursor-zoom-in block {{ $showcaseAspectClass }}"
                                        data-modal-trigger
                                        data-slide-index="0"
                                        aria-label="View {{ $project->title }} main image fullscreen">
                                    <img src="{{ asset('storage/' . $project->image_path) }}" decoding="async" class="lazy-fade w-full h-full object-cover object-top" alt="{{ $project->title }} main showcase">
                                </button>
                            </x-browser-chrome>
                        @else
                            <button type="button"
                                        class="relative z-10 rounded-[2rem] sm:rounded-[3rem] overflow-hidden shadow-2xl border border-slate-200 bg-white cursor-zoom-in block {{ $showcaseAspectClass }} {{ $showcaseWidthClass }}"
                                        data-modal-trigger
                                        data-slide-index="0"
                                        aria-label="View {{ $project->title }} main image fullscreen">
                                    <img src="{{ asset('storage/' . $project->image_path) }}" decoding="async" class="lazy-fade w-full h-full object-cover object-top" alt="{{ $project->title }} main showcase">
                                </button>
                        @endif
                    </div>
                </div>
            </div>

        </main>
    </div>

    <div id="imageModal" class="hidden fixed inset-0 z-[100] bg-slate-950/95 backdrop-blur-sm" role="dialog" aria-modal="true" aria-label="Image preview">
        <button type="button" id="modalClose" class="absolute top-6 right-6 md:top-8 md:right-8 z-10 text-white hover:text-blue-400 transition transform hover:rotate-90 duration-300" aria-label="Close image preview">
            <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <button type="button" id="modalPrev" class="hidden absolute left-3 md:left-6 top-1/2 -translate-y-1/2 z-10 bg-white/10 hover:bg-white/20 text-white p-2.5 md:p-3 rounded-full backdrop-blur-sm transition-colors duration-300" aria-label="Previous image">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button type="button" id="modalNext" class="hidden absolute right-3 md:right-6 top-1/2 -translate-y-1/2 z-10 bg-white/10 hover:bg-white/20 text-white p-2.5 md:p-3 rounded-full backdrop-blur-sm transition-colors duration-300" aria-label="Next image">
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
        </button>

        {{-- GSAP-driven horizontal slider: every project image is a persistent slide (no
             src-swapping), image-modal.js tweens this track's x position between them.
             Each image fits entirely on screen by default (no forced scrolling even for
             very tall/composite exports) — scroll-wheel, double-click, or drag lets you
             zoom in and pan for the fine detail instead. --}}
        <div class="w-full h-full overflow-hidden">
            <div id="modalTrack" class="flex h-full">
                @foreach ($allSlides as $slide)
                    <div class="modal-slide shrink-0 w-screen h-full flex items-center justify-center p-4 md:p-10 lg:p-16 overflow-hidden">
                        <img data-zoom-img src="{{ $slide }}" class="max-w-full max-h-full w-auto h-auto object-contain select-none rounded-lg shadow-2xl cursor-zoom-in" draggable="false" alt="{{ $project->title }}">
                    </div>
                @endforeach
            </div>
        </div>

        <span id="modalZoomHint" class="hidden md:block absolute bottom-6 right-6 md:right-8 text-white/50 text-[11px] font-semibold uppercase tracking-wider">Scroll or tap to zoom</span>

        <span id="modalCounter" class="hidden absolute bottom-6 left-1/2 -translate-x-1/2 text-white/80 text-xs font-bold bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-full tracking-wider"></span>
    </div>

    <footer class="bg-slate-900 py-16 sm:py-20 text-center text-white mt-20">
        <h4 class="text-2xl sm:text-3xl font-bold mb-6">Need a design like this?</h4>
        <a href="{{ route('home') }}#contact" class="inline-block bg-blue-600 px-10 py-4 rounded-full font-bold hover:bg-blue-500 transition shadow-lg shadow-blue-600/20">Contact Surya</a>
    </footer>

    @push('scripts')
        @vite('resources/js/image-modal.js')
    @endpush

</x-layout>
