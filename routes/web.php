<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExperienceController as AdminExperienceController;
use App\Http\Controllers\Admin\ProjectAssistantController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PortfolioController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

// SetLocale is scoped to just these public-facing routes (not the global
// web group) specifically so it never reaches Admin -- Admin stays
// English-only this release regardless of whatever locale a visitor has
// selected on the public site in the same browser session. Carbon's own
// diffForHumans() reads app()->getLocale() directly, so if this middleware
// were applied globally, Admin's "2 weeks ago" style timestamps would
// silently start rendering in Indonesian too even though no Admin view
// calls __() -- this scoping is what actually prevents that leak.
Route::middleware(SetLocale::class)->group(function () {
    Route::get('/', [PortfolioController::class, 'index'])->name('home');
    Route::get('/project/{project}', [PortfolioController::class, 'show'])->name('portfolio.show');
    Route::get('/projects', [PortfolioController::class, 'projects'])->name('portfolio.projects');
    Route::get('/lang/{locale}', [LocaleController::class, 'switch'])->name('lang.switch');

    // Rute untuk menangani pengiriman form kontak
    Route::post('/contact', [PortfolioController::class, 'contact'])
        ->middleware('throttle:contact')
        ->name('contact.send');
});

Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'store'])->name('admin.login.store');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('projects', AdminProjectController::class)->except(['show']);

    // AI Project Assistant (V1.1) — server-side Gemini only, never a public
    // endpoint. Registered twice (Create: no Project yet, Edit: bound
    // {project}) rather than one {project?} group — route()/URL generation
    // for an absent optional route-model-bound param produces a broken
    // double-slash URL ("assistant//analyze"), so this avoids that
    // footgun entirely instead of working around it in the view. Both
    // groups hit the exact same controller actions (?Project $project is
    // simply null for the Create group). Rate-limited on top of 'auth' to
    // absorb double-clicks/JS retry loops, not to manage real
    // multi-tenant traffic (Section 8).
    Route::middleware('throttle:ai-assistant')->prefix('projects/assistant')->name('projects.assistant.')->group(function () {
        Route::post('analyze', [ProjectAssistantController::class, 'analyze'])->name('analyze');
        Route::post('reply', [ProjectAssistantController::class, 'reply'])->name('reply');
        Route::post('draft', [ProjectAssistantController::class, 'generateDraft'])->name('draft');
        Route::post('refine', [ProjectAssistantController::class, 'refine'])->name('refine');
        Route::post('reset', [ProjectAssistantController::class, 'reset'])->name('reset');
        Route::post('cover', [ProjectAssistantController::class, 'recommendCover'])->name('cover');
        // V1.2: Tool Suggestions/Quality Review/SEO Assistant all work
        // before the first save too (they read live form values), so they
        // exist in both route groups like analyze/draft/refine already do.
        // AI Suggested Gallery Order does NOT — it's Edit-only, see the
        // project-scoped group below.
        Route::post('tools', [ProjectAssistantController::class, 'suggestTools'])->name('tools');
        Route::post('quality-review', [ProjectAssistantController::class, 'reviewQuality'])->name('quality-review');
        Route::post('seo', [ProjectAssistantController::class, 'generateSeo'])->name('seo');
    });
    Route::middleware('throttle:ai-assistant')->prefix('projects/{project}/assistant')->name('projects.assistant.')->group(function () {
        Route::post('analyze', [ProjectAssistantController::class, 'analyze'])->name('project-analyze');
        Route::post('reply', [ProjectAssistantController::class, 'reply'])->name('project-reply');
        Route::post('draft', [ProjectAssistantController::class, 'generateDraft'])->name('project-draft');
        Route::post('refine', [ProjectAssistantController::class, 'refine'])->name('project-refine');
        Route::post('reset', [ProjectAssistantController::class, 'reset'])->name('project-reset');
        Route::post('cover', [ProjectAssistantController::class, 'recommendCover'])->name('project-cover');
        Route::post('apply-cover', [ProjectAssistantController::class, 'applyCover'])->name('apply-cover');
        Route::post('tools', [ProjectAssistantController::class, 'suggestTools'])->name('project-tools');
        Route::post('quality-review', [ProjectAssistantController::class, 'reviewQuality'])->name('project-quality-review');
        Route::post('seo', [ProjectAssistantController::class, 'generateSeo'])->name('project-seo');
        Route::post('gallery-order', [ProjectAssistantController::class, 'suggestGalleryOrder'])->name('gallery-order');
    });
    // No create/store: category creation is deferred (see CategoryController
    // docblock) -- the homepage's Selected Works accordion doesn't render
    // categories outside its fixed 3-slug list.
    Route::resource('categories', AdminCategoryController::class)->except(['show', 'create', 'store']);
    Route::resource('experiences', AdminExperienceController::class)->except(['show']);
});
