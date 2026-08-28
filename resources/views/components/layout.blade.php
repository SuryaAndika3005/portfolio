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
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
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
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $resolvedDescription }}">
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

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme.js', 'resources/js/preferences-fab.js'])
    @stack('styles')
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
