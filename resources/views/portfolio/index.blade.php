<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surya Andika | Portfolio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-pattern {
            background-image: url('path/to/subtle-gonjong-pattern.svg'); /* Ganti dengan pattern SVG abstrak nanti */
            background-size: cover;
            background-blend-mode: overlay;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased bg-pattern">

<nav class="fixed top-0 w-full z-50 bg-white/70 backdrop-blur-lg border-b border-slate-100 transform-gpu">    <div class="max-w-[1600px] mx-auto px-6 md:px-12 lg:px-20 flex justify-between items-center">
        <a href="#" class="text-2xl font-black tracking-tighter text-slate-900 group">
            SURYA<span class="text-blue-600 group-hover:text-slate-900 transition-colors">ANDIKA</span>
        </a>

        <div class="hidden md:flex items-center gap-1 bg-white/50 backdrop-blur-md border border-white/20 p-1.5 rounded-full shadow-sm">
            <a href="#" class="px-6 py-2.5 rounded-full text-sm font-bold text-slate-600 hover:text-blue-600 transition-all">Home</a>
            <a href="#projects" class="px-6 py-2.5 rounded-full text-sm font-bold text-slate-600 hover:text-blue-600 transition-all">Projects</a>
            <a href="#skills" class="px-6 py-2.5 rounded-full text-sm font-bold text-slate-600 hover:text-blue-600 transition-all">Skiils</a>
            <a href="#contact" class="ml-4 px-6 py-2.5 bg-slate-900 text-white rounded-full text-sm font-bold hover:bg-blue-600 hover:shadow-lg hover:shadow-blue-500/30 transition-all">Let's Talk</a>
        </div>

        <button id="menu-btn" class="md:hidden p-3 rounded-2xl bg-white shadow-md text-slate-900">
            <svg id="menu-icon" class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
    </div>

    <div id="mobile-menu" class="hidden fixed inset-0 top-[88px] bg-white/95 backdrop-blur-xl z-[90] p-8 flex-col gap-6 items-center text-center">
        <a href="#home" onclick="toggleMenu()" class="text-2xl font-bold text-slate-800">Home</a>
        <a href="#projects" onclick="toggleMenu()" class="text-2xl font-bold text-slate-800">Projects</a>
        <a href="#about" onclick="toggleMenu()" class="text-2xl font-bold text-slate-800">About</a>
        <a href="#contact" onclick="toggleMenu()" class="w-full py-4 bg-blue-600 text-white rounded-2xl font-bold shadow-xl shadow-blue-600/20">Contact Me</a>
    </div>
</nav>

<script>
    const nav = document.getElementById('main-nav');
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');

    // 1. Efek Scroll Navbar
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            nav.classList.add('bg-white/80', 'backdrop-blur-lg', 'py-4', 'shadow-sm', 'border-b', 'border-slate-100');
            nav.classList.remove('py-6');
        } else {
            nav.classList.remove('bg-white/80', 'backdrop-blur-lg', 'py-4', 'shadow-sm', 'border-b', 'border-slate-100');
            nav.classList.add('py-6');
        }
    });

    // 2. Logika Mobile Menu
    function toggleMenu() {
        mobileMenu.classList.toggle('hidden');
        mobileMenu.classList.toggle('flex');
        
        // Animasi icon (putar sedikit saat diklik)
        menuIcon.classList.toggle('rotate-90');
        
        // Stop scroll body saat menu buka
        if (!mobileMenu.classList.contains('hidden')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'auto';
        }
    }

    menuBtn.addEventListener('click', toggleMenu);
