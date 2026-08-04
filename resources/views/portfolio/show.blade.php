<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->title }} | Surya Andika</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .scrollbar-thin::-webkit-scrollbar { width: 6px; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
        #imageModal { transition: opacity 0.3s ease-out; }
        #imageModal.hidden { opacity: 0; pointer-events: none; display: none; }
        #imageModal.flex { display: flex; opacity: 1; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <nav class="fixed top-0 w-full z-50 bg-white/70 backdrop-blur-lg border-b border-slate-100">
        <div class="max-w-[1600px] mx-auto px-8 lg:px-20 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-slate-800 hover:text-blue-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Home
            </a>
            <div class="text-sm font-bold tracking-widest uppercase text-slate-400">{{ $project->category->name ?? 'Uncategorized' }}</div>
        </div>
    </nav>

    <header class="pt-32 pb-16 px-8 lg:px-20 max-w-[1600px] mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-widest">
                {{ $project->category->name ?? 'Uncategorized' }}
            </span>
            @if ($project->is_highlighted)
                <span class="bg-amber-50 text-amber-600 text-xs font-bold px-3 py-1.5 rounded-full border border-amber-200">
                    ✦ Highlighted Work
                </span>
            @endif
        </div>

        <h1 class="text-5xl lg:text-7xl font-black text-slate-900 mb-10 leading-tight">
            {{ $project->title }}
        </h1>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @php
                $meta = ['Role' => $project->role, 'Client' => $project->client, 'Year' => $project->year, 'Tools' => $project->tools];
            @endphp
            @foreach ($meta as $label => $value)
                <div class="bg-white rounded-2xl border border-slate-200 px-5 py-4 shadow-sm">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ $label }}</p>
                    <p class="text-base font-bold text-slate-800 leading-snug">{{ $value ?: '—' }}</p>
                </div>
            @endforeach
        </div>
    </header>

    <main class="max-w-[1600px] mx-auto px-8 lg:px-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 py-16 border-t border-slate-100">

            <div class="lg:col-span-4">
                @if ($project->problem || $project->process || $project->result)
                    <div class="space-y-10">
                        @if ($project->problem)
                            <div>
                                <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-3">The Problem</p>
                                <p class="text-slate-600 leading-relaxed text-lg">{{ $project->problem }}</p>
                            </div>
                        @endif
                        @if ($project->process)
                            <div>
                                <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-3">The Process</p>
                                <p class="text-slate-600 leading-relaxed text-lg">{{ $project->process }}</p>
                            </div>
                        @endif
                        @if ($project->result)
                            <div>
                                <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-3">The Result</p>
                                <p class="text-slate-600 leading-relaxed text-lg">{{ $project->result }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <h3 class="text-2xl font-bold text-slate-900 mb-6">Project Overview</h3>
                    @if ($project->description)
                        <p class="text-slate-600 leading-relaxed text-lg">{{ $project->description }}</p>
                    @else
                        <p class="text-slate-400 leading-relaxed text-lg italic">No description added for this project yet.</p>
                    @endif
                @endif

                <div class="mt-10 pt-10 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Like what you see?</p>
                    <a href="{{ route('home') }}#contact" class="group inline-flex items-center gap-2 bg-slate-900 text-white font-bold px-6 py-3.5 rounded-full hover:bg-slate-800 transition-colors">
                        Discuss a similar project
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-12">
                <h3 class="text-2xl font-bold text-slate-900">Visual Showcase</h3>

                <div class="group relative rounded-[3rem] overflow-hidden shadow-2xl border border-slate-200 bg-white cursor-zoom-in" onclick="openModal('{{ asset('storage/' . $project->image_path) }}')">
                    <div class="max-h-[650px] overflow-y-auto scrollbar-thin">
                        <img src="{{ asset('storage/' . $project->image_path) }}" class="w-full h-auto object-top" alt="{{ $project->title }} main showcase">
                    </div>
                    <div class="absolute inset-0 bg-blue-600/0 group-hover:bg-blue-600/10 transition-all duration-300 flex items-center justify-center">
                        <span class="bg-white px-6 py-3 rounded-full font-bold shadow-xl opacity-0 group-hover:opacity-100 transition-opacity">Click for Fullscreen</span>
                    </div>
                </div>

                @if ($project->galleryImages()->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach ($project->galleryImages() as $image)
                            <div class="bg-white p-4 rounded-[2.5rem] shadow-xl border border-slate-100/50 group cursor-zoom-in" onclick="openModal('{{ asset($image) }}')">
                                <div class="aspect-[4/5] rounded-[1.8rem] overflow-hidden border border-slate-50 bg-slate-100">
                                    <img src="{{ asset($image) }}" loading="lazy" class="w-full h-auto object-cover object-top transition duration-700 group-hover:scale-105" alt="{{ $project->title }} detail view {{ $loop->iteration }}">
                                </div>
                                <div class="mt-4 px-4 pb-2">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Project Detail: {{ $project->title }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>

    <div id="imageModal" class="hidden fixed inset-0 z-[100] bg-slate-950/95 backdrop-blur-sm items-center justify-center p-4 md:p-10" onclick="closeModal()">
        <button class="absolute top-8 right-8 text-white hover:text-blue-400 transition transform hover:rotate-90 duration-300">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <div class="max-w-full max-h-full overflow-auto rounded-2xl scrollbar-hide" onclick="event.stopPropagation()">
            <img id="modalImg" src="" class="w-full h-auto rounded-lg">
        </div>
    </div>

    <script>
        function openModal(src) {
            const modal = document.getElementById('imageModal');
            document.getElementById('modalImg').src = src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
    </script>

    <footer class="bg-slate-900 py-20 text-center text-white mt-20">
        <h4 class="text-3xl font-bold mb-6">Need a design like this?</h4>
        <a href="{{ route('home') }}#contact" class="inline-block bg-blue-600 px-10 py-4 rounded-full font-bold hover:bg-blue-500 transition shadow-lg shadow-blue-600/20">Contact Surya</a>
    </footer>
</body>
</html>
