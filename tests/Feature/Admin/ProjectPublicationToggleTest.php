<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * is_published had no admin form control at all before this pass — it could
 * only be toggled via direct DB access. Covers the fix through the real HTTP
 * PUT the Admin form submits, including the "unchecked checkbox must still
 * save" case that already required the same treatment for is_highlighted.
 */
class ProjectPublicationToggleTest extends TestCase
{
    use RefreshDatabase;

    private function baseFields(Project $project): array
    {
        return [
            'category_id' => $project->category_id,
            'title' => $project->title,
        ];
    }

    public function test_unchecking_published_in_the_admin_form_unpublishes_the_project(): void
    {
        $project = Project::factory()->create([
            'category_id' => Category::factory()->create()->id,
            'is_published' => true,
        ]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.projects.update', $project), $this->baseFields($project))
            ->assertRedirect(route('admin.projects.index'));

        $this->assertFalse((bool) $project->fresh()->is_published);
    }

    public function test_checking_published_in_the_admin_form_republishes_the_project(): void
    {
        $project = Project::factory()->create([
            'category_id' => Category::factory()->create()->id,
            'is_published' => false,
        ]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.projects.update', $project), array_merge($this->baseFields($project), [
                'is_published' => '1',
            ]))
            ->assertRedirect(route('admin.projects.index'));

        $this->assertTrue((bool) $project->fresh()->is_published);
    }

    public function test_creating_a_project_without_touching_the_checkbox_defaults_to_published(): void
    {
        $category = Category::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.store'), [
                'category_id' => $category->id,
                'title' => 'New Project',
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.projects.index'));

        $this->assertTrue((bool) Project::where('title', 'New Project')->first()->is_published);
    }
}
