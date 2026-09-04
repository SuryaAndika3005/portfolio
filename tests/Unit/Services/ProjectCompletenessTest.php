<?php

namespace Tests\Unit\Services;

use App\Models\Category;
use App\Models\Project;
use App\Services\ProjectCompleteness;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Deterministic — no Gemini/HTTP involved (Sections 27, 75). Covers the
 * full range from an empty Project to a fully-filled one, plus the
 * gallery-emptiness and cover-fallback edge cases the checklist itself
 * has to get right without ever probing image_path directly for "cover".
 */
class ProjectCompletenessTest extends TestCase
{
    use RefreshDatabase;

    private function project(array $overrides = []): Project
    {
        $category = Category::factory()->create();

        return Project::create(array_merge([
            'category_id' => $category->id,
            'title' => 'Untitled',
        ], $overrides));
    }

    public function test_minimal_project_scores_zero(): void
    {
        $project = $this->project();

        $result = ProjectCompleteness::evaluate($project);

        $this->assertSame(0, $result['score']);
        $this->assertSame(10, $result['total']);
        $this->assertTrue(collect($result['checks'])->every(fn ($c) => $c['complete'] === false));
    }

    public function test_fully_filled_project_scores_full_marks(): void
    {
        $project = $this->project([
            'description' => 'A short description.',
            'role' => 'Graphic Designer',
            'year' => '2026',
            'client' => 'Acme Co',
            'image_path' => 'projects/main.jpg',
            'gallery_images' => ['storage/projects/g1.jpg', 'storage/projects/g2.jpg'],
            'problem' => 'The problem.',
            'process' => 'The process.',
            'result' => 'The result.',
            'tools' => 'Figma, Photoshop',
        ]);

        $result = ProjectCompleteness::evaluate($project);

        $this->assertSame(10, $result['score']);
        $this->assertSame(10, $result['total']);
        $this->assertTrue(collect($result['checks'])->every(fn ($c) => $c['complete'] === true));
    }

    public function test_one_image_project_still_counts_main_image_and_not_gallery(): void
    {
        $project = $this->project(['image_path' => 'projects/only.jpg', 'gallery_images' => []]);

        $result = ProjectCompleteness::evaluate($project);
        $byKey = collect($result['checks'])->keyBy('key');

        $this->assertTrue($byKey['image_path']['complete']);
        $this->assertFalse($byKey['gallery']['complete']);
    }

    public function test_empty_gallery_array_is_incomplete(): void
    {
        $project = $this->project(['gallery_images' => []]);

        $byKey = collect(ProjectCompleteness::evaluate($project)['checks'])->keyBy('key');

        $this->assertFalse($byKey['gallery']['complete']);
    }

    public function test_case_study_fields_are_independent(): void
    {
        $project = $this->project(['problem' => 'x', 'process' => null, 'result' => null]);

        $byKey = collect(ProjectCompleteness::evaluate($project)['checks'])->keyBy('key');

        $this->assertTrue($byKey['problem']['complete']);
        $this->assertFalse($byKey['process']['complete']);
        $this->assertFalse($byKey['result']['complete']);
    }

    public function test_graphic_design_and_web_app_categories_use_the_same_checklist(): void
    {
        // No category-specific fields exist in the current schema (no
        // live-demo/repository column for any category — Section 29), so
        // completeness must not vary by category name/slug.
        $graphicDesign = Category::factory()->create(['name' => 'Graphic Design', 'slug' => 'graphic-design']);
        $webApp = Category::factory()->create(['name' => 'Web & Systems', 'slug' => 'it-development']);

        $projectA = Project::create(['category_id' => $graphicDesign->id, 'title' => 'A', 'role' => 'Designer']);
        $projectB = Project::create(['category_id' => $webApp->id, 'title' => 'B', 'role' => 'Developer']);

        $this->assertSame(
            collect(ProjectCompleteness::evaluate($projectA)['checks'])->pluck('key')->all(),
            collect(ProjectCompleteness::evaluate($projectB)['checks'])->pluck('key')->all(),
        );
    }
}
