<!DOCTYPE html>
<html lang="id">
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
        
        /* Animasi Modal */
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
                Kembali
            </a>
            <div class="text-sm font-bold tracking-widest uppercase text-slate-400">{{ $project->category->name }}</div>
        </div>
    </nav>

    <header class="pt-32 pb-16 px-8 lg:px-20 max-w-[1600px] mx-auto">
        <h1 class="text-5xl lg:text-7xl font-black text-slate-900 mb-8 leading-tight">
            {{ $project->title }}
        </h1>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-12 py-10 border-t border-b border-slate-200">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Role</p>
                <p class="text-lg font-bold text-slate-800">
                    @if($project->category->name == 'Graphic Design') Graphic Designer
                    @elseif($project->category->name == 'UI/UX Design') UI/UX Designer
                    @elseif($project->category->name == 'IT & Development') Web & App Developer
                    @else Creative Designer @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Client</p>
                <p class="text-lg font-bold text-slate-800">
                    @if(str_contains(strtolower($project->title), 'unand') || str_contains(strtolower($project->title), 'adzkia')) Akademik
                    @elseif(str_contains(strtolower($project->title), 'manufer') || str_contains(strtolower($project->title), 'league')) Sports & Event
                    @else Personal / Commercial @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Year</p>
                <p class="text-lg font-bold text-slate-800">
                    @if(str_contains(strtolower($project->title), 'informatika')) 2023
                    @elseif(str_contains(strtolower($project->title), '523')) 2024
                    @else 2025/2026 @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Tools</p>
                <p class="text-lg font-bold text-slate-800">
                    @if($project->category->name == 'Graphic Design') PS, AI
                    @elseif($project->category->name == 'UI/UX Design') Figma
                    @else Laravel, Flutter @endif
                </p>
            </div>
        </div>
    </header>

    <main class="max-w-[1600px] mx-auto px-8 lg:px-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 py-20">
            <div class="lg:col-span-4">
                <h3 class="text-2xl font-bold text-slate-900 mb-6">Overview</h3>
                <p class="text-slate-600 leading-relaxed text-lg italic italic">"{{ $project->description }}"</p>
            </div>
            
            <div class="lg:col-span-8 space-y-12">
                <h3 class="text-2xl font-bold text-slate-900 flex items-center gap-3">Visual Showcase</h3>
                
                <div class="group relative rounded-[3rem] overflow-hidden shadow-2xl border border-slate-200 bg-white cursor-zoom-in" onclick="openModal('{{ asset('storage/' . $project->image_path) }}')">
                    <div class="max-h-[650px] overflow-y-auto scrollbar-thin">
                        <img src="{{ asset('storage/' . $project->image_path) }}" class="w-full h-auto object-top" alt="Main Showcase">
                    </div>
                    <div class="absolute inset-0 bg-blue-600/0 group-hover:bg-blue-600/10 transition-all duration-300 flex items-center justify-center">
                        <span class="bg-white px-6 py-3 rounded-full font-bold shadow-xl opacity-0 group-hover:opacity-100 transition-opacity">Klik untuk Fullscreen</span>
                    </div>
                </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @for ($i = 1; $i <= 6; $i++)
                @php
                    $pathWebp = 'storage/projects/client-' . $project->id . '-' . $i . '.webp';
                    $pathPng  = 'storage/projects/client-' . $project->id . '-' . $i . '.png';
                    $pathJpg  = 'storage/projects/client-' . $project->id . '-' . $i . '.jpg';
                    
                    if (file_exists(public_path($pathWebp))) {
                        $finalPath = $pathWebp;
                    } elseif (file_exists(public_path($pathPng))) {
                        $finalPath = $pathPng;
                    } elseif (file_exists(public_path($pathJpg))) {
                        $finalPath = $pathJpg;
                    } else {
                        $finalPath = null;
                    }
                @endphp

                @if($finalPath)
                    <div class="bg-white p-4 rounded-[2.5rem] shadow-xl border border-slate-100/50 group cursor-zoom-in" onclick="openModal('{{ asset($finalPath) }}')">
                        <div class="aspect-[4/5] rounded-[1.8rem] overflow-hidden border border-slate-50 bg-slate-100">
                            <img src="{{ asset($finalPath) }}" 
                                loading="lazy" 
                                class="w-full h-auto object-cover object-top transition duration-700 group-hover:scale-105"
                                alt="Detail Project View">
                        </div>
                        <div class="mt-4 px-4 pb-2">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Project Detail: {{ $project->title }}</p>
                        </div>
                    </div>
                @endif
            @endfor
        </div>
            </div>
        </main>
    </div>

    <div id="imageModal" class="hidden fixed inset-0 z-[100] bg-slate-950/95 backdrop-blur-sm items-center justify-center p-4 md:p-10" role="dialog" aria-modal="true" aria-label="Image preview">
        <button type="button" id="modalClose" class="absolute top-8 right-8 text-white hover:text-blue-400 transition transform hover:rotate-90 duration-300" aria-label="Close image preview">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <div class="max-w-full max-h-full overflow-auto rounded-2xl scrollbar-hide">
            <img id="modalImg" src="" class="w-full h-auto rounded-lg" alt="">
        </div>
    </div>

    <script>
        function openModal(src) {
            const modal = document.getElementById('imageModal');
            const img = document.getElementById('modalImg');
            img.src = src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden'; // Stop scroll
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    </script>

    <footer class="bg-slate-900 py-20 text-center text-white mt-20">
        <h4 class="text-3xl font-bold mb-6">Need a design like this?</h4>
        <a href="{{ route('home') }}#contact" class="inline-block bg-blue-600 px-10 py-4 rounded-full font-bold hover:bg-blue-500 transition shadow-lg shadow-blue-600/20">Contact Surya</a>
    </footer>
</body>
</html>