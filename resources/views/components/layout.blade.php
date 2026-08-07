@props(['title' => 'Surya Andika | Portfolio', 'showBack' => false, 'metaDescription' => null])
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Portfolio of Surya Andika: UI/UX design, graphic design, and web development.' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <div id="page-loading-bar" aria-hidden="true"></div>

    <x-nav :show-back="$showBack ?? false" />

    <main>
        {{ $slot }}
    </main>

    <x-footer />

    @stack('scripts')
</body>
</html>
