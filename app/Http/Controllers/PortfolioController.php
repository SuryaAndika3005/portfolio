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
     * Homepage: top 5 latest projects, all categories, and experience timeline.
     */
    public function index(): View
    {
        $projects = Project::with('category')->latest()->take(5)->get();
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
     * Single project detail page.
     */
    public function show(Project $project): View
    {
        $project->load('category');

        return view('portfolio.show', compact('project'));
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
