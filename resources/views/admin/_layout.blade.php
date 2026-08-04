@php
    // Included at the top of every admin/projects/* view.
    // Not a Blade component — this project doesn't use the components/ folder.
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <nav class="border-b border-slate-200 bg-white">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('admin.projects.index') }}" class="font-black text-slate-900">Portfolio Admin</a>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('admin.projects.create') }}" class="font-semibold text-blue-600 hover:text-blue-700">+ New Project</a>
                <a href="{{ route('home') }}" target="_blank" class="text-slate-400 hover:text-slate-600">View site</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-500">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-6 py-10">
        @if (session('success'))
            <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
