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
            <a href="#skills" class="px-6 py-2.5 rounded-full text-sm font-bold text-slate-600 hover:text-blue-600 transition-all">Skills</a>
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
        
        <div class="absolute top-20 left-10 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-[120px] opacity-30 animate-pulse"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-cyan-300 rounded-full mix-blend-multiply filter blur-[120px] opacity-30 animate-pulse" style="animation-delay: 2s;"></div>

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
                Graphic Designer &mdash; UI/UX &amp; Web Developer
            </h2>
            
            <p class="text-slate-500 mb-12 max-w-xl text-lg lg:text-xl leading-relaxed">
                I craft bold visual identities and brand materials, backed by the ability to design and build the digital products that carry them &mdash; from concept to high-performance code.
            </p>
            
            <div class="flex flex-wrap gap-5 items-center">
                <a href="#projects" class="group relative px-8 py-4 bg-blue-600 text-white rounded-full font-bold overflow-hidden shadow-xl shadow-blue-200 hover:shadow-2xl hover:shadow-blue-300 transition-all duration-300 transform hover:-translate-y-1 text-lg">
                    <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-500"></div>
                    <span class="relative flex items-center gap-2">
                        View My Work
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </span>
                </a>
                <a href="#contact" class="px-8 py-4 border-2 border-slate-200 text-slate-700 rounded-full font-bold hover:border-blue-600 hover:text-blue-600 transition-all duration-300 text-lg">
                    Hire Me
                </a>
                <a href="{{ asset('storage/projects/CV.pdf') }}" download class="text-slate-400 hover:text-blue-600 font-semibold text-sm underline underline-offset-4 transition-colors">
                    Download Resume
                </a>
                <a href="https://github.com/suryaandika3005" target="_blank" class="p-4 bg-slate-800/40 border border-slate-700 text-slate-300 rounded-2xl hover:bg-slate-800 hover:text-white hover:border-slate-500 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center group shadow-md" title="View GitHub Profile">
                <span class="sr-only">GitHub</span>
                <svg class="w-6 h-6 transition-transform duration-300 group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                </svg>
                </a>
            </div>
        </div>

        <div class="md:w-5/12 mt-20 md:mt-0 relative z-10 flex justify-end">
            
            <div class="relative w-80 h-80 lg:w-[32rem] lg:h-[32rem]">
                <div class="absolute inset-0 bg-gradient-to-tr from-blue-600 to-cyan-400 rounded-[3.5rem] transform rotate-6 scale-105 shadow-2xl opacity-80"></div>
                
                <div class="absolute inset-0 bg-slate-200 rounded-[3.5rem] overflow-hidden transform -rotate-3 hover:rotate-0 transition duration-500 border-[6px] border-white shadow-inner">
                    <img src="{{ asset('storage/projects/dika.webp') }}" alt="Surya Andika" loading="lazy" class="w-full h-full object-cover filter hover:contrast-110 transition duration-500">
                </div>

                <div class="absolute -top-10 -left-10 bg-white/80 backdrop-blur-md p-5 rounded-3xl shadow-xl border border-white/60 animate-bounce" style="animation-duration: 3s;">
                    <div class="flex items-center gap-4">
                        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/figma/figma-original.svg" class="w-10 h-10" alt="UI/UX">
                        <div>
                            <p class="text-sm text-slate-500 font-medium">UI/UX</p>
                            <p class="text-base font-bold text-slate-800">Designer</p>
                        </div>
                    </div>
                </div>

                <div class="absolute -bottom-10 -right-10 bg-white/80 backdrop-blur-md p-5 rounded-3xl shadow-xl border border-white/60 animate-bounce" style="animation-duration: 4s;">
                    <div class="flex items-center gap-4">
                        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg" class="w-10 h-10" alt="Laravel">
                        <div>
                            <p class="text-sm text-slate-500 font-medium">Web</p>
                            <p class="text-base font-bold text-slate-800">Developer</p>
                        </div>
                    </div>
                </div>
                
                <div class="absolute bottom-12 -left-14 bg-white/80 backdrop-blur-md p-4 rounded-full shadow-lg border border-white/60 animate-pulse" style="animation-duration: 5s;">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/illustrator/illustrator-plain.svg" class="w-8 h-8" alt="Design">
                </div>
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
                
                @if ($project->is_highlighted)
                    <span class="absolute top-4 right-4 z-10 bg-white/90 backdrop-blur text-slate-900 text-[10px] font-bold px-2.5 py-1 rounded-full shadow">✦ Highlighted</span>
                @endif
                
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
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
        </a>
    </div>
