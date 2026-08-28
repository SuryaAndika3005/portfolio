<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExperienceRequest;
use App\Models\Experience;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Experience CMS — full CRUD around the existing, already database-backed
 * Experience model.
 *
 * index() ordering: every seeded row shares an identical created_at, so
 * Experience::latest() (created_at desc) was never really chronological —
 * it only happened to look plausible by accident of DB tie-breaking (see
 * LOCALIZATION_THEME_AUDIT.md). Rows are now grouped by category in a fixed
 * tier order (Professional Work, then Organization, then Events — matching
 * the order categories already appear across the site), and sorted
 * chronologically (current/most-recent first) within each tier via
 * Experience::sortChronologically(), which parses the existing `duration`
 * string rather than trusting created_at. This stays a single flat table
 * (no new filter UI, no drag-and-drop) to match Admin's current simplicity.
 */
class ExperienceController extends Controller
{
    public function index(): View
    {
        $categoryOrder = ['Professional Work' => 0, 'Organization' => 1, 'Events' => 2];

        $experiences = Experience::all()
            ->groupBy('category')
            ->sortBy(fn ($group, $category) => $categoryOrder[$category] ?? count($categoryOrder))
            ->flatMap(fn ($group) => Experience::sortChronologically($group))
            ->values();

        return view('admin.experiences.index', compact('experiences'));
    }

    public function create(): View
    {
        $experience = null;

        return view('admin.experiences.create', compact('experience'));
    }

    public function store(ExperienceRequest $request): RedirectResponse
    {
        Experience::create($request->validated());

        return redirect()->route('admin.experiences.index')->with('success', 'Experience added.');
    }

    public function edit(Experience $experience): View
    {
        return view('admin.experiences.edit', compact('experience'));
    }

    public function update(ExperienceRequest $request, Experience $experience): RedirectResponse
    {
        $experience->update($request->validated());

        return redirect()->route('admin.experiences.index')->with('success', 'Experience updated.');
    }

    public function destroy(Experience $experience): RedirectResponse
    {
        $experience->delete();

        return redirect()->route('admin.experiences.index')->with('success', 'Experience deleted.');
    }
}
