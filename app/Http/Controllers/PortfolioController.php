<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Experience;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    /**
     * Homepage: highlighted projects first (falling back to latest if none
     * are highlighted yet), experience timeline grouped by category.
     */
    public function index(): View
    {
        $projects = Project::with('category')->highlighted()->take(5)->get();

        if ($projects->isEmpty()) {
            $projects = Project::with('category')->latest()->take(5)->get();
        }

        $categories = Category::all();
        $experiences = Experience::orderBy('category')->latest()->get()->groupBy('category');

        return view('portfolio.index', compact('projects', 'categories', 'experiences'));
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
     */
    public function contact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            Mail::send([], [], function ($message) use ($validated) {
                $message->to(config('mail.admin_address', 'surdik2811@gmail.com'))
                    ->subject('New Portfolio Message from '.$validated['name'])
                    ->html(
                        '<div style="font-family: sans-serif; padding: 20px; color: #334155; max-width: 600px; border: 1px solid #e2e8f0;">'
                        .'<h2 style="color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">New Portfolio Inquiry</h2>'
                        .'<p style="margin-top: 20px;"><strong>Name:</strong> '.e($validated['name']).'</p>'
                        .'<p><strong>Email:</strong> <a href="mailto:'.e($validated['email']).'">'.e($validated['email']).'</a></p>'
                        .'<div style="margin-top: 20px; padding: 15px; border-left: 4px solid #3b82f6; background: #f8fafc;">'
                        .'<p style="margin: 0; font-weight: bold; color: #475569;">Message:</p>'
                        .'<p style="margin: 0; white-space: pre-wrap; line-height: 1.6;">'.e($validated['message']).'</p>'
                        .'</div></div>'
                    );
            });
        } catch (\Throwable $e) {
            Log::error('Portfolio contact mail failed to send.', ['error' => $e->getMessage()]);

            return redirect()->back()->withInput()->with(
                'error',
                'Sorry, something went wrong sending your message. Please try emailing me directly instead.'
            );
        }

        return redirect()->back()->with('success', 'Thank you! Your message has been sent successfully.');
    }
}