</script>

    <header class="relative max-w-[1600px] mx-auto px-8 lg:px-20 pt-32 pb-24 flex flex-col md:flex-row items-center justify-between min-h-[85vh] overflow-hidden">

        <div class="absolute top-20 left-10 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-[120px] opacity-30 animate-pulse" aria-hidden="true"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-cyan-300 rounded-full mix-blend-multiply filter blur-[120px] opacity-30 animate-pulse" style="animation-delay: 2s;" aria-hidden="true"></div>

        <div class="md:w-1/2 relative z-10 pr-0 lg:pr-10">
            <div class="inline-flex items-center space-x-3 bg-white/60 backdrop-blur-sm px-5 py-2.5 rounded-full mb-8 border border-slate-200 shadow-sm">
                <span class="relative flex h-3.5 w-3.5">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-green-500"></span>
                </span>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-widest">Available for New Projects</span>
            </div>

            <h1 class="text-6xl lg:text-7xl font-extrabold leading-tight mb-5 text-slate-900 tracking-tight">
                Hi, I'm <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">Surya Andika.</span>
            </h1>

            <h2 class="text-2xl lg:text-3xl font-medium text-slate-600 mb-6">
                Informatics Student & Digital Architect
            </h2>

            <p class="text-slate-500 mb-12 max-w-xl text-lg lg:text-xl leading-relaxed">
                I craft bold visual identities and brand materials, backed by the ability to design and build the digital products that carry them &mdash; from concept to high-performance code.
            </p>

            <div class="flex flex-wrap gap-5 items-center">
                <a href="#projects" class="group relative px-8 py-4 bg-blue-600 text-white rounded-full font-bold overflow-hidden shadow-xl shadow-blue-200 hover:shadow-2xl hover:shadow-blue-300 transition-all duration-300 transform hover:-translate-y-1 text-lg">
                    <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-500"></div>
                    <span class="relative flex items-center gap-2">
                        See Projects
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </span>
                </a>
                <a href="{{ asset('storage/projects/CV.pdf') }}" download class="px-8 py-4 bg-white text-slate-700 rounded-full font-bold border-2 border-slate-200 hover:border-blue-600 hover:text-blue-600 transition-all duration-300 text-lg">
                    Resume
                </a>
            </div>
        </div>

        <div class="md:w-1/2 relative mt-16 md:mt-0 flex justify-center">
            <div class="relative bg-white/70 backdrop-blur-md p-6 rounded-3xl shadow-xl border border-white/60">
                <div class="flex items-center gap-4">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg" class="w-10 h-10" alt="Laravel logo">
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Web</p>
                        <p class="text-base font-bold text-slate-800">Developer</p>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-12 -left-14 bg-white/80 backdrop-blur-md p-4 rounded-full shadow-lg border border-white/60 animate-pulse" style="animation-duration: 5s;">
                <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/illustrator/illustrator-plain.svg" class="w-8 h-8" alt="Illustrator logo">
            </div>
        </div>
    </header>

    <section id="projects" class="max-w-[1600px] mx-auto px-8 lg:px-20 py-24 relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
            <div>
                <h3 class="text-4xl font-extrabold text-slate-900 mb-2">Selected Works</h3>
                <p class="text-lg text-slate-500">A curation of my finest visual works and digital explorations.</p>
            </div>
        </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 auto-rows-[320px]">
        @forelse($projects as $project)
            <a href="{{ route('portfolio.show', $project->id) }}" 
               class="{{ $loop->first ? 'lg:col-span-2' : '' }} group relative rounded-[2rem] overflow-hidden bg-slate-100 cursor-pointer shadow-sm hover:shadow-xl transition-all duration-500 border border-slate-200/50 block">
                
                <img src="{{ asset('storage/' . $project->image_path) }}" loading="lazy" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700 ease-out" alt="{{ $project->title }}">
                
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
            <div class="lg:col-span-3 py-20 text-center">No projects available at the moment.</div>
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
            <div class="text-center mb-16">
                <h3 class="text-3xl font-bold text-white mb-4">Tech &amp; Creative Stack</h3>
                <p class="text-slate-400">A seamless blend of software engineering expertise and visual design.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($skillGroups as $group)
                    <div class="bg-slate-800/50 p-8 rounded-3xl border border-slate-700 hover:border-blue-500 hover:bg-slate-800 transition duration-300 group flex flex-col justify-between shadow-2xl">
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
                                    <img src="{{ $tool['icon'] }}" class="w-7 h-7" title="{{ $tool['name'] }}" alt="{{ $tool['name'] }} logo">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="education" class="max-w-[1600px] lg:px-20 mx-auto px-6 py-20 relative z-10 -mt-10">
        <div class="relative overflow-hidden bg-white rounded-[3rem] p-10 md:p-14 shadow-2xl shadow-slate-200/50 border border-slate-100 group hover:shadow-blue-200/50 transition-shadow duration-500">
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
                <div class="flex items-start gap-6">
                    <div class="relative bg-gradient-to-br from-blue-600 to-cyan-500 text-white p-5 rounded-3xl shadow-lg transform group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                    </div>
                    <div>
                        <span class="text-sm font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500 uppercase tracking-widest">2023 - Present</span>
                        <h4 class="text-3xl font-extrabold text-slate-800 tracking-tight">Bachelor of Informatics</h4>
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
        <div class="text-center mb-16">
            <h3 class="text-4xl font-extrabold text-slate-900 mb-4">The Journey</h3>
            <p class="text-lg text-slate-500">Professional milestones, leadership roles, and creative contributions.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/30 border border-slate-100 hover:border-blue-300 transition-colors duration-300 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 rounded-bl-full -z-10 opacity-50 group-hover:scale-110 transition-transform"></div>
                <div class="flex items-center space-x-4 mb-10">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h4 class="text-xl font-extrabold text-slate-800">Professional Work</h4>
                </div>
                
                <div class="relative pl-8 border-l-2 border-slate-100 space-y-10">
                    <div class="relative group/item">
                        <div class="absolute -left-[2.1rem] top-1 w-4 h-4 bg-white rounded-full border-4 border-blue-500 group-hover/item:scale-125 group-hover/item:shadow-[0_0_15px_rgba(59,130,246,0.5)] transition-all duration-300"></div>
                        <span class="inline-block px-3 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-full mb-3">Jun 2025 - Present</span>
                        <h5 class="text-lg font-bold text-slate-800 group-hover/item:text-blue-600 transition-colors">Graphic Designer</h5>
                        <p class="text-sm text-slate-500 font-medium">523 Studio - Creative Agency</p>
                    </div>
                    <div class="relative group/item">
                        <div class="absolute -left-[2.1rem] top-1 w-4 h-4 bg-white rounded-full border-4 border-slate-300 group-hover/item:border-blue-400 transition-colors duration-300"></div>
                        <span class="inline-block px-3 py-1 bg-slate-100 text-slate-500 text-xs font-bold rounded-full mb-3">May - Jul 2024</span>
                        <h5 class="text-lg font-bold text-slate-800 group-hover/item:text-blue-600 transition-colors">Graphic Designer Intern</h5>
                        <p class="text-sm text-slate-500 font-medium">Alir Pictures - Production House</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/30 border border-slate-100 hover:border-emerald-300 transition-colors duration-300 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-100 rounded-bl-full -z-10 opacity-50 group-hover:scale-110 transition-transform"></div>
                <div class="flex items-center space-x-4 mb-10">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h4 class="text-xl font-extrabold text-slate-800">Organization</h4>
                </div>
                
                <div class="relative pl-8 border-l-2 border-slate-100 space-y-10">
                    <div class="relative group/item">
                        <div class="absolute -left-[2.1rem] top-1 w-4 h-4 bg-white rounded-full border-4 border-emerald-500 group-hover/item:scale-125 group-hover/item:shadow-[0_0_15px_rgba(16,185,129,0.5)] transition-all duration-300"></div>
                        <span class="inline-block px-3 py-1 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-full mb-3">2024 - 2025</span>
                        <h5 class="text-lg font-bold text-slate-800 group-hover/item:text-emerald-600 transition-colors">Coordinator of Media & Info</h5>
                        <p class="text-sm text-slate-500 font-medium">LIMPAKO</p>
                    </div>
                    <div class="relative group/item">
                        <div class="absolute -left-[2.1rem] top-1 w-4 h-4 bg-white rounded-full border-4 border-slate-300 group-hover/item:border-emerald-400 transition-colors duration-300"></div>
                        <span class="inline-block px-3 py-1 bg-slate-100 text-slate-500 text-xs font-bold rounded-full mb-3">2024 - 2025</span>
                        <h5 class="text-lg font-bold text-slate-800 group-hover/item:text-emerald-600 transition-colors">Coordinator of InfoMed</h5>
                        <p class="text-sm text-slate-500 font-medium">Himpunan Mahasiswa Informatika</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/30 border border-slate-100 hover:border-purple-300 transition-colors duration-300 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-purple-100 rounded-bl-full -z-10 opacity-50 group-hover:scale-110 transition-transform"></div>
                <div class="flex items-center space-x-4 mb-10">
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h4 class="text-xl font-extrabold text-slate-800">Events</h4>
                </div>
                
                <div class="relative pl-8 border-l-2 border-slate-100 space-y-8">
                    <div class="relative group/item">
                        <div class="absolute -left-[2.1rem] top-1 w-4 h-4 bg-white rounded-full border-4 border-purple-500 group-hover/item:scale-125 group-hover/item:shadow-[0_0_15px_rgba(168,85,247,0.5)] transition-all duration-300"></div>
                        <span class="inline-block px-3 py-1 bg-purple-50 text-purple-600 text-xs font-bold rounded-full mb-3">2025 - 2026</span>
                        <h5 class="text-lg font-bold text-slate-800 group-hover/item:text-purple-600 transition-colors">Pubdok Coordinator</h5>
                        <p class="text-sm text-slate-500 font-medium">KKN KAMS</p>
                    </div>
                    <div class="relative group/item">
                        <div class="absolute -left-[2.1rem] top-1 w-4 h-4 bg-white rounded-full border-4 border-slate-300 group-hover/item:border-purple-400 transition-colors duration-300"></div>
                        <span class="inline-block px-3 py-1 bg-slate-100 text-slate-500 text-xs font-bold rounded-full mb-3">Apr - Aug 2025</span>
                        <h5 class="text-lg font-bold text-slate-800 group-hover/item:text-purple-600 transition-colors">Chief Executive</h5>
                        <p class="text-sm text-slate-500 font-medium">Bakti FTI</p>
                    </div>
                    <div class="relative group/item">
                        <div class="absolute -left-[2.1rem] top-1 w-4 h-4 bg-white rounded-full border-4 border-slate-300 group-hover/item:border-purple-400 transition-colors duration-300"></div>
                        <span class="inline-block px-3 py-1 bg-slate-100 text-slate-500 text-xs font-bold rounded-full mb-3">2024 - 2025</span>
                        <h5 class="text-lg font-bold text-slate-800 group-hover/item:text-purple-600 transition-colors">Pubdok Coordinator</h5>
                        <p class="text-sm text-slate-500 font-medium">APAN 8 - LIMPAKO</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section id="contact" class="relative bg-slate-900 pt-32 pb-12 mt-20 overflow-hidden border-t border-slate-800">
        <div class="max-w-[1600px] mx-auto px-8 lg:px-20 relative z-10">
            <div class="flex flex-col lg:flex-row gap-16 lg:gap-24 items-center">

                <div class="w-full lg:w-5/12 text-center lg:text-left">
                    <h2 class="text-5xl lg:text-7xl font-extrabold text-white mb-6 tracking-tight leading-tight">
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

                <div class="w-full lg:w-7/12">
                    <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 lg:p-12 rounded-[3rem] shadow-2xl relative overflow-hidden">
                        @if(session('success'))
                            <div class="bg-green-500/10 border border-green-500/50 text-green-400 px-6 py-4 rounded-2xl mb-6 font-medium" role="status">
                                ✅ {{ session('success') }}
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
                                <label class="block text-sm font-medium text-slate-400 mb-2 ml-2">Message / Project Idea</label>
                                <textarea name="message" required rows="4" placeholder="Ceritakan sedikit tentang proyek yang ingin Anda buat..." class="w-full bg-slate-800/50 border border-slate-700 text-white px-5 py-4 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-slate-600 resize-none"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold text-lg px-8 py-4 rounded-2xl hover:shadow-[0_0_20px_rgba(59,130,246,0.5)] transition-all duration-300 transform hover:-translate-y-1">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[1600px] mx-auto px-8 lg:px-20 mt-32 relative z-10 border-t border-slate-800/50 pt-8 pb-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-slate-500 text-sm font-medium">
                &copy; {{ date('Y') }} <span class="text-slate-300 font-bold">Surya Andika</span>. All rights reserved.
            </div>
            
            <div class="flex space-x-5">
                <a href="https://linkedin.com/in/surya-andika" target="_blank" class="text-slate-500 hover:text-blue-400 transition-colors">
                    <span class="sr-only">LinkedIn</span>
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                </a>
                <a href="https://instagram.com/surdik_28" target="_blank" class="text-slate-500 hover:text-pink-500 transition-colors">
                    <span class="sr-only">Instagram</span>
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4s1.791-4 4-4 4 1.791 4 4-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>
            </div>
            <div class="text-slate-500 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Based in Padang, Indonesia
            </div>
        </div>
    </section>

    <footer class="text-center py-8 text-slate-400 text-sm border-t border-slate-200">
        <p>&copy; 2026 Surya Andika. Informatics Student & Designer.</p>
    </footer>

    <script>
    function filterProjects(slug) {
        const items = document.querySelectorAll('.project-item');
        const buttons = document.querySelectorAll('.filter-btn');

        // Ubah tampilan tombol aktif
        buttons.forEach(btn => {
            btn.classList.remove('bg-white', 'shadow-md', 'text-blue-600', 'active-filter');
            btn.classList.add('text-slate-500', 'hover:text-slate-800', 'hover:bg-slate-200/50');
            
            if(btn.getAttribute('onclick').includes(`'${slug}'`)) {
                btn.classList.add('bg-white', 'shadow-md', 'text-blue-600', 'active-filter');
                btn.classList.remove('text-slate-500', 'hover:text-slate-800', 'hover:bg-slate-200/50');
            }
        });
        items.forEach(item => {
            const itemCategory = item.getAttribute('data-category');
            if (slug === 'all' || itemCategory === slug) {
                item.style.display = 'block';
                setTimeout(() => item.style.opacity = '1', 10);
            } else {
                item.style.opacity = '0';
                setTimeout(() => item.style.display = 'none', 400);
            }
        });
        const lenis = new Lenis({
            autoRaf: true,
            smoothWheel: true,
            syncTouch: true, 
        });
    }
</script>

    </body>
    
</html>