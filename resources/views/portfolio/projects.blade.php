<x-layout title="Project Archive | Surya Andika" :show-back="true">

    <header class="reveal pt-16 pb-16 px-8 lg:px-20 max-w-[1600px] mx-auto text-center">
        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-slate-900 mb-6 tracking-tight">
            Project <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Archive.</span>
        </h1>
        <p class="text-lg lg:text-xl text-slate-500 max-w-2xl mx-auto leading-relaxed">
            A complete collection of my digital journeys, from code architecture to classy visual explorations.
        </p>
    </header>

    <main class="max-w-[1600px] mx-auto px-8 lg:px-20 pb-32">

        <div class="reveal flex flex-wrap justify-center gap-2 bg-slate-100 p-1.5 rounded-full border border-slate-200/60 shadow-inner w-fit mx-auto mb-20" role="group" aria-label="Filter projects by category">
            <button type="button" data-filter="all" class="filter-btn active-filter px-6 py-2.5 text-sm font-bold rounded-full transition-all" aria-pressed="true">
                All Projects
            </button>
            @foreach($categories as $category)
                <button type="button" data-filter="{{ $category->slug }}" class="filter-btn px-6 py-2.5 text-sm font-semibold rounded-full text-slate-500 hover:text-slate-800 hover:bg-slate-200 transition-all" aria-pressed="false">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        <div id="projects-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($projects as $project)
                <div style="--reveal-delay: {{ $loop->index * 60 }}ms" class="reveal project-item group" data-category="{{ $project->category->slug ?? 'all' }}">
                    <a href="{{ route('portfolio.show', $project->id) }}" class="block">
                        <div class="relative aspect-[4/3] rounded-[2.5rem] overflow-hidden shadow-xl border border-slate-100 bg-slate-200">
                            <img src="{{ asset('storage/' . $project->image_path) }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700 ease-out"
                                 alt="{{ $project->title }} — {{ $project->category->name ?? 'project' }} preview">

                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500">
                                <div class="absolute bottom-0 left-0 w-full p-8 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                    <span class="bg-blue-600 text-white text-[10px] font-bold px-3 py-1 rounded-full mb-3 inline-block uppercase tracking-widest">
                                        {{ $project->category->name ?? 'Uncategorized' }}
                                    </span>
                                    <h4 class="text-2xl font-bold text-white mb-2">{{ $project->title }}</h4>
                                    <p class="text-xs text-slate-300 line-clamp-2">{{ $project->description }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-span-full py-20 text-center">
                    <p class="text-slate-400 font-medium">No projects available at the moment.</p>
                </div>
            @endforelse
        </div>

        <p id="filter-empty-state" class="hidden col-span-full py-20 text-center text-slate-400 font-medium">
            No projects in this category yet.
        </p>
    </main>

    @push('scripts')
        <script src="{{ asset('js/project-filter.js') }}" defer></script>
    @endpush

</x-layout>