</section>

<section id="skills" class="bg-slate-900 py-24 mt-10">
        <div class="max-w-[1600px] lg:px-20 mx-auto px-6">
            <div class="text-center mb-16">
                <h3 class="text-3xl font-bold text-white mb-4">Tech & Creative Stack</h3>
                <p class="text-slate-400">A seamless blend of software engineering expertise and visual design.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <div class="bg-slate-800/50 p-8 rounded-3xl border border-slate-700 hover:border-blue-500 hover:bg-slate-800 transition duration-300 group flex flex-col justify-between shadow-2xl">
                    <div>
                        <div class="mb-6 p-3 bg-slate-700/50 w-fit rounded-2xl group-hover:bg-blue-600 group-hover:text-white text-blue-400 transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-white mb-3">Visual Crafting</h4>
                        <p class="text-sm text-slate-400 mb-8 leading-relaxed">Crafting visual identities, brand materials, and impactful graphic designs that tell a compelling story.</p>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center opacity-80 group-hover:opacity-100 transition duration-500">
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/illustrator/illustrator-plain.svg" class="w-7 h-7" title="Adobe Illustrator" alt="Illustrator"></div>
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/photoshop/photoshop-plain.svg" class="w-7 h-7" title="Adobe Photoshop" alt="Photoshop"></div>
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/canva/canva-original.svg" class="w-7 h-7" title="Canva" alt="Canva"></div>
                    </div>
                </div>

                <div class="bg-slate-800/50 p-8 rounded-3xl border border-slate-700 hover:border-blue-500 hover:bg-slate-800 transition duration-300 group flex flex-col justify-between shadow-2xl">
                    <div>
                        <div class="mb-6 p-3 bg-slate-700/50 w-fit rounded-2xl group-hover:bg-blue-600 group-hover:text-white text-blue-400 transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 21h6l-.75-4M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-white mb-3">UI/UX Design</h4>
                        <p class="text-sm text-slate-400 mb-8 leading-relaxed">Building intuitive user flows through comprehensive wireframing, mockups, and interactive prototyping.</p>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center opacity-80 group-hover:opacity-100 transition duration-500">
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/figma/figma-original.svg" class="w-7 h-7" title="Figma" alt="Figma"></div>
                    </div>
                </div>

                <div class="bg-slate-800/50 p-8 rounded-3xl border border-slate-700 hover:border-blue-500 hover:bg-slate-800 transition duration-300 group flex flex-col justify-between shadow-2xl">
                    <div>
                        <div class="mb-6 p-3 bg-slate-700/50 w-fit rounded-2xl group-hover:bg-blue-600 group-hover:text-white text-blue-400 transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-white mb-3">Dynamic Visuals</h4>
                        <p class="text-sm text-slate-400 mb-8 leading-relaxed">Creating motion graphics, video editing, and creative documentation for impactful media publications.</p>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center opacity-80 group-hover:opacity-100 transition duration-500">
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/aftereffects/aftereffects-plain.svg" class="w-7 h-7" title="After Effects" alt="After Effects"></div>
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/premierepro/premierepro-plain.svg" class="w-7 h-7" title="Premiere Pro" alt="Premiere"></div>
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.worldvectorlogo.com/logos/capcut-1.svg" class="w-7 h-7" title="CapCut" alt="CapCut"></div>
                    </div>
                </div>

                <div class="bg-slate-800/50 p-8 rounded-3xl border border-slate-700 hover:border-blue-500 hover:bg-slate-800 transition duration-300 group flex flex-col justify-between shadow-2xl">
                    <div>
                        <div class="mb-6 p-3 bg-slate-700/50 w-fit rounded-2xl group-hover:bg-blue-600 group-hover:text-white text-blue-400 transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-white mb-3">Web Development</h4>
                        <p class="text-sm text-slate-400 mb-8 leading-relaxed">Developing solid, secure, and responsive web applications and landing pages using modern frameworks.</p>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center opacity-80 group-hover:opacity-100 transition duration-500">
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg" class="w-7 h-7" title="Laravel" alt="Laravel"></div>
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/tailwindcss/tailwindcss-original.svg" class="w-7 h-7" title="Tailwind CSS" alt="Tailwind"></div>
                    </div>
                </div>

                <div class="bg-slate-800/50 p-8 rounded-3xl border border-slate-700 hover:border-blue-500 hover:bg-slate-800 transition duration-300 group flex flex-col justify-between shadow-2xl">
                    <div>
                        <div class="mb-6 p-3 bg-slate-700/50 w-fit rounded-2xl group-hover:bg-blue-600 group-hover:text-white text-blue-400 transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-white mb-3">Mobile Apps</h4>
                        <p class="text-sm text-slate-400 mb-8 leading-relaxed">Designing and building high-performance, cross-platform mobile applications with seamless interface integration.</p>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center opacity-80 group-hover:opacity-100 transition duration-500">
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/flutter/flutter-original.svg" class="w-7 h-7" title="Flutter" alt="Flutter"></div>
                    </div>
                </div>

                <div class="bg-slate-800/50 p-8 rounded-3xl border border-slate-700 hover:border-blue-500 hover:bg-slate-800 transition duration-300 group flex flex-col justify-between shadow-2xl">
                    <div>
                        <div class="mb-6 p-3 bg-slate-700/50 w-fit rounded-2xl group-hover:bg-blue-600 group-hover:text-white text-blue-400 transition duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-white mb-3">Data & AI</h4>
                        <p class="text-sm text-slate-400 mb-8 leading-relaxed">Implementing machine learning algorithms, predictive models, computer vision, and big data analysis.</p>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center opacity-80 group-hover:opacity-100 transition duration-500">
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/python/python-original.svg" class="w-7 h-7" title="Python" alt="Python"></div>
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/tensorflow/tensorflow-original.svg" class="w-7 h-7" title="TensorFlow" alt="TensorFlow"></div>
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/scikitlearn/scikitlearn-original.svg" class="w-7 h-7" title="Scikit-learn" alt="Scikit-learn"></div>
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/opencv/opencv-original.svg" class="w-7 h-7" title="OpenCV" alt="OpenCV"></div>
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/pandas/pandas-original.svg" class="w-7 h-7" title="Pandas" alt="Pandas"></div>
                        <div class="bg-white p-2.5 rounded-2xl shadow-lg hover:scale-110 transition duration-300"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/jupyter/jupyter-original.svg" class="w-7 h-7" title="Jupyter" alt="Jupyter"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<section id="education" class="max-w-[1600px] lg:px-20 mx-auto px-6 py-20 relative z-10 -mt-10">
        <div class="relative overflow-hidden bg-white rounded-[3rem] p-10 md:p-14 shadow-2xl shadow-slate-200/50 border border-slate-100 group hover:shadow-blue-200/50 transition-shadow duration-500">
            
            <div class="absolute top-0 right-0 w-full h-full overflow-hidden pointer-events-none">
                <svg class="absolute -right-20 -top-20 w-96 h-96 text-blue-50/50 group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-700 ease-out" viewBox="0 0 200 200" fill="currentColor">
                    <path d="M 100, 0 C 150, 50 180, 100 200, 200 L 0, 200 C 20, 100 50, 50 100, 0 Z" />
                </svg>
                <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-cyan-100 rounded-full mix-blend-multiply filter blur-[80px] opacity-50 group-hover:opacity-70 transition-opacity"></div>
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
                <div class="flex items-start gap-6">
                    <div class="relative">
                        <div class="absolute inset-0 bg-blue-400 blur-md opacity-40 rounded-3xl"></div>
                        <div class="relative bg-gradient-to-br from-blue-600 to-cyan-500 text-white p-5 rounded-3xl shadow-lg transform group-hover:scale-105 transition-transform duration-300">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                        </div>
                    </div>
                    <div>
                        <div class="inline-flex items-center space-x-2 mb-2">
                            <span class="text-sm font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500 uppercase tracking-widest">2023 - Present</span>
                        </div>
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
        <div class="text-center mb-16 relative">
            <h3 class="text-4xl font-extrabold text-slate-900 mb-4">The Journey</h3>
            <p class="text-lg text-slate-500">Professional milestones, leadership roles, and creative contributions.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @php
                $groupStyles = [
                    'Professional Work' => ['ring' => 'blue', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    'Organization' => ['ring' => 'emerald', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                    'Events' => ['ring' => 'purple', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ];
            @endphp

            @forelse ($experiences as $category => $items)
                @php $style = $groupStyles[$category] ?? ['ring' => 'blue', 'icon' => $groupStyles['Professional Work']['icon']]; @endphp
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/30 border border-slate-100 hover:border-{{ $style['ring'] }}-300 transition-colors duration-300 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-{{ $style['ring'] }}-100 rounded-bl-full -z-10 opacity-50 group-hover:scale-110 transition-transform"></div>
                    <div class="flex items-center space-x-4 mb-10">
                        <div class="p-3 bg-{{ $style['ring'] }}-50 text-{{ $style['ring'] }}-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $style['icon'] }}"></path></svg>
                        </div>
                        <h4 class="text-xl font-extrabold text-slate-800">{{ $category }}</h4>
                    </div>

                    <div class="relative pl-8 border-l-2 border-slate-100 space-y-10">
                        @foreach ($items as $item)
                            <div class="relative group/item">
                                <div class="absolute -left-[2.1rem] top-1 w-4 h-4 bg-white rounded-full border-4 {{ $loop->first ? 'border-'.$style['ring'].'-500 group-hover/item:scale-125' : 'border-slate-300 group-hover/item:border-'.$style['ring'].'-400' }} transition-all duration-300"></div>
                                <span class="inline-block px-3 py-1 {{ $loop->first ? 'bg-'.$style['ring'].'-50 text-'.$style['ring'].'-600' : 'bg-slate-100 text-slate-500' }} text-xs font-bold rounded-full mb-3">{{ $item->duration }}</span>
                                <h5 class="text-lg font-bold text-slate-800 group-hover/item:text-{{ $style['ring'] }}-600 transition-colors">{{ $item->role }}</h5>
                                <p class="text-sm text-slate-500 font-medium">{{ $item->company }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="lg:col-span-3 text-center text-slate-400">No experience added yet.</p>
            @endforelse
        </div>
    </section>

<section id="contact" class="relative bg-slate-900 pt-32 pb-12 mt-20 overflow-hidden border-t border-slate-800">
        
        <div class="absolute bottom-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-blue-600 rounded-full mix-blend-screen filter blur-[130px] opacity-40 animate-pulse"></div>
            <div class="absolute top-20 right-20 w-80 h-80 bg-cyan-400 rounded-full mix-blend-screen filter blur-[130px] opacity-20 animate-pulse" style="animation-delay: 3s;"></div>
            
            <svg class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-[800px] h-[800px] text-slate-800/30 opacity-50" viewBox="0 0 200 200" fill="currentColor">
                <path d="M 100, 50 C 150, 100 180, 150 200, 200 L 0, 200 C 20, 150 50, 100 100, 50 Z" />
            </svg>
        </div>

        <div class="max-w-[1600px] mx-auto px-8 lg:px-20 relative z-10">
            <div class="flex flex-col lg:flex-row gap-16 lg:gap-24 items-center">
                
                <div class="w-full lg:w-5/12 text-center lg:text-left">
                    <div class="inline-flex items-center space-x-2 bg-slate-800/50 backdrop-blur-md px-4 py-2 rounded-full mb-6 border border-slate-700">
                        <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                        <span class="text-xs font-bold text-blue-300 uppercase tracking-widest">Let's Collaborate</span>
                    </div>
                    
                    <h2 class="text-5xl lg:text-7xl font-extrabold text-white mb-6 tracking-tight leading-tight">
                        Let's build <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">something great.</span>
                    </h2>
                    
                    <p class="text-lg text-slate-400 mb-10 leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Whether it's app design, visual identity, or web development projects, I am always open to discussing new ideas
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="mailto:surdik2811@gmail.com" class="group flex items-center gap-4 bg-slate-800/50 border border-slate-700 p-4 rounded-2xl hover:bg-slate-800 hover:border-blue-500 transition-all duration-300 w-full sm:w-auto backdrop-blur-sm">
                            <div class="p-3 bg-slate-700 rounded-xl group-hover:bg-blue-600 group-hover:text-white text-blue-400 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="text-left">
                                <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-0.5">Email</p>
                                <p class="text-sm font-bold text-white">surdik2811@gmail.com</p>
                            </div>
                        </a>
                        
                        <a href="https://wa.me/6282288706114" target="_blank" class="group flex items-center gap-4 bg-slate-800/50 border border-slate-700 p-4 rounded-2xl hover:bg-slate-800 hover:border-green-500 transition-all duration-300 w-full sm:w-auto backdrop-blur-sm">
                            <div class="p-3 bg-slate-700 rounded-xl group-hover:bg-green-500 group-hover:text-white text-green-400 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div class="text-left">
                                <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mb-0.5">WhatsApp</p>
                                <p class="text-sm font-bold text-white">+62 822 8870 6114</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="w-full lg:w-7/12">
                    <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 lg:p-12 rounded-[3rem] shadow-2xl relative overflow-hidden">
                        
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-blue-500/20 to-transparent rounded-bl-full pointer-events-none"></div>
                        @if(session('success'))
                            <div class="bg-green-500/10 border border-green-500/50 text-green-400 px-6 py-4 rounded-2xl mb-6 font-medium">
                                ✅ {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('contact.send') }}" method="POST" class="space-y-6 relative z-10">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-400 mb-2 ml-2">Your Name</label>
                                    <input type="text" name="name" required placeholder="John Doe" class="w-full bg-slate-800/50 border border-slate-700 text-white px-5 py-4 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-slate-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-400 mb-2 ml-2">Your Email</label>
                                    <input type="email" name="email" required placeholder="john@example.com" class="w-full bg-slate-800/50 border border-slate-700 text-white px-5 py-4 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-slate-600">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-2 ml-2">Message / Project Idea</label>
                                <textarea name="message" required rows="4" placeholder="Tell me a bit about the project you have in mind..." class="w-full bg-slate-800/50 border border-slate-700 text-white px-5 py-4 rounded-2xl focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-slate-600 resize-none"></textarea>
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
                &copy; 2026 <span class="text-slate-300 font-bold">Surya Andika</span>. All rights reserved.
            </div>
            
            <div class="flex space-x-5 items-center">
                <a href="https://github.com/suryaandika3005" target="_blank" class="text-slate-500 hover:text-white transition-colors">
                    <span class="sr-only">GitHub</span>
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" /></svg>
                </a>
                
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
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Based in Padang, Indonesia
            </div>
        </div>
    </section>

    <footer class="text-center py-8 text-slate-400 text-sm border-t border-slate-200">
        <p>&copy; 2026 Surya Andika. Graphic Designer &amp; Developer.</p>
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