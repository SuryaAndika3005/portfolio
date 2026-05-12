<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->title }} | Surya Andika Portfolio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-blue-200 selection:text-blue-900">

    <nav class="flex justify-between items-center py-6 px-8 lg:px-20 max-w-[1600px] mx-auto bg-transparent absolute top-0 w-full z-50">
        <a href="{{ route('home') }}" class="group flex items-center gap-2 bg-white/20 backdrop-blur-md px-5 py-2.5 rounded-full text-white font-bold hover:bg-white hover:text-blue-600 transition-all duration-300 border border-white/30">
            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path></svg>
            Kembali
        </a>
    </nav>

    <header class="relative w-full h-[60vh] lg:h-[70vh] bg-slate-900 overflow-hidden">
        <img src="{{ asset('storage/' . $project->image_path) }}" alt="{{ $project->title }}" class="absolute inset-0 w-full h-full object-cover opacity-60">
        
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>

        <div class="absolute bottom-0 left-0 w-full max-w-[1200px] mx-auto px-8 lg:px-20 pb-16 transform translate-y-4">
            <span class="inline-block bg-blue-600 text-white text-sm font-bold px-4 py-1.5 rounded-full mb-4 tracking-widest uppercase shadow-lg shadow-blue-500/30">
                {{ $project->category->name ?? 'Project' }}
            </span>
            <h1 class="text-5xl lg:text-7xl font-extrabold text-white mb-4 tracking-tight">
                {{ $project->title }}
            </h1>
        </div>
    </header>

    <main class="max-w-[1000px] mx-auto px-8 lg:px-20 py-20 bg-white -mt-10 relative z-10 rounded-t-[3rem] shadow-2xl">
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-16 border-b border-slate-100 pb-10">
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">Role</p>
                <p class="text-slate-800 font-semibold">Lead Designer</p> </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">Client</p>
                <p class="text-slate-800 font-semibold">TBA</p> </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">Timeline</p>
                <p class="text-slate-800 font-semibold">2025</p> </div>
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">Tools</p>
                <p class="text-slate-800 font-semibold">Adobe Suite / Figma</p>
            </div>
        </div>

        <article class="prose prose-lg prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-900 prose-a:text-blue-600 hover:prose-a:text-blue-500">
            <h2 class="text-3xl font-bold text-slate-900 mb-6">Tentang Proyek Ini</h2>
            <p class="text-slate-600 leading-relaxed mb-8">
                {{ $project->description }}
            </p>

            <div class="w-full bg-slate-100 h-96 rounded-3xl flex items-center justify-center text-slate-400 border-2 border-dashed border-slate-300 mb-8">
                <p>Area Galeri / Desain UI / Layout Feed IG</p>
            </div>
            
            <p class="text-slate-600 leading-relaxed">
                Tantangan utama dalam proyek ini adalah bagaimana menjaga konsistensi identitas visual sekaligus memastikan pesan komunikasi tersampaikan dengan jelas kepada audiens...
            </p>
        </article>

        <div class="mt-20 pt-10 border-t border-slate-100 text-center">
            <h3 class="text-2xl font-bold text-slate-800 mb-6">Punya ide proyek serupa?</h3>
            <a href="mailto:surdik2811@gmail.com" class="inline-block bg-slate-900 text-white font-bold px-8 py-4 rounded-full hover:bg-blue-600 transition-colors duration-300 shadow-lg">
                Mari Diskusi Bersama
            </a>
        </div>
    </main>

</body>
</html>