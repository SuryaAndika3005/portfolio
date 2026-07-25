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
        <header class="pt-16 pb-16 px-8 lg:px-20 max-w-[1600px] mx-auto">
            <h1 class="text-5xl lg:text-7xl font-black text-slate-900 mb-8 leading-tight">
                {{ $project->title }}
            </h1>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-12 py-10 border-t border-b border-slate-200">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Role</p>
                    <p class="text-lg font-bold text-slate-800">{{ $project->role }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Client</p>
                    <p class="text-lg font-bold text-slate-800">{{ $project->client }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Year</p>
                    <p class="text-lg font-bold text-slate-800">{{ $project->year }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Tools</p>
                    <p class="text-lg font-bold text-slate-800">{{ $project->tools }}</p>
                </div>
            </div>
        </header>

        <main class="max-w-[1600px] mx-auto px-8 lg:px-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 py-20">
                <div class="lg:col-span-4">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6">Overview</h3>
                    <p class="text-slate-600 leading-relaxed text-lg italic">&ldquo;{{ $project->description }}&rdquo;</p>

                    <a href="{{ route('home') }}#contact" class="inline-flex items-center gap-2 mt-10 text-blue-600 font-bold hover:text-blue-800 transition-colors">
                        Discuss a similar project
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>

                <div class="lg:col-span-8 space-y-12">
                    <h3 class="text-2xl font-bold text-slate-900">Visual Showcase</h3>

                    <button type="button"
                            class="group relative rounded-[3rem] overflow-hidden shadow-2xl border border-slate-200 bg-white cursor-zoom-in w-full text-left"
                            data-modal-trigger
                            data-image-src="{{ asset('storage/' . $project->image_path) }}"
                            aria-label="View {{ $project->title }} main image fullscreen">
                        <div class="max-h-[650px] overflow-y-auto scrollbar-thin">
                            <img src="{{ asset('storage/' . $project->image_path) }}" class="w-full h-auto object-top" alt="{{ $project->title }} main showcase">
                        </div>
                        <div class="absolute inset-0 bg-blue-600/0 group-hover:bg-blue-600/10 transition-all duration-300 flex items-center justify-center">
                            <span class="bg-white px-6 py-3 rounded-full font-bold shadow-xl opacity-0 group-hover:opacity-100 transition-opacity">Click for Fullscreen</span>
                        </div>
                    </button>

                    @if ($project->galleryImages()->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @foreach ($project->galleryImages() as $image)
                                <button type="button"
                                        class="bg-white p-4 rounded-[2.5rem] shadow-xl border border-slate-100/50 group cursor-zoom-in text-left w-full"
                                        data-modal-trigger
                                        data-image-src="{{ asset($image) }}"
                                        aria-label="View detail image {{ $loop->iteration }} of {{ $project->title }} fullscreen">
                                    <div class="aspect-[4/5] rounded-[1.8rem] overflow-hidden border border-slate-50 bg-slate-100">
                                        <img src="{{ asset($image) }}"
                                             loading="lazy"
                                             class="w-full h-auto object-cover object-top transition duration-700 group-hover:scale-105"
                                             alt="{{ $project->title }} detail view {{ $loop->iteration }}">
                                    </div>
                                    <div class="mt-4 px-4 pb-2">
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Project Detail: {{ $project->title }}</p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <div id="imageModal" class="hidden fixed inset-0 z-[100] bg-slate-950/95 backdrop-blur-sm items-center justify-center p-4 md:p-10" role="dialog" aria-modal="true" aria-label="Image preview">
        <button type="button" id="modalClose" class="absolute top-8 right-8 text-white hover:text-blue-400 transition transform hover:rotate-90 duration-300" aria-label="Close image preview">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <div class="max-w-full max-h-full overflow-auto rounded-2xl scrollbar-hide">
            <img id="modalImg" src="" class="w-full h-auto rounded-lg" alt="">
        </div>
    </div>

    <footer class="bg-slate-900 py-20 text-center text-white mt-20">
        <h4 class="text-3xl font-bold mb-6">Need a design like this?</h4>
        <a href="{{ route('home') }}#contact" class="inline-block bg-blue-600 px-10 py-4 rounded-full font-bold hover:bg-blue-500 transition shadow-lg shadow-blue-600/20">Contact Surya</a>
    </footer>

    @push('scripts')
        <script src="{{ asset('js/image-modal.js') }}" defer></script>
    @endpush

</x-layout>
