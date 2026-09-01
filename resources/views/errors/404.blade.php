{{--
    Known limitation, documented rather than silently worked around: a
    genuinely unmatched route never passes through the 'web' middleware
    group (confirmed by testing -- session() is null and gets a fresh id
    on every request here), so the per-route SetLocale middleware (see its
    own class comment for why it's deliberately not global) never runs and
    this page cannot honor a visitor's chosen Indonesian locale. Fixing
    that properly would mean making session/locale handling global, which
    SetLocale's own comment explicitly warns against (it would leak the
    public locale into Admin). Left as English-only pending a deliberate
    architecture decision -- not something to silently change here.
--}}
<x-layout :title="__('Page Not Found') . ' | Surya Andika'"
    :meta-description="__('This page could not be found.')"
    :noindex="true">

    <section class="min-h-[70vh] flex items-center max-w-[1600px] mx-auto px-8 lg:px-20 py-24">
        <div class="max-w-xl">
            <p class="text-eyebrow font-bold uppercase tracking-[0.25em] text-primary-fg mb-6">404</p>
            <h1 class="text-heading lg:text-display font-extrabold tracking-tight text-slate-900 dark:text-white mb-6">
                {{ __("This page doesn't exist.") }}
            </h1>
            <p class="text-body text-slate-500 dark:text-slate-400 mb-10">
                {{ __("The page you're looking for may have moved or never existed. Here are a couple of places to go instead.") }}
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Back Home') }}</a>
                <a href="{{ route('portfolio.projects') }}" class="btn btn-secondary">{{ __('View All Projects') }}</a>
            </div>
        </div>
    </section>

</x-layout>
