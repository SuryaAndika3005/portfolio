<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::with('category')->latest()->paginate(12);

        return view('admin.projects.index', compact('projects'));
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

        $remove = collect($request->input('remove_gallery', []));
        $remaining = collect($project->galleryImages())
            ->reject(fn ($path) => $remove->contains($path))
            ->values();

        foreach ($remove as $path) {
            Storage::disk('public')->delete(ltrim(str_replace('storage/', '', $path), '/'));
        }

        $data['gallery_images'] = $remaining->merge($this->uploadGalleryImages($request))->values()->all();

        $project->update($data);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        if ($project->image_path) {
            Storage::disk('public')->delete($project->image_path);
        }

        foreach ($project->galleryImages() as $image) {
            Storage::disk('public')->delete(ltrim(str_replace('storage/', '', $image), '/'));
        }

        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted.');
    }

    private function fieldsFromRequest(ProjectRequest $request): array
    {
        $data = $request->safe()->except(['tools', 'tools_custom', 'image', 'gallery', 'remove_gallery']);
        $data['tools'] = $request->toolsString();

        return $data;
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
