<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Manual Gallery Reorder (V1.2, Sections 6-10, 76) — no Gemini involved.
 * Covers ProjectController::update()'s reorderedGallery() directly through
 * the real HTTP PUT the Admin form submits.
 */
class ProjectGalleryReorderTest extends TestCase
{
    use RefreshDatabase;

    private function projectWithGallery(array $gallery): Project
    {
        $category = Category::factory()->create();

        return Project::factory()->create([
            'category_id' => $category->id,
            'gallery_images' => $gallery,
        ]);
    }

    private function baseFields(Project $project): array
    {
        return [
            'category_id' => $project->category_id,
            'title' => $project->title,
        ];
    }

    public function test_valid_reorder_persists_the_new_sequence(): void
    {
        $project = $this->projectWithGallery(['storage/projects/a.jpg', 'storage/projects/b.jpg', 'storage/projects/c.jpg']);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.projects.update', $project), array_merge($this->baseFields($project), [
                'gallery_order' => ['storage/projects/c.jpg', 'storage/projects/a.jpg', 'storage/projects/b.jpg'],
            ]))
            ->assertRedirect(route('admin.projects.index'));

        $this->assertSame(
            ['storage/projects/c.jpg', 'storage/projects/a.jpg', 'storage/projects/b.jpg'],
            $project->fresh()->galleryImages(),
        );
    }

    public function test_same_images_different_order_is_not_treated_as_a_change_in_count(): void
    {
        $project = $this->projectWithGallery(['storage/projects/a.jpg', 'storage/projects/b.jpg']);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.projects.update', $project), array_merge($this->baseFields($project), [
                'gallery_order' => ['storage/projects/b.jpg', 'storage/projects/a.jpg'],
            ]));

        $fresh = $project->fresh();
        $this->assertCount(2, $fresh->galleryImages());
        $this->assertSame('storage/projects/b.jpg', $fresh->galleryImages()[0]);
    }

    public function test_duplicate_path_in_order_is_rejected_and_falls_back_to_original_order(): void
    {
        $project = $this->projectWithGallery(['storage/projects/a.jpg', 'storage/projects/b.jpg']);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.projects.update', $project), array_merge($this->baseFields($project), [
                // "a" duplicated, "b" never mentioned — not a valid permutation.
                'gallery_order' => ['storage/projects/a.jpg', 'storage/projects/a.jpg'],
            ]));

        $this->assertSame(
            ['storage/projects/a.jpg', 'storage/projects/b.jpg'],
            $project->fresh()->galleryImages(),
        );
    }

    public function test_unknown_path_in_order_is_rejected_and_falls_back_to_original_order(): void
    {
        $project = $this->projectWithGallery(['storage/projects/a.jpg', 'storage/projects/b.jpg']);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.projects.update', $project), array_merge($this->baseFields($project), [
                'gallery_order' => ['storage/projects/a.jpg', 'storage/projects/does-not-exist.jpg'],
            ]));

        $this->assertSame(
            ['storage/projects/a.jpg', 'storage/projects/b.jpg'],
            $project->fresh()->galleryImages(),
        );
    }

    public function test_foreign_project_path_in_order_is_rejected(): void
    {
        $project = $this->projectWithGallery(['storage/projects/a.jpg', 'storage/projects/b.jpg']);
        $other = $this->projectWithGallery(['storage/projects/other.jpg']);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.projects.update', $project), array_merge($this->baseFields($project), [
                'gallery_order' => ['storage/projects/a.jpg', 'storage/projects/other.jpg'],
            ]));

        // Neither project's gallery was corrupted by the foreign path.
        $this->assertSame(
            ['storage/projects/a.jpg', 'storage/projects/b.jpg'],
            $project->fresh()->galleryImages(),
        );
        $this->assertSame(['storage/projects/other.jpg'], $other->fresh()->galleryImages());
    }

    public function test_reorder_combined_with_removal_reorders_only_the_remaining_images(): void
    {
        $project = $this->projectWithGallery(['storage/projects/a.jpg', 'storage/projects/b.jpg', 'storage/projects/c.jpg']);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.projects.update', $project), array_merge($this->baseFields($project), [
                'remove_gallery' => ['storage/projects/b.jpg'],
                'gallery_order' => ['storage/projects/c.jpg', 'storage/projects/a.jpg'],
            ]));

        $this->assertSame(
            ['storage/projects/c.jpg', 'storage/projects/a.jpg'],
            $project->fresh()->galleryImages(),
        );
    }

    public function test_no_gallery_order_field_keeps_existing_order(): void
    {
        $project = $this->projectWithGallery(['storage/projects/a.jpg', 'storage/projects/b.jpg']);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.projects.update', $project), $this->baseFields($project));

        $this->assertSame(
            ['storage/projects/a.jpg', 'storage/projects/b.jpg'],
            $project->fresh()->galleryImages(),
        );
    }
}
