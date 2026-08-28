<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();

        $projects = Project::with('category')
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->integer('category')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.projects.index', compact('projects', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $project = null;

        return view('admin.projects.create', compact('categories', 'project'));
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $data = $this->fieldsFromRequest($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('projects', 'public');
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('projects', 'public');
        }

        $data['gallery_images'] = $this->uploadGalleryImages($request);

        Project::create($data);

        return redirect()->route('admin.projects.index')->with('success', 'Project added.');
    }

    public function edit(Project $project): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.projects.edit', compact('project', 'categories'));
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $data = $this->fieldsFromRequest($request);

        if ($request->hasFile('image')) {
            if ($project->image_path) {
                Storage::disk('public')->delete($project->image_path);
            }
            $data['image_path'] = $request->file('image')->store('projects', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($project->cover_image_path) {
                Storage::disk('public')->delete($project->cover_image_path);
            }
            $data['cover_image_path'] = $request->file('cover_image')->store('projects', 'public');
        } elseif ($request->boolean('remove_cover_image') && $project->cover_image_path) {
            Storage::disk('public')->delete($project->cover_image_path);
            $data['cover_image_path'] = null;
        }

        // Intersected against the project's own stored gallery paths so an
        // arbitrary submitted string can never reach Storage::delete() below
        // — only a path this project actually owns can ever be removed.
        $existingGallery = collect($project->galleryImages());
        $remove = collect($request->input('remove_gallery', []))
            ->intersect($existingGallery)
            ->values();
        $kept = $existingGallery
            ->reject(fn ($path) => $remove->contains($path))
            ->values();

        foreach ($remove as $path) {
            Storage::disk('public')->delete(ltrim(str_replace('storage/', '', $path), '/'));
        }

        $remaining = $this->reorderedGallery($request, $kept);

        $data['gallery_images'] = $remaining->merge($this->uploadGalleryImages($request))->values()->all();

        $project->update($data);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        if ($project->image_path) {
            Storage::disk('public')->delete($project->image_path);
        }

        if ($project->cover_image_path) {
            Storage::disk('public')->delete($project->cover_image_path);
        }

        foreach ($project->galleryImages() as $image) {
            Storage::disk('public')->delete(ltrim(str_replace('storage/', '', $image), '/'));
        }

        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted.');
    }

    private function fieldsFromRequest(ProjectRequest $request): array
    {
        $data = $request->safe()->except(['tools', 'tools_custom', 'image', 'cover_image', 'remove_cover_image', 'gallery', 'remove_gallery', 'gallery_order']);
        $data['tools'] = $request->toolsString();

        return $data;
    }

    /**
     * Manual gallery reorder (V1.2, Sections 6-10) — display order is the
     * gallery_images JSON array's own order (confirmed against the
     * migration/model; no position table added). gallery_order[] is
     * trusted only when it's an exact permutation of $kept: every
     * submitted path must already belong to this project's own
     * (post-removal) gallery, and every kept path must be accounted for.
     * A partial, tampered, or foreign-project path array is never
     * partially honored — it's rejected wholesale and the original kept
     * order is used instead, so a malformed request can reorder but never
     * corrupt, duplicate, or inject gallery entries.
     */
    private function reorderedGallery(ProjectRequest $request, Collection $kept): Collection
    {
        $submitted = collect($request->input('gallery_order', []));

        if ($submitted->isEmpty()) {
            return $kept;
        }

        $validOrder = $submitted->intersect($kept)->unique()->values();

        return $validOrder->count() === $kept->count() ? $validOrder : $kept;
    }

    private function uploadGalleryImages(ProjectRequest $request): array
    {
        if (! $request->hasFile('gallery')) {
            return [];
        }

        return collect($request->file('gallery'))
            ->map(fn ($file) => 'storage/'.$file->store('projects', 'public'))
            ->values()
            ->all();
    }
}
