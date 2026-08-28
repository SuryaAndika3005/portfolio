<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Category CMS — deliberately Index/Edit/Destroy only, no Create.
 *
 * The homepage's Selected Works accordion (index.blade.php) iterates a
 * hardcoded 3-item $accordionPanels array keyed to exactly the slugs
 * graphic-design/uiux-design/it-development — any category outside those
 * three silently gets no panel there, no fallback. The Project Archive
 * DOES have a generic fallback for other categories, but the homepage does
 * not, so a newly created category's projects would be invisible on the
 * site's own front page. Per Batch 8B's explicit instruction, category
 * creation is deferred rather than shipping Admin functionality that
 * produces invisible public content — see the batch report's "Category
 * creation deferred" note. Only Name is editable; Slug is read-only
 * (CategoryRequest doesn't even accept it as input) because show.blade.php
 * keys its category-aware image treatment ($isUiux/$isIt) directly off
 * category->slug, and projects.blade.php/index.blade.php's grouping both
 * key off it too — renaming a slug would silently break all three.
 */
class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('projects')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $count = $category->projects()->count();

        if ($count > 0) {
            $noun = $count === 1 ? 'project' : 'projects';

            return redirect()->route('admin.categories.index')->with(
                'error',
                "This category still contains {$count} {$noun}. Reassign them to another category before deleting."
            );
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
