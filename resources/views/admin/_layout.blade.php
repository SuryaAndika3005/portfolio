@php
    // Included at the top of every admin/* view. Not a Blade component —
    // this project doesn't use the components/ folder for full-page shells,
    // only for smaller reusable pieces (see resources/views/components/admin).
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Admin' }} | Admin</title>
    {{-- See resources/views/components/layout.blade.php's comment -- same
         missing-@fonts bug, same fix, admin.css references the same family. --}}
    @fonts
    @vite(['resources/css/admin.css', 'resources/js/admin.js', 'resources/js/admin/project-assistant.js', 'resources/js/admin/gallery-reorder.js'])
</head>
<body class="admin-body font-sans antialiased">

    <div class="admin-shell">
        <div id="admin-sidebar-backdrop" class="admin-sidebar-backdrop"></div>

        <aside class="admin-sidebar">
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="admin-brand">SURYA<span>ANDIKA</span></a>
                <button type="button" id="admin-sidebar-close" class="admin-menu-btn lg:hidden" aria-label="Close menu">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="admin-nav" aria-label="Admin navigation">
                <p class="admin-nav-group-label">Content</p>
                <x-admin.nav-item :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    <x-slot:icon>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </x-slot:icon>
                    Overview
                </x-admin.nav-item>
                <x-admin.nav-item :href="route('admin.projects.index')" :active="request()->routeIs('admin.projects.*')">
                    <x-slot:icon>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h6a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                    </x-slot:icon>
                    Projects
                </x-admin.nav-item>
                <x-admin.nav-item :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                    <x-slot:icon>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M4 4h16v16H4V4zm3 8h10M7 16h6"/></svg>
                    </x-slot:icon>
                    Categories
                </x-admin.nav-item>
                <x-admin.nav-item :href="route('admin.experiences.index')" :active="request()->routeIs('admin.experiences.*')">
                    <x-slot:icon>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6V4a2 2 0 012-2h2a2 2 0 012 2v2m-9 0h14a1 1 0 011 1v11a2 2 0 01-2 2H6a2 2 0 01-2-2V7a1 1 0 011-1z"/></svg>
                    </x-slot:icon>
                    Experience
                </x-admin.nav-item>
            </nav>

            <div class="admin-sidebar-footer">
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="admin-nav-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>View Portfolio ↗</span>
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="admin-nav-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="admin-workspace">
            <header class="admin-topbar">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" id="admin-menu-btn" class="admin-menu-btn" aria-label="Open menu" aria-expanded="false">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <p class="admin-topbar-title truncate">{{ $title ?? 'Admin' }}</p>
                </div>
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn-text btn--compact shrink-0">View Portfolio ↗</a>
            </header>

            <main class="admin-content">
                <x-admin.flash />
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Shared delete-confirmation dialog — see resources/js/admin.js. One
         instance serves every "Delete" form on the page; its action is
         rewritten per-row before it opens, so no per-project markup clone
         is needed. --}}
    <dialog id="admin-delete-dialog" class="admin-dialog">
        <div class="admin-dialog-body">
            <p class="admin-dialog-title">Confirm delete</p>
            <p class="admin-dialog-message">
                <strong id="admin-delete-dialog-title"></strong> will be permanently deleted. This cannot be undone.
            </p>
            <form id="admin-delete-dialog-form" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="admin-dialog-actions">
                    <button type="button" class="btn btn-secondary btn--compact" onclick="document.getElementById('admin-delete-dialog').close()">Cancel</button>
                    <button type="submit" class="btn btn-destructive btn--compact">Delete</button>
                </div>
            </form>
        </div>
    </dialog>

</body>
</html>
