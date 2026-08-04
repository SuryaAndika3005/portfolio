@csrf

@php
    $allTools = ['Photoshop', 'Illustrator', 'Canva', 'Figma', 'After Effects', 'Premiere Pro', 'CapCut', 'Laravel', 'Tailwind CSS', 'Flutter', 'Python'];
    $selectedTools = collect(explode(',', old('tools', $project->tools ?? '')))->map(fn ($t) => trim($t))->filter();
    $roleOptions = ['Graphic Designer', 'UI/UX Designer', 'Web & App Developer', 'Mobile Developer', 'Video Editor', 'Photographer', 'Model'];
    $clientOptions = ['Personal / Commercial', 'Akademik', 'Sports & Event', '523 Studio', 'Alir Pictures'];
    $currentYear = (int) date('Y');
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Title</label>
        <input type="text" name="title" value="{{ old('title', $project->title ?? '') }}" required
               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Category</label>
        <select name="category_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            <option value="">Select category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $project->category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Year</label>
        <select name="year" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            <option value="">Select year</option>
            @for ($y = $currentYear + 1; $y >= $currentYear - 8; $y--)
                <option value="{{ $y }}" @selected(old('year', $project->year ?? '') == $y)>{{ $y }}</option>
            @endfor
        </select>
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Role</label>
        <input type="text" name="role" list="role-options" value="{{ old('role', $project->role ?? '') }}"
               placeholder="Pick or type your own" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
        <datalist id="role-options">
            @foreach ($roleOptions as $option) <option value="{{ $option }}"> @endforeach
        </datalist>
    </div>

    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Client</label>
        <input type="text" name="client" list="client-options" value="{{ old('client', $project->client ?? '') }}"
               placeholder="Pick or type your own" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
        <datalist id="client-options">
            @foreach ($clientOptions as $option) <option value="{{ $option }}"> @endforeach
        </datalist>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-2">Tools</label>
        <div class="flex flex-wrap gap-2 border border-slate-200 rounded-xl p-4">
            @foreach ($allTools as $tool)
                <label class="flex items-center gap-1.5 text-xs font-medium bg-slate-50 border border-slate-200 rounded-full px-3 py-1.5 cursor-pointer has-[:checked]:bg-blue-50 has-[:checked]:border-blue-300 has-[:checked]:text-blue-700">
                    <input type="checkbox" name="tools[]" value="{{ $tool }}" class="rounded border-slate-300" @checked($selectedTools->contains($tool))>
                    {{ $tool }}
                </label>
            @endforeach
        </div>
        <input type="text" name="tools_custom" placeholder="Other tools not listed (comma separated)" value="{{ old('tools_custom') }}"
               class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Short Description</label>
        <p class="text-xs text-slate-400 mb-2">One or two sentences. Shown on the project grid card, and as a fallback if you skip the case study fields below.</p>
        <textarea name="description" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{{ old('description', $project->description ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2 border-t border-slate-100 pt-6">
        <p class="text-sm font-bold text-slate-800 mb-1">Case Study (recommended)</p>
        <p class="text-xs text-slate-400 mb-4">Fill these in to show recruiters and clients how you think, not just what it looks like. Leave blank to skip — the detail page falls back to the short description above.</p>
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Problem</label>
                <p class="text-xs text-slate-400 mb-1.5">What challenge or goal was this project solving for the client/brand?</p>
                <textarea name="problem" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{{ old('problem', $project->problem ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Process</label>
                <p class="text-xs text-slate-400 mb-1.5">How did you approach it? Key decisions, iterations, tools used along the way.</p>
                <textarea name="process" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{{ old('process', $project->process ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Result</label>
                <p class="text-xs text-slate-400 mb-1.5">What was the outcome? Delivered assets, client feedback, measurable impact if any.</p>
                <textarea name="result" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{{ old('result', $project->result ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Cover Image</label>
        <p class="text-xs text-slate-400 mb-2">Main thumbnail on the project grid and hero of the detail page.</p>
        @if (! empty($project) && $project->image_path)
            <img src="{{ asset('storage/' . $project->image_path) }}" class="w-40 h-28 object-cover rounded-xl mb-3 border border-slate-200">
        @endif
        <input type="file" name="image" accept="image/*" class="w-full text-sm">
        <p class="text-xs text-slate-400 mt-1">Leave empty to keep the current image.</p>
        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2 border-t border-slate-100 pt-6">
        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Gallery Images</label>
        <p class="text-xs text-slate-400 mb-3">Extra screenshots shown in "Visual Showcase" on the detail page. Select multiple files at once.</p>

        @if (! empty($project) && $project->galleryImages()->isNotEmpty())
            <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-4">
                @foreach ($project->galleryImages() as $image)
                    <label class="relative block group">
                        <img src="{{ asset($image) }}" class="w-full aspect-square object-cover rounded-lg border border-slate-200">
                        <span class="absolute inset-0 bg-red-600/0 group-has-[:checked]:bg-red-600/60 rounded-lg flex items-center justify-center text-white text-[10px] font-bold opacity-0 group-has-[:checked]:opacity-100">Remove</span>
                        <input type="checkbox" name="remove_gallery[]" value="{{ $image }}" class="absolute top-1 right-1">
                    </label>
                @endforeach
            </div>
            <p class="text-xs text-slate-400 mb-3">Check an image above to remove it when you save.</p>
        @endif

        <input type="file" name="gallery[]" accept="image/*" multiple class="w-full text-sm">
        @error('gallery.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2 border-t border-slate-100 pt-6 flex items-start gap-8">
        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
            <input type="checkbox" name="is_highlighted" value="1" @checked(old('is_highlighted', $project->is_highlighted ?? false)) class="rounded border-slate-300">
            Show in Highlighted projects
        </label>
        <div class="flex items-center gap-2">
            <label class="text-sm font-semibold text-slate-700">Highlight order</label>
            <input type="number" min="0" name="featured_order" value="{{ old('featured_order', $project->featured_order ?? '') }}" placeholder="e.g. 1"
                   class="w-24 rounded-xl border border-slate-200 px-3 py-2 text-sm">
        </div>
    </div>
    <p class="md:col-span-2 -mt-4 text-xs text-slate-400">Lower "highlight order" shows first. Leave empty to fall back to newest-first among highlighted projects.</p>

</div>

<div class="mt-8 flex items-center gap-3">
    <button type="submit" class="bg-slate-900 text-white font-bold px-6 py-2.5 rounded-xl hover:bg-slate-800">
        {{ isset($project) ? 'Save Changes' : 'Add Project' }}
    </button>
    <a href="{{ route('admin.projects.index') }}" class="text-slate-500 text-sm font-semibold hover:text-slate-700">Cancel</a>
</div>
