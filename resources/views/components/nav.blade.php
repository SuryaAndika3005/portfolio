@props(['showBack' => false])

<nav id="main-nav" class="fixed top-0 w-full z-50 bg-white/90 backdrop-blur-lg border-b border-slate-100 transition-all duration-300 py-5">
    <div class="max-w-[1600px] mx-auto px-6 md:px-12 lg:px-20 flex justify-between items-center">

        @if($showBack)
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-slate-600 hover:text-blue-600 transition-all group">
                <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Home
            </a>
            <div class="text-2xl font-black tracking-tighter text-slate-900">
                SURYA<span class="text-blue-600">ANDIKA</span>
            </div>
        @else
            <a href="{{ route('home') }}" class="text-2xl font-black tracking-tighter text-slate-900 group">
                SURYA<span class="text-blue-600 group-hover:text-slate-900 transition-colors">ANDIKA</span>
            </a>

            <div class="hidden md:flex items-center gap-1 bg-white/50 backdrop-blur-md border border-white/20 p-1.5 rounded-full shadow-sm">
                <a href="{{ route('home') }}" class="px-6 py-2.5 rounded-full text-sm font-bold text-slate-600 hover:text-blue-600 transition-all">Home</a>
                <a href="{{ route('home') }}#about" class="px-6 py-2.5 rounded-full text-sm font-bold text-slate-600 hover:text-blue-600 transition-all">About</a>
                <a href="{{ route('home') }}#projects" class="px-6 py-2.5 rounded-full text-sm font-bold text-slate-600 hover:text-blue-600 transition-all">Projects</a>
                <a href="{{ route('home') }}#skills" class="px-6 py-2.5 rounded-full text-sm font-bold text-slate-600 hover:text-blue-600 transition-all">Skills</a>
                <a href="{{ route('home') }}#contact" class="ml-4 px-6 py-2.5 bg-slate-900 text-white rounded-full text-sm font-bold hover:bg-blue-600 hover:shadow-lg hover:shadow-blue-500/30 transition-all">Let's Talk</a>
            </div>

            <button id="menu-btn" type="button" class="md:hidden p-3 rounded-2xl bg-white shadow-md text-slate-900" aria-expanded="false" aria-controls="mobile-menu" aria-label="Toggle navigation menu">
                <svg id="menu-icon" class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        @endif
    </div>

    @unless($showBack)
        <div id="mobile-menu" class="hidden fixed inset-0 top-[72px] bg-white/95 backdrop-blur-xl z-[90] p-8 flex-col gap-6 items-center text-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-slate-800">Home</a>
            <a href="{{ route('home') }}#about" class="text-2xl font-bold text-slate-800">About</a>
            <a href="{{ route('home') }}#projects" class="text-2xl font-bold text-slate-800">Projects</a>
            <a href="{{ route('home') }}#skills" class="text-2xl font-bold text-slate-800">Skills</a>
            <a href="{{ route('home') }}#contact" class="w-full py-4 bg-blue-600 text-white rounded-2xl font-bold shadow-xl shadow-blue-600/20">Contact Me</a>
        </div>
    @endunless
</nav>
