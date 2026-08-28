@props(['showBack' => false, 'archiveChapters' => null])

<nav id="main-nav" @if($archiveChapters || $showBack) data-compact-topbar @endif
    class="fixed top-0 w-full z-50 bg-white/90 dark:bg-slate-950/90 backdrop-blur-lg border-b border-slate-100 dark:border-white/10 transition-all duration-300 {{ $archiveChapters || $showBack ? 'py-3.5' : 'py-5' }}">
    <div class="max-w-[1600px] mx-auto px-6 md:px-12 lg:px-20 flex justify-between items-center gap-4">

        @if($archiveChapters)
            {{-- Archive topbar: brand + chapter navigation + back-home
                 coexist in this ONE sticky bar -- there is no second sticky
                 nav on /projects. Chapter links are always present (not
                 faded in after scroll) but stay visually quiet until
                 archive-nav.js's scrollspy marks one .is-active, which
                 already only happens once the visitor has scrolled into
                 that chapter -- the "quiet at first, useful once scrolled"
                 behavior falls out of the existing scrollspy for free,
                 with no extra visibility-toggle logic needed. Hidden below
                 md: full category names/counts still exist in the page's
                 own static index and chapter headings. --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-sm text-slate-600 dark:text-slate-300 hover:text-primary transition-colors duration-200 group shrink-0">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="hidden sm:inline">{{ __('Back Home') }}</span>
            </a>

            <div class="hidden md:flex items-center gap-6 lg:gap-8">
                @foreach ($archiveChapters as $chapter)
                    <a href="#{{ $chapter['slug'] }}" data-chapter-link data-target="{{ $chapter['slug'] }}"
                        aria-label="{{ __('Jump to :section', ['section' => $chapter['label']]) }}"
                        class="archive-chapter-link text-sm font-bold uppercase tracking-wide outline-none focus-visible:ring-2 focus-visible:ring-primary/40 rounded-md py-1.5">
                        <span class="tabular-nums">{{ $chapter['index'] }}</span> {{ $chapter['shortLabel'] }}
                    </a>
                @endforeach
            </div>

            <a href="{{ route('home') }}" class="text-xl sm:text-2xl font-black tracking-tighter text-slate-900 dark:text-white group shrink-0">
                SURYA<span class="text-primary group-hover:text-slate-900 dark:group-hover:text-white transition-colors duration-200">ANDIKA</span>
            </a>
        @elseif($showBack)
            {{-- Project Detail topbar: compact -- back to the archive on the
                 left, brand on the right. No full nav links re-appear here;
                 the archive is one click away via this link, and the
                 homepage via the brand. --}}
            <a href="{{ route('portfolio.projects') }}" class="flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-slate-600 dark:text-slate-300 hover:text-primary transition-colors duration-200 group">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Projects') }}
            </a>
            <a href="{{ route('home') }}" class="text-xl sm:text-2xl font-black tracking-tighter text-slate-900 dark:text-white group">
                SURYA<span class="text-primary group-hover:text-slate-900 dark:group-hover:text-white transition-colors duration-200">ANDIKA</span>
            </a>
        @else
            <a href="{{ route('home') }}" class="text-2xl font-black tracking-tighter text-slate-900 dark:text-white group">
                SURYA<span class="text-primary group-hover:text-slate-900 dark:group-hover:text-white transition-colors duration-200">ANDIKA</span>
            </a>

            <div class="hidden md:flex items-center gap-1 bg-white/50 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 pl-6 pr-1.5 py-1.5 rounded-[var(--radius-pill)] shadow-sm">
                <a href="{{ route('home') }}" data-nav-link data-target="top" class="nav-link px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-primary focus-visible:text-primary">{{ __('Home') }}</a>
                <a href="{{ route('home') }}#about" data-nav-link data-target="about" class="nav-link px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-primary focus-visible:text-primary">{{ __('About') }}</a>
                <a href="{{ route('home') }}#projects" data-nav-link data-target="projects" class="nav-link px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-primary focus-visible:text-primary">{{ __('Projects') }}</a>
                <a href="{{ route('home') }}#skills" data-nav-link data-target="skills" class="nav-link px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-primary focus-visible:text-primary">{{ __('Skills') }}</a>
                <a href="{{ route('home') }}#contact" class="btn btn-primary ml-4 !py-2 !px-5 text-sm">{{ __("Let's Talk") }}</a>
            </div>

            <button id="menu-btn" type="button" class="md:hidden p-3 rounded-2xl bg-white dark:bg-slate-900 shadow-md text-slate-900 dark:text-white" aria-expanded="false" aria-controls="mobile-menu" aria-label="{{ __('Toggle navigation menu') }}">
                <svg id="menu-icon" class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        @endif
    </div>

    @unless($showBack || $archiveChapters)
        {{-- Solid dropdown panel, not a translucent full-screen overlay --
             absolutely positioned against #main-nav itself (fixed, so it's
             a valid containing block), sitting directly under the topbar's
             own bottom edge rather than floating text over Hero content.
             bg-surface/text-ink/border-border-light are the same semantic
             tokens used elsewhere (Footer, Contact form, etc.) -- opaque
             in both themes by definition, no alpha channel, no blur. --}}
        <div id="mobile-menu" class="hidden absolute top-full left-0 w-full md:hidden flex-col bg-surface border-b border-border-light shadow-lg px-6 py-5">
            <a href="{{ route('home') }}" data-nav-link data-target="top" class="block w-full text-lg font-bold text-ink hover:text-primary transition-colors duration-200 py-3">{{ __('Home') }}</a>
            <a href="{{ route('home') }}#about" data-nav-link data-target="about" class="block w-full text-lg font-bold text-ink hover:text-primary transition-colors duration-200 py-3">{{ __('About') }}</a>
            <a href="{{ route('home') }}#projects" data-nav-link data-target="projects" class="block w-full text-lg font-bold text-ink hover:text-primary transition-colors duration-200 py-3">{{ __('Projects') }}</a>
            <a href="{{ route('home') }}#skills" data-nav-link data-target="skills" class="block w-full text-lg font-bold text-ink hover:text-primary transition-colors duration-200 py-3">{{ __('Skills') }}</a>
            <a href="{{ route('home') }}#contact" class="btn btn-primary w-full justify-center mt-4">{{ __("Let's Talk") }}</a>
        </div>
    @endunless
</nav>
