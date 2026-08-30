<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessage;
use App\Models\Category;
use App\Models\Experience;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    /**
     * Number of projects to show on the homepage's Featured Projects section
     * when no project is marked is_highlighted (see buildFeaturedProjects).
     */
    private const FEATURED_FALLBACK_COUNT = 4;

    /**
     * Homepage: featured projects, all projects (grouped by category into
     * the works accordion), all categories, and experience timeline.
     */
    public function index(): View
    {
        $projects = Project::with('category')->published()->latest()->get();
        $featuredProjects = $this->buildFeaturedProjects($projects);
        $categories = Category::all();
        $experiences = Experience::latest()->get();
        $skillGroups = config('skills.groups');
        $projectCount = Project::published()->count();

        return view('portfolio.index', compact('projects', 'featuredProjects', 'categories', 'experiences', 'skillGroups', 'projectCount'));
    }

    /**
     * Featured Projects for the homepage: is_highlighted projects ordered by
     * featured_order, or a fallback to the most recent published projects
     * when none are highlighted -- so the homepage never renders an empty
     * featured section. $allProjects is expected to already be
     * published+latest()-ordered (from index()), but this re-asserts
     * is_published defensively (cheap in-memory filter, not a query) so an
     * unpublished project can never surface here even if is_highlighted is
     * accidentally left true, or a future caller passes an unfiltered
     * collection. Ties in featured_order break deterministically by id
     * (ascending) rather than being left to whatever order the collection
     * happened to arrive in.
     */
    private function buildFeaturedProjects($allProjects)
    {
        $highlighted = $allProjects
            ->where('is_published', true)
            ->where('is_highlighted', true)
            ->sortBy([['featured_order', 'asc'], ['id', 'asc']])
            ->values();

        return $highlighted->isNotEmpty()
            ? $highlighted
            : $allProjects->where('is_published', true)->take(self::FEATURED_FALLBACK_COUNT);
    }

    /**
     * Full project archive with client-side category filtering.
     */
    public function projects(): View
    {
        $projects = Project::with('category')->published()->latest()->get();
        $categories = Category::all();

        return view('portfolio.projects', compact('projects', 'categories'));
    }

    /**
     * Single project detail page. Previous/next navigation is scoped to
     * the current project's own category, published-only (an unpublished
     * project never appears as a neighbor), and ordered the same way the
     * archive/homepage order everything by default (->latest()) --
     * is_highlighted/featured_order only drive the homepage's Featured
     * Projects section, not this chronological neighbor ordering.
     *
     * An unpublished project 404s on this public route (Curation Pass
     * 01.1 -- "unpublished" must mean actually inaccessible, not just
     * unlisted). This check is generic on is_published, not a per-ID
     * condition, and only applies to this public route -- the admin edit
     * route resolves the same Project model through a separate controller
     * untouched by this guard, so admin access is unaffected.
     */
    public function show(Project $project): View
    {
        abort_unless($project->is_published, 404);

        $project->load('category');

        $siblingIds = Project::where('category_id', $project->category_id)->published()->latest()->pluck('id');
        $position = $siblingIds->search($project->id);
        $siblingCount = $siblingIds->count();

        $previousProject = null;
        $nextProject = null;

        if ($position !== false && $siblingCount > 1) {
            $previousProject = Project::find($siblingIds[($position - 1 + $siblingCount) % $siblingCount]);
            $nextProject = Project::find($siblingIds[($position + 1) % $siblingCount]);
        }

        return view('portfolio.show', compact('project', 'previousProject', 'nextProject'));
    }

    /**
     * Handle contact form submission.
     * Rate-limited via the 'throttle:contact' middleware (see routes/web.php).
     */
    public function contact(ContactRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            Mail::to(config('portfolio.contact_email'))->send(new ContactMessage(
                senderName: $validated['name'],
                senderEmail: $validated['email'],
                body: $validated['message'],
            ));
        } catch (\Throwable $e) {
            Log::error('Portfolio contact mail failed to send.', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->withInput()->with(
                'error',
                'Sorry, something went wrong sending your message. Please try emailing me directly instead.'
            );
        }

        return redirect()->back()->with(
            'success',
            'Thank you! Your message has been sent successfully.'
        );
    }
}
