@props([
    'title' => 'Surya Andika | Portfolio',
    'showBack' => false,
    'metaDescription' => null,
    'hideFooter' => false,
    'archiveChapters' => null,
    'ogImage' => null,
    'ogType' => 'website',
])
@php
    $resolvedDescription = $metaDescription ?? 'Portfolio of Surya Andika: UI/UX design, graphic design, and web development.';
    $resolvedOgImage = $ogImage ?? asset('storage/projects/dika.webp');
    $gaMeasurementId = config('services.google_analytics.measurement_id');
    $gaEnabled = app()->environment('production') && filled($gaMeasurementId);
@endphp
<!DOCTYPE html>
{{-- data-locale (BFCache Preference State Sync fix): the locale this exact
     HTML was rendered with, read by resources/js/locale-sync.js to detect a
     stale BFCache-restored snapshot after a locale switch elsewhere. Not a
     styling/behavior hook -- lang= above still does that job; this is a
     dedicated sync value so a future change to lang='s format never
     silently breaks the sync check. --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-locale="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Theme applied before first paint, deliberately inline and first in
         <head> -- a deferred/external script here would let the page paint
         Light for a frame before flipping to Dark. Mirrors theme.js's own
         "system" logic exactly; kept tiny on purpose (see Section 27 of the
         batch brief). First-time visitors (no stored preference at all)
         default to Light, not OS/system -- only an explicit saved "system"
         choice follows prefers-color-scheme. Distinguishing "no preference
         saved yet" from "explicitly chose System" is the whole fix here. --}}
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('theme');
                var dark = stored === 'dark' || (stored === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', dark);
            } catch (e) {}
        })();
    </script>
    @if ($gaEnabled)
        {{-- Google tag (gtag.js) -- production-only, gated on
             GOOGLE_ANALYTICS_ID via config/services.php so local/dev
             requests never reach GA4. Kept to a single tag per page. --}}
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaMeasurementId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', {!! json_encode($gaMeasurementId) !!});
        </script>
    @endif
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $resolvedDescription }}">
    {{-- Every public page is indexable; this is stated explicitly (rather
         than left to the crawler default) so an accidental noindex
         regression is a one-line diff to spot, and so this tag is the
         opposite of admin/_layout.blade.php's noindex, nofollow. --}}
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">

    <meta property="og:site_name" content="Surya Andika">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $resolvedDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $resolvedOgImage }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $resolvedDescription }}">
    <meta name="twitter:image" content="{{ $resolvedOgImage }}">

    {{-- @vite BEFORE @fonts (Mobile LCP Final Optimization pass): the LCP
         element on the homepage is the hero H1 text, which paints only once
         app.css (render-blocking, no fallback) arrives -- but @fonts emits
         its own <link rel="preload" as="font"> tags, and the preload
         scanner requests whatever it meets first in the HTML. With @fonts
         first, 3 font files were being discovered (and competing for the
         same limited early connections/bandwidth) before app.css even
         entered the queue, on every load -- pure request-ordering, no
         change to what's fetched. Font-face text always paints via
         font-display: swap regardless of arrival order (confirmed via
         Lighthouse's font-display-insight: 0ms available savings), so
         there's no correctness reason for fonts to go first; only the
         genuinely paint-blocking resource (app.css) benefits from being
         the earliest. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme.js', 'resources/js/preferences-fab.js', 'resources/js/locale-sync.js'])
    {{-- Self-hosted Instrument Sans (weights 400/500/600 via the Vite fonts
         plugin, configured in vite.config.js) -- this directive is what
         actually injects its @font-face rules + preload links; app.css only
         references the family name, it doesn't declare the font itself.
         Without this the site silently falls back to the browser's default
         sans-serif on every page (found during the final QA pass -- @fonts
         existed only in the unused stock welcome.blade.php, never here). --}}
    @fonts
    @stack('styles')
    {{-- Structured data: each page pushes its own JSON-LD payload(s) via
         @push('json-ld') + <x-json-ld :data="..."> (see that component) --
         nothing is emitted here for pages that push nothing. --}}
    @stack('json-ld')
</head>
<body class="bg-canvas text-slate-800 dark:text-slate-100 font-sans antialiased">

    <div id="page-loading-bar" aria-hidden="true"></div>

    <x-nav :show-back="$showBack ?? false" :archive-chapters="$archiveChapters ?? null" />
    <x-preferences-fab />

    <main>
        {{ $slot }}
    </main>

    @unless ($hideFooter)
        <x-footer />
    @endunless

    @stack('scripts')
</body>
</html>
