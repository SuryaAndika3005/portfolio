<x-layout>

    <header
        class="relative max-w-[1600px] mx-auto px-8 lg:px-20 pt-32 pb-24 flex flex-col items-center justify-center text-center min-h-[80vh] overflow-hidden">

        <div class="absolute top-20 left-10 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-[120px] opacity-30 animate-pulse"
            aria-hidden="true"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-cyan-300 rounded-full mix-blend-multiply filter blur-[120px] opacity-30 animate-pulse"
            style="animation-delay: 2s;" aria-hidden="true"></div>

        <div class="relative z-10 max-w-3xl">
            <div
                class="inline-flex items-center space-x-3 bg-white/60 backdrop-blur-sm px-5 py-2.5 rounded-full mb-8 border border-slate-200 shadow-sm">
                <span class="relative flex h-3.5 w-3.5">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-green-500"></span>
                </span>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-widest">Available for New
                    Projects</span>
            </div>

            <h1
                class="text-5xl sm:text-6xl lg:text-7xl font-extrabold leading-tight mb-5 text-slate-900 tracking-tight">
                Hi, I'm <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Surya
                    Andika.</span>
            </h1>

            <h2
                class="text-xl sm:text-2xl lg:text-3xl font-bold mb-6 h-9 sm:h-10 lg:h-11 flex items-center justify-center">
                <span id="role-rotator" data-roles="Graphic Designer,UI/UX Designer,Web Developer"
                    class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500 transition-opacity duration-300">Graphic
                    Designer</span>
            </h2>

            <p class="text-slate-500 mb-12 max-w-xl mx-auto text-base sm:text-lg lg:text-xl leading-relaxed">
                I craft bold visual identities and brand materials, backed by the ability to design and build the
                digital products that carry them, from concept to high-performance code.
            </p>

            <div class="flex flex-wrap gap-5 items-center justify-center">
                <a href="#projects"
                    class="group relative px-8 py-4 bg-blue-600 text-white rounded-full font-bold overflow-hidden shadow-xl shadow-blue-200 hover:shadow-2xl hover:shadow-blue-300 transition-all duration-300 transform hover:-translate-y-1 text-lg">
                    <div
                        class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-500">
                    </div>
                    <span class="relative flex items-center gap-2">
                        See Projects
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </span>
                </a>
                <a href="{{ asset('storage/projects/CV.pdf') }}" download
                    class="px-8 py-4 bg-white text-slate-700 rounded-full font-bold border-2 border-slate-200 hover:border-blue-600 hover:text-blue-600 transition-all duration-300 text-lg">
                    Resume
                </a>
            </div>

            <div class="flex items-center justify-center gap-3 mt-16 pt-8 border-t border-slate-200/70">
                <p class="text-3xl font-black text-slate-900">{{ $projectCount }}+</p>
                <p class="text-sm font-semibold text-slate-500 text-left leading-snug">Projects shipped<br>across design
                    &amp; development</p>
            </div>
        </div>
    </header>

    <section id="about" class="reveal max-w-[1600px] mx-auto px-8 lg:px-20 py-24 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-center">
            <div class="lg:col-span-5 relative flex justify-center lg:justify-start">
                <div class="relative w-full max-w-xs sm:max-w-sm">
                    <div class="rounded-[2.5rem] overflow-hidden shadow-2xl border border-slate-100 aspect-[4/5]">
                        <img src="{{ asset('storage/projects/dika.webp') }}" loading="lazy" decoding="async"
                            class="lazy-fade w-full h-full object-cover" alt="Surya Andika">
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7 text-center lg:text-left">
                <span class="text-sm font-extrabold text-blue-600 uppercase tracking-widest">About Me</span>
                <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3 mb-6">Design-first thinking, backed
                    by code.</h3>
                <p class="text-slate-600 text-lg leading-relaxed mb-4">
                    I'm Surya, an Informatics student at Andalas University and a graphic designer at 523 Studio. My
                    work sits at the intersection of visual design and web development. I care as much about how
                    something looks as how it's built.
                </p>
                <p class="text-slate-600 text-lg leading-relaxed">
                    From brand identities to full product interfaces, I like owning a project end-to-end: research,
                    design, and, when the project calls for it, the code that ships it.
                </p>
                <div class="flex flex-wrap gap-3 mt-8 justify-center lg:justify-start">
                    <span class="px-4 py-2 bg-slate-100 rounded-full text-sm font-semibold text-slate-700">Padang,
                        Indonesia</span>
                    <span class="px-4 py-2 bg-slate-100 rounded-full text-sm font-semibold text-slate-700">Informatics @
                        Unand</span>
                    <span class="px-4 py-2 bg-slate-100 rounded-full text-sm font-semibold text-slate-700">523
                        Studio</span>
                </div>
            </div>
        </div>
    </section>

    <section id="projects" class="max-w-[1600px] mx-auto px-8 lg:px-20 py-24 relative z-10">
        <div class="reveal flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
            <div>
                <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-2">Selected Works</h3>
                <p class="text-lg text-slate-500">Three disciplines, one practice. Hover a panel to explore it.</p>
            </div>
        </div>

        @php
            $accordionPanels = [
                ['slug' => 'graphic-design', 'label' => 'Graphic Design', 'tagline' => 'Visual identities & brand campaigns', 'badge' => 'bg-blue-600/90'],
                ['slug' => 'uiux-design', 'label' => 'UI/UX Design', 'tagline' => 'Product flows, wireframes & prototypes', 'badge' => 'bg-violet-600/90'],
                ['slug' => 'it-development', 'label' => 'Web & App Development', 'tagline' => 'Responsive, production-ready builds', 'badge' => 'bg-emerald-600/90'],
            ];
            $accordionGrouped = $projects->groupBy(fn($project) => $project->category->slug ?? 'other')->toBase();
        @endphp

        @if ($accordionGrouped->isEmpty())
            <div class="py-20 text-center text-slate-400">No projects available at the moment.</div>
        @else
        <div id="works-accordion" class="reveal flex flex-col lg:flex-row gap-4 lg:h-[600px]">
            @foreach ($accordionPanels as $panel)
            @php($items = $accordionGrouped->get($panel['slug'], collect()))
            @continue($items->isEmpty())
            <a href="{{ route('portfolio.projects') }}#{{ $panel['slug'] }}" data-accordion-panel
                class="accordion-panel group relative block overflow-hidden rounded-[2rem] min-h-[240px] lg:min-h-0 bg-slate-900">
                <div class="absolute inset-0">
                    {{-- Capped at 4 slides: keeping every image in a category stacked
                    and painting simultaneously (up to 7 for Graphic Design) was
                    part of what made hovering this row feel heavy. --}}
                    @foreach ($items->take(4) as $i => $project)
                        <img data-slide src="{{ asset('storage/' . $project->image_path) }}" loading="lazy" decoding="async"
                            class="absolute inset-0 w-full h-full object-cover object-top {{ $i === 0 ? 'is-active' : '' }}"
                            alt="{{ $project->title }}">
                    @endforeach
                </div>

                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/30 to-slate-900/10"></div>

                <div class="absolute inset-0 p-6 lg:p-8 flex flex-col justify-end">
                    <span
                        class="accordion-count {{ $panel['badge'] }} backdrop-blur-sm text-white text-[11px] font-bold px-3 py-1 rounded-full mb-3 inline-block uppercase tracking-wider w-fit">
                        {{ $items->count() }} {{ Str::plural('Work', $items->count()) }}
                    </span>
                    <h4 class="accordion-title font-black text-white leading-tight">{{ $panel['label'] }}</h4>
                    <p class="accordion-tagline text-sm text-slate-300 mt-1 max-w-xs">{{ $panel['tagline'] }}</p>
                </div>
            </a>
            @endforeach
        </div>
        @endif

        <div class="mt-16 text-center">
            <a href="{{ route('portfolio.projects') }}"
                class="inline-flex items-center gap-3 px-10 py-4 bg-slate-900 text-white rounded-full font-bold hover:bg-blue-600 transition-all duration-300 shadow-xl hover:shadow-blue-500/30 group">
                Explore All Works
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </section>

    <section id="skills" class="bg-slate-900 py-24 mt-10">
        <div class="max-w-[1600px] lg:px-20 mx-auto px-6">
            <div class="reveal text-center mb-16">
                <h3 class="text-3xl font-bold text-white mb-4">Tech &amp; Creative Stack</h3>
                <p class="text-slate-400">A seamless blend of software engineering expertise and visual design.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($skillGroups as $group)
                    <div style="--reveal-delay: {{ $loop->index * 80 }}ms"
                        class="reveal bg-slate-800/50 p-8 rounded-3xl border border-slate-700 hover:border-blue-500 hover:bg-slate-800 transition duration-300 group flex flex-col justify-between shadow-2xl">
                        <div>
                            <div
                                class="mb-6 p-3 bg-slate-700/50 w-fit rounded-2xl group-hover:bg-blue-600 group-hover:text-white text-blue-400 transition duration-300">
                                {!! $group['icon'] !!}
                            </div>
                            <h4 class="text-xl font-bold text-white mb-3">{{ $group['title'] }}</h4>
                            <p class="text-sm text-slate-400 mb-8 leading-relaxed">{{ $group['description'] }}</p>
                        </div>
                        <div
                            class="flex flex-wrap gap-4 items-center opacity-80 group-hover:opacity-100 transition duration-500">
                            @foreach ($group['tools'] as $tool)
                                <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300">
                                    <img src="{{ $tool['icon'] }}" loading="lazy" decoding="async" class="w-7 h-7"
                                        title="{{ $tool['name'] }}" alt="{{ $tool['name'] }} logo">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="education" class="max-w-[1600px] lg:px-20 mx-auto px-6 py-20 relative z-10 -mt-10">
        <div
            class="reveal relative overflow-hidden bg-white rounded-[3rem] p-8 sm:p-10 md:p-14 shadow-2xl shadow-slate-200/50 border border-slate-100 group hover:shadow-blue-200/50 transition-shadow duration-500">
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
                <div class="flex items-start gap-6">
                    <div
                        class="relative bg-gradient-to-br from-blue-600 to-cyan-500 text-white p-5 rounded-3xl shadow-lg transform group-hover:scale-105 transition-transform duration-300 shrink-0">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <span
                            class="text-sm font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500 uppercase tracking-widest">2023
                            - Present</span>
                        <h4 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">Bachelor of
                            Informatics</h4>
                        <p class="text-lg text-slate-500 font-medium mt-1">Andalas University</p>
                    </div>
                </div>
                <div
                    class="w-full md:w-auto flex items-center justify-between md:block gap-4 bg-slate-50 px-6 py-4 rounded-2xl border border-slate-100 shadow-sm text-left md:text-center md:min-w-[140px] group-hover:border-blue-200 transition-colors">
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-0 md:mb-1">Current GPA</p>
                    <p class="text-3xl font-black text-slate-800">3.57</p>
                </div>
            </div>
        </div>
    </section>

    <section id="experience" class="max-w-[1600px] lg:px-20 mx-auto px-6 py-24">
        <div class="reveal text-center mb-16">
            <h3 class="text-4xl font-extrabold text-slate-900 mb-4">The Journey</h3>
            <p class="text-lg text-slate-500">Professional milestones, leadership roles, and creative contributions.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @forelse ($experiences->groupBy('category') as $category => $items)
                <div style="--reveal-delay: {{ $loop->index * 100 }}ms"
                    class="reveal bg-white rounded-[2rem] p-6 sm:p-7 shadow-lg shadow-slate-200/30 border border-slate-100 hover:border-blue-300 transition-colors duration-300 flex flex-col max-h-[26rem]">
                    <h4 class="text-lg font-extrabold text-slate-800 mb-6 shrink-0">{{ $category }}</h4>
                    <div
                        class="scrollbar-thin relative pl-5 border-l-2 border-slate-100 space-y-5 overflow-y-auto pr-2 -mr-2">
                        @foreach ($items as $item)
                            <div class="relative">
                                <div
                                    class="absolute -left-[1.4rem] top-1 w-2.5 h-2.5 bg-blue-500 rounded-full ring-4 ring-white">
                                </div>
                                <p class="text-[11px] font-bold text-blue-500 uppercase tracking-wider mb-1">
                                    {{ $item->duration }}</p>
                                <h5 class="text-sm font-bold text-slate-800">{{ $item->role }}</h5>
                                <p class="text-xs text-slate-500">{{ $item->company }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-slate-400 text-center col-span-3">No experience entries yet.</p>
            @endforelse
        </div>
    </section>

    <section id="contact" class="relative bg-slate-900 pt-32 pb-12 mt-20 overflow-hidden border-t border-slate-800">
        <div class="max-w-[1600px] mx-auto px-8 lg:px-20 relative z-10">
            <div class="flex flex-col lg:flex-row gap-16 lg:gap-24 items-center">

                <div class="reveal w-full lg:w-5/12 text-center lg:text-left">
                    <h2
                        class="text-4xl sm:text-5xl lg:text-7xl font-extrabold text-white mb-6 tracking-tight leading-tight">
                        Let's build <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">something
                            great.</span>
                    </h2>
                    <p class="text-lg text-slate-400 mb-10 leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Whether it's app design, visual identity, or web development projects, I am always open to
                        discussing new ideas.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="mailto:{{ config('portfolio.contact_email') }}"
                            class="group flex items-center gap-4 bg-slate-800/50 border border-slate-700 p-4 rounded-2xl hover:bg-slate-800 hover:border-blue-500 transition-all duration-300 w-full sm:w-auto backdrop-blur-sm">
                            <div class="text-left">
                                <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-0.5">Email</p>
                                <p class="text-sm font-bold text-white">{{ config('portfolio.contact_email') }}</p>
                            </div>
                        </a>
                        <a href="https://wa.me/{{ config('portfolio.whatsapp_number') }}" target="_blank" rel="noopener"
                            class="group flex items-center gap-4 bg-slate-800/50 border border-slate-700 p-4 rounded-2xl hover:bg-slate-800 hover:border-green-500 transition-all duration-300 w-full sm:w-auto backdrop-blur-sm">
                            <div class="text-left">
                                <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-0.5">WhatsApp
                                </p>
                                <p class="text-sm font-bold text-white">{{ config('portfolio.whatsapp_display') }}</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="reveal w-full lg:w-7/12" style="--reveal-delay: 120ms">
                    <div
                        class="bg-white/5 backdrop-blur-xl border border-white/10 p-6 sm:p-8 lg:p-12 rounded-[3rem] shadow-2xl relative overflow-hidden">
                        @if(session('success'))
                            <div class="bg-green-500/10 border border-green-500/50 text-green-400 px-6 py-4 rounded-2xl mb-6 font-medium"
                                role="status">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="bg-red-500/10 border border-red-500/50 text-red-400 px-6 py-4 rounded-2xl mb-6 font-medium"
                                role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('contact.send') }}" method="POST" class="space-y-6 relative z-10"
                            novalidate>
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-slate-400 mb-2 ml-2">Your
                                        Name</label>
                                    <input id="name" type="text" name="name" required placeholder="John Doe"
                                        value="{{ old('name') }}"
                                        class="w-full bg-slate-800/50 border border-slate-700 text-white px-5 py-4 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-slate-600">
                                    @error('name')
                                    <p class="text-red-400 text-xs mt-2 ml-2">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-slate-400 mb-2 ml-2">Your
                                        Email</label>
                                    <input id="email" type="email" name="email" required placeholder="john@example.com"
                                        value="{{ old('email') }}"
                                        class="w-full bg-slate-800/50 border border-slate-700 text-white px-5 py-4 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-slate-600">
                                    @error('email')
                                    <p class="text-red-400 text-xs mt-2 ml-2">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-medium text-slate-400 mb-2 ml-2">Message
                                    / Project Idea</label>
                                <textarea id="message" name="message" required rows="4"
                                    placeholder="Tell me a little about the project you have in mind..."
                                    class="w-full bg-slate-800/50 border border-slate-700 text-white px-5 py-4 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-slate-600 resize-none">{{ old('message') }}</textarea>
                                @error('message')
                                <p class="text-red-400 text-xs mt-2 ml-2">{{ $message }}</p>@enderror
                            </div>
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold text-lg px-8 py-4 rounded-2xl hover:shadow-[0_0_20px_rgba(59,130,246,0.5)] transition-all duration-300 transform hover:-translate-y-1">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="max-w-[1600px] mx-auto px-8 lg:px-20 mt-32 relative z-10 border-t border-slate-800/50 pt-8 pb-8 flex flex-col md:flex-row justify-between items-center gap-4 text-center md:text-left">
            <div class="text-slate-500 text-sm font-medium">
                &copy; {{ date('Y') }} <span class="text-slate-300 font-bold">Surya Andika</span>. All rights reserved.
            </div>

            <div class="flex space-x-5">
                <a href="https://github.com/SuryaAndika3005" target="_blank" rel="noopener"
                    class="text-slate-500 hover:text-slate-900 transition-colors" aria-label="GitHub profile">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M12 0C5.373 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.6.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.605-2.665-.303-5.467-1.332-5.467-5.93 0-1.31.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.5 11.5 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.61-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
                    </svg>
                </a>
                <a href="https://linkedin.com/in/surya-andika" target="_blank" rel="noopener"
                    class="text-slate-500 hover:text-blue-400 transition-colors" aria-label="LinkedIn profile">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                    </svg>
                </a>
                <a href="https://instagram.com/surdik_28" target="_blank" rel="noopener"
                    class="text-slate-500 hover:text-pink-500 transition-colors" aria-label="Instagram profile">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M12 7a5 5 0 100 10 5 5 0 000-10zm0 8a3 3 0 110-6 3 3 0 010 6zm5.25-8.25a1.25 1.25 0 112.5 0 1.25 1.25 0 01-2.5 0zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 2.16c3.203 0 3.583.012 4.849.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.849.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069z" />
                    </svg>
                </a>
            </div>
            <div class="text-slate-500 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Based in Padang, Indonesia
            </div>
        </div>
    </section>

    @push('scripts')
        @vite('resources/js/project-accordion.js')
    @endpush

</x-layout>