<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Project;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Portfolio overview — real counts only (Batch 8A explicitly forbids
     * invented analytics: no views/conversion/traffic, nothing the backend
     * can't actually answer). Gallery asset count sums each project's
     * stored gallery_images array in PHP rather than a SQL aggregate,
     * since it's a JSON column, not a relation.
     */
    public function index(): View
    {
        $totalProjects = Project::count();
        $totalCategories = Category::count();
        $totalGalleryImages = Project::pluck('gallery_images')
            ->reduce(fn ($carry, $images) => $carry + count($images ?? []), 0);

        $projectsByCategory = Category::withCount('projects')
            ->orderByDesc('projects_count')
            ->get();

        $recentProjects = Project::with('category')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalProjects',
            'totalCategories',
            'totalGalleryImages',
            'projectsByCategory',
            'recentProjects',
        ));
    }
}
