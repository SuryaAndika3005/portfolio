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
     * Homepage: all projects (grouped by category into the works accordion),
     * all categories, and experience timeline.
     */
    public function index(): View
    {
        $projects = Project::with('category')->latest()->get();
        $categories = Category::all();
        $experiences = Experience::latest()->get();
        $skillGroups = config('skills.groups');
        $projectCount = Project::count();

        return view('portfolio.index', compact('projects', 'categories', 'experiences', 'skillGroups', 'projectCount'));
    }

    /**
     * Full project archive with client-side category filtering.
     */
    public function projects(): View
    {
        $projects = Project::with('category')->latest()->get();
        $categories = Category::all();

        return view('portfolio.projects', compact('projects', 'categories'));
    }

    /**
     * Single project detail page. Previous/next navigation is scoped to
     * the current project's own category and ordered the same way the
     * archive/homepage already order everything (->latest()) -- the one
     * ordering that's consistently used and trustworthy site-wide, unlike
     * is_highlighted/featured_order (unpopulated/unused, see the audit).
     */
    public function show(Project $project): View
    {
        $project->load('category');

        $siblingIds = Project::where('category_id', $project->category_id)->latest()->pluck('id');
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
