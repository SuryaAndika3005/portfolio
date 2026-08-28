{{-- Editorial closing sign-off, not a generic multi-column SaaS footer. One
     restrained signature device: the thin hairline divider above already
     doubles as that device (no second decorative flourish layered on top).
     Renders only on pages that aren't the homepage (hideFooter is set there
     -- see layout.blade.php -- since the homepage's own dark Contact section
     is its one and only closing beat). Every color here is a semantic token,
     so it stays correct in both themes with zero dark: overrides. Language/
     theme controls are not duplicated here -- <x-nav> already carries them
     on every page this footer appears on. --}}
<footer class="border-t border-border-light bg-canvas">
    <div class="max-w-[1600px] mx-auto px-8 lg:px-20 py-14 lg:py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-start">
            <div class="lg:col-span-6">
                <a href="{{ route('home') }}" class="text-xl font-black tracking-tighter text-ink">
                    SURYA<span class="text-primary">ANDIKA</span>
                </a>
                <p class="text-small text-muted mt-3 max-w-sm leading-relaxed">
                    {{ __('Design, technology, and selected work from Surya Andika.') }}
                </p>
                <p class="text-meta font-semibold uppercase tracking-widest text-soft-muted mt-4">
                    {{ __('Graphic Designer & Informatics Student') }} &middot; {{ __('Padang, Indonesia') }}
                </p>
            </div>

            <nav aria-label="{{ __('Footer') }}" class="lg:col-span-3 flex flex-row lg:flex-col gap-x-6 gap-y-3 flex-wrap">
                <a href="{{ route('home') }}" class="text-small font-semibold text-muted hover:text-primary transition-colors duration-[var(--motion-fast)]">{{ __('Home') }}</a>
                <a href="{{ route('portfolio.projects') }}" class="text-small font-semibold text-muted hover:text-primary transition-colors duration-[var(--motion-fast)]">{{ __('Projects') }}</a>
                <a href="https://linkedin.com/in/surya-andika" target="_blank" rel="noopener" class="text-small font-semibold text-muted hover:text-primary transition-colors duration-[var(--motion-fast)]">LinkedIn</a>
                <a href="mailto:{{ config('portfolio.contact_email') }}" class="text-small font-semibold text-muted hover:text-primary transition-colors duration-[var(--motion-fast)]">{{ __('Email') }}</a>
            </nav>

            <div class="lg:col-span-3 lg:text-right">
                <p class="text-meta text-soft-muted">
                    &copy; {{ date('Y') }} {{ __('Surya Andika. All rights reserved.') }}
                </p>
            </div>
        </div>
    </div>
</footer>
