<x-layout>

    <header class="relative max-w-[1600px] mx-auto px-8 lg:px-20 pt-32 pb-24 flex flex-col lg:flex-row items-center justify-between min-h-[85vh] overflow-hidden gap-16 lg:gap-8">

        <div class="absolute top-20 left-10 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-[120px] opacity-30 animate-pulse" aria-hidden="true"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-cyan-300 rounded-full mix-blend-multiply filter blur-[120px] opacity-30 animate-pulse" style="animation-delay: 2s;" aria-hidden="true"></div>

        <div class="lg:w-1/2 relative z-10 pr-0 lg:pr-10 text-center lg:text-left">
            <div class="inline-flex items-center space-x-3 bg-white/60 backdrop-blur-sm px-5 py-2.5 rounded-full mb-8 border border-slate-200 shadow-sm">
                <span class="relative flex h-3.5 w-3.5">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-green-500"></span>
                </span>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-widest">Available for New Projects</span>
            </div>

            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold leading-tight mb-5 text-slate-900 tracking-tight">
                Hi, I'm <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Surya Andika.</span>
            </h1>

            <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-6 h-9 sm:h-10 lg:h-11 flex items-center justify-center lg:justify-start">
                <span id="role-rotator" data-roles="Graphic Designer,UI/UX Designer,Web Developer" class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500 transition-opacity duration-300">Graphic Designer</span>
            </h2>

            <p class="text-slate-500 mb-12 max-w-xl mx-auto lg:mx-0 text-base sm:text-lg lg:text-xl leading-relaxed">
                I craft bold visual identities and brand materials, backed by the ability to design and build the digital products that carry them &mdash; from concept to high-performance code.
            </p>

            <div class="flex flex-wrap gap-5 items-center justify-center lg:justify-start">
                <a href="#projects" class="group relative px-8 py-4 bg-blue-600 text-white rounded-full font-bold overflow-hidden shadow-xl shadow-blue-200 hover:shadow-2xl hover:shadow-blue-300 transition-all duration-300 transform hover:-translate-y-1 text-lg">
                    <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-500"></div>
                    <span class="relative flex items-center gap-2">
                        See Projects
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </span>
                </a>
                <a href="{{ asset('storage/projects/CV.pdf') }}" download class="px-8 py-4 bg-white text-slate-700 rounded-full font-bold border-2 border-slate-200 hover:border-blue-600 hover:text-blue-600 transition-all duration-300 text-lg">
                    Resume
                </a>
            </div>
        </div>

        <div class="lg:w-1/2 relative flex justify-center items-center min-h-[380px] sm:min-h-[440px] w-full">
            <div class="relative w-full max-w-sm h-[380px] sm:h-[440px]">
                @php $heroProjects = $projects->take(3)->values(); @endphp
                @foreach ($heroProjects as $i => $hp)
                    <a href="{{ route('portfolio.show', $hp->id) }}"
                       class="absolute rounded-[2rem] overflow-hidden shadow-2xl border-4 border-white transition-all duration-500 hover:z-30 hover:-translate-y-2 hover:rotate-0
                              {{ match ($i) {
                                    0 => 'w-44 sm:w-52 top-0 left-2 sm:left-6 -rotate-6 z-10',
                                    1 => 'w-44 sm:w-52 bottom-0 right-0 sm:right-4 rotate-6 z-10',
                                    default => 'w-40 sm:w-44 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -rotate-2 z-20',
                                } }}"
                       aria-label="View {{ $hp->title }}">
                        <img src="{{ asset('storage/' . $hp->image_path) }}" decoding="async" class="lazy-fade w-full aspect-[4/5] object-cover" alt="{{ $hp->title }}">
                    </a>
                @endforeach

                <div class="absolute -bottom-6 -left-4 sm:-left-10 bg-white/90 backdrop-blur-md p-4 sm:p-5 rounded-2xl shadow-xl border border-white/60 z-40">
                    <p class="text-2xl sm:text-3xl font-black text-slate-900">{{ $projectCount }}+</p>
                    <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest">Projects Shipped</p>
                </div>
            </div>
        </div>
    </header>

    <section id="about" class="reveal max-w-[1600px] mx-auto px-8 lg:px-20 py-24 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-center">
            <div class="lg:col-span-5 relative flex justify-center lg:justify-start">
                <div class="relative w-full max-w-xs sm:max-w-sm">
                    <div class="rounded-[2.5rem] overflow-hidden shadow-2xl border border-slate-100 aspect-[4/5]">
                        <img src="{{ asset('storage/projects/dika.webp') }}" loading="lazy" decoding="async" class="lazy-fade w-full h-full object-cover" alt="Surya Andika">
                    </div>
                    <div class="absolute -bottom-6 -right-4 sm:-right-8 bg-white p-4 sm:p-5 rounded-2xl shadow-xl border border-slate-100">
                        <p class="text-2xl sm:text-3xl font-black text-slate-900">3.57</p>
                        <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest">Current GPA</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7 text-center lg:text-left">
                <span class="text-sm font-extrabold text-blue-600 uppercase tracking-widest">About Me</span>
                <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3 mb-6">Design-first thinking, backed by code.</h3>
                <p class="text-slate-600 text-lg leading-relaxed mb-4">
                    I'm Surya, an Informatics student at Andalas University and a graphic designer at 523 Studio. My work sits at the intersection of visual design and web development &mdash; I care as much about how something looks as how it's built.
                </p>
                <p class="text-slate-600 text-lg leading-relaxed">
                    From brand identities to full product interfaces, I like owning a project end-to-end: research, design, and &mdash; when the project calls for it &mdash; the code that ships it.
                </p>
                <div class="flex flex-wrap gap-3 mt-8 justify-center lg:justify-start">
                    <span class="px-4 py-2 bg-slate-100 rounded-full text-sm font-semibold text-slate-700">Padang, Indonesia</span>
                    <span class="px-4 py-2 bg-slate-100 rounded-full text-sm font-semibold text-slate-700">Informatics @ Unand</span>
                    <span class="px-4 py-2 bg-slate-100 rounded-full text-sm font-semibold text-slate-700">523 Studio</span>
                </div>
            </div>
        </div>
    </section>

    <section id="projects" class="max-w-[1600px] mx-auto px-8 lg:px-20 py-24 relative z-10">
        <div class="reveal flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
            <div>
                <h3 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-2">Selected Works</h3>
                <p class="text-lg text-slate-500">A curation of my finest visual works and digital explorations.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 auto-rows-[320px]">
            @forelse($projects as $project)
                <a href="{{ route('portfolio.show', $project->id) }}"
                   style="--reveal-delay: {{ $loop->index * 80 }}ms"
                   class="reveal {{ $loop->first ? 'lg:col-span-2' : '' }} group relative rounded-[2rem] overflow-hidden bg-slate-100 cursor-pointer shadow-sm hover:shadow-xl transition-all duration-500 border border-slate-200/50 block">

                    <img src="{{ asset('storage/' . $project->image_path) }}" loading="lazy" decoding="async" class="lazy-fade w-full h-full object-cover transform group-hover:scale-110 transition duration-700 ease-out" alt="{{ $project->title }} — {{ $project->category->name ?? 'project' }} preview">

                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/95 via-slate-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                    <div class="absolute bottom-0 left-0 w-full p-8 translate-y-6 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500 ease-out">
                        <span class="bg-blue-600/90 backdrop-blur-sm text-white text-xs font-bold px-4 py-1.5 rounded-full mb-3 inline-block uppercase tracking-wider">
                            {{ $project->category->name ?? 'Uncategorized' }}
                        </span>
                        <h4 class="text-2xl lg:text-3xl font-bold text-white mb-2">{{ $project->title }}</h4>
                        <p class="text-sm text-slate-300 line-clamp-2">{{ $project->description }}</p>
                    </div>
                </a>
            @empty
                <div class="lg:col-span-3 py-20 text-center text-slate-400">No projects available at the moment.</div>
            @endforelse
        </div>

        <div class="mt-16 text-center">
            <a href="{{ route('portfolio.projects') }}" class="inline-flex items-center gap-3 px-10 py-4 bg-slate-900 text-white rounded-full font-bold hover:bg-blue-600 transition-all duration-300 shadow-xl hover:shadow-blue-500/30 group">
                Explore All Works
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
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
                    <div style="--reveal-delay: {{ $loop->index * 80 }}ms" class="reveal bg-slate-800/50 p-8 rounded-3xl border border-slate-700 hover:border-blue-500 hover:bg-slate-800 transition duration-300 group flex flex-col justify-between shadow-2xl">
                        <div>
                            <div class="mb-6 p-3 bg-slate-700/50 w-fit rounded-2xl group-hover:bg-blue-600 group-hover:text-white text-blue-400 transition duration-300">
                                {!! $group['icon'] !!}
                            </div>
                            <h4 class="text-xl font-bold text-white mb-3">{{ $group['title'] }}</h4>
                            <p class="text-sm text-slate-400 mb-8 leading-relaxed">{{ $group['description'] }}</p>
                        </div>
                        <div class="flex flex-wrap gap-4 items-center opacity-80 group-hover:opacity-100 transition duration-500">
                            @foreach ($group['tools'] as $tool)
                                <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300">
                                    <img src="{{ $tool['icon'] }}" loading="lazy" decoding="async" class="w-7 h-7" title="{{ $tool['name'] }}" alt="{{ $tool['name'] }} logo">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="education" class="max-w-[1600px] lg:px-20 mx-auto px-6 py-20 relative z-10 -mt-10">
        <div class="reveal relative overflow-hidden bg-white rounded-[3rem] p-8 sm:p-10 md:p-14 shadow-2xl shadow-slate-200/50 border border-slate-100 group hover:shadow-blue-200/50 transition-shadow duration-500">
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
                <div class="flex items-start gap-6">
                    <div class="relative bg-gradient-to-br from-blue-600 to-cyan-500 text-white p-5 rounded-3xl shadow-lg transform group-hover:scale-105 transition-transform duration-300 shrink-0">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                    </div>
                    <div>
                        <span class="text-sm font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500 uppercase tracking-widest">2023 - Present</span>
                        <h4 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">Bachelor of Informatics</h4>
                        <p class="text-lg text-slate-500 font-medium mt-1">Andalas University</p>
                    </div>
                </div>
                <div class="bg-slate-50 px-6 py-4 rounded-2xl border border-slate-100 shadow-sm text-center min-w-[140px] group-hover:border-blue-200 transition-colors">
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">Current GPA</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @forelse ($experiences->groupBy('category') as $category => $items)
                <div style="--reveal-delay: {{ $loop->index * 100 }}ms" class="reveal bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/30 border border-slate-100 hover:border-blue-300 transition-colors duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 rounded-bl-full -z-10 opacity-50 group-hover:scale-110 transition-transform"></div>
                    <h4 class="text-xl font-extrabold text-slate-800 mb-10">{{ $category }}</h4>
                    <div class="relative pl-8 border-l-2 border-slate-100 space-y-10">
                        @foreach ($items as $item)
                            <div class="relative group/item">
                                <div class="absolute -left-[2.1rem] top-1 w-4 h-4 bg-white rounded-full border-4 border-blue-500 group-hover/item:scale-125 group-hover/item:shadow-[0_0_15px_rgba(59,130,246,0.5)] transition-all duration-300"></div>
                                <span class="inline-block px-3 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-full mb-3">{{ $item->duration }}</span>
                                <h5 class="text-lg font-bold text-slate-800 group-hover/item:text-blue-600 transition-colors">{{ $item->role }}</h5>
                                <p class="text-sm text-slate-500 font-medium">{{ $item->company }}</p>
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
                    <h2 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold text-white mb-6 tracking-tight leading-tight">
                        Let's build <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">something great.</span>
                    </h2>
                    <p class="text-lg text-slate-400 mb-10 leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Whether it's app design, visual identity, or web development projects, I am always open to discussing new ideas.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="mailto:{{ config('portfolio.contact_email') }}" class="group flex items-center gap-4 bg-slate-800/50 border border-slate-700 p-4 rounded-2xl hover:bg-slate-800 hover:border-blue-500 transition-all duration-300 w-full sm:w-auto backdrop-blur-sm">
                            <div class="text-left">
                                <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-0.5">Email</p>
                                <p class="text-sm font-bold text-white">{{ config('portfolio.contact_email') }}</p>
                            </div>
                        </a>
                        <a href="https://wa.me/{{ config('portfolio.whatsapp_number') }}" target="_blank" rel="noopener" class="group flex items-center gap-4 bg-slate-800/50 border border-slate-700 p-4 rounded-2xl hover:bg-slate-800 hover:border-green-500 transition-all duration-300 w-full sm:w-auto backdrop-blur-sm">
                            <div class="text-left">
                                <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-0.5">WhatsApp</p>
                                <p class="text-sm font-bold text-white">{{ config('portfolio.whatsapp_display') }}</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="reveal w-full lg:w-7/12" style="--reveal-delay: 120ms">
                    <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-6 sm:p-8 lg:p-12 rounded-[3rem] shadow-2xl relative overflow-hidden">
                        @if(session('success'))
                            <div class="bg-green-500/10 border border-green-500/50 text-green-400 px-6 py-4 rounded-2xl mb-6 font-medium" role="status">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="bg-red-500/10 border border-red-500/50 text-red-400 px-6 py-4 rounded-2xl mb-6 font-medium" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('contact.send') }}" method="POST" class="space-y-6 relative z-10" novalidate>
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-slate-400 mb-2 ml-2">Your Name</label>
                                    <input id="name" type="text" name="name" required placeholder="John Doe" value="{{ old('name') }}" class="w-full bg-slate-800/50 border border-slate-700 text-white px-5 py-4 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-slate-600">
                                    @error('name')<p class="text-red-400 text-xs mt-2 ml-2">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-slate-400 mb-2 ml-2">Your Email</label>
                                    <input id="email" type="email" name="email" required placeholder="john@example.com" value="{{ old('email') }}" class="w-full bg-slate-800/50 border border-slate-700 text-white px-5 py-4 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-slate-600">
                                    @error('email')<p class="text-red-400 text-xs mt-2 ml-2">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-medium text-slate-400 mb-2 ml-2">Message / Project Idea</label>
                                <textarea id="message" name="message" required rows="4" placeholder="Tell me a little about the project you have in mind..." class="w-full bg-slate-800/50 border border-slate-700 text-white px-5 py-4 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-slate-600 resize-none">{{ old('message') }}</textarea>
                                @error('message')<p class="text-red-400 text-xs mt-2 ml-2">{{ $message }}</p>@enderror
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold text-lg px-8 py-4 rounded-2xl hover:shadow-[0_0_20px_rgba(59,130,246,0.5)] transition-all duration-300 transform hover:-translate-y-1">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[1600px] mx-auto px-8 lg:px-20 mt-32 relative z-10 border-t border-slate-800/50 pt-8 pb-8 flex flex-col md:flex-row justify-between items-center gap-4 text-center md:text-left">
            <div class="text-slate-500 text-sm font-medium">
                &copy; {{ date('Y') }} <span class="text-slate-300 font-bold">Surya Andika</span>. All rights reserved.
            </div>

            <div class="flex space-x-5">
                <a href="https://github.com/SuryaAndika3005" target="_blank" rel="noopener" class="text-slate-500 hover:text-slate-900 transition-colors" aria-label="GitHub profile">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.6.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.605-2.665-.303-5.467-1.332-5.467-5.93 0-1.31.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.5 11.5 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.61-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>
                </a>
                <a href="https://linkedin.com/in/surya-andika" target="_blank" rel="noopener" class="text-slate-500 hover:text-blue-400 transition-colors" aria-label="LinkedIn profile">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                </a>
                <a href="https://instagram.com/surdik_28" target="_blank" rel="noopener" class="text-slate-500 hover:text-pink-500 transition-colors" aria-label="Instagram profile">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0z"/></svg>
                </a>
            </div>
            <div class="text-slate-500 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Based in Padang, Indonesia
            </div>
        </div>
    </section>

</x-layout>
