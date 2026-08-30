<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * End-to-end proof that moving cover-dimension lookups out of the Blade
 * loop (into PortfolioController + App\Support\FeaturedCoverMetadata)
 * preserved the exact same Featured treatment/crop decisions the inline
 * getimagesize() call used to produce -- this hits the real homepage
 * route, not the support class in isolation (see
 * tests/Unit/Support/FeaturedCoverMetadataTest.php for that).
 */
class FeaturedCoverTreatmentTest extends TestCase
{
    use RefreshDatabase;

    private function realPng(int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);
        ob_start();
        imagepng($image);
        $bytes = ob_get_clean();
        imagedestroy($image);

        return $bytes;
    }

    /** Isolates the rendered markup for one project's Featured card by its detail-page href. */
    private function extractCardMarkup(string $html, int $projectId): string
    {
        $needle = 'href="http://localhost/project/'.$projectId.'"';
        $start = strpos($html, $needle);
        $this->assertNotFalse($start, "Card markup for project {$projectId} not found.");

        $end = strpos($html, '</a>', $start);

        return substr($html, $start, $end - $start);
    }

    /**
     * Fills Featured slots 1-2 (hero + secondary) with generic projects so
     * the project under test -- always given featured_order 3 -- lands in
     * the supporting tier (4/3 canvas) rather than becoming the hero by
     * default just because it's the only highlighted project. The hero/
     * secondary aspect-ratio math is separate and already covered by the
     * "existing theme/hero" test suites; this file is only about the
     * per-cover treatment decision, so slots 1-2 use plain, uninteresting
     * landscape covers.
     */
    private function fillHeroAndSecondarySlots(): void
    {
        // A neutral slug, deliberately none of 'graphic-design' /
        // 'uiux-design' / 'it-development' -- those are the ones under
        // test and must stay free for the tests to create themselves.
        $filler = Category::factory()->create(['slug' => 'filler-category']);
        foreach ([1, 2] as $order) {
            $path = "projects/filler-{$order}.webp";
            Project::factory()->create([
                'category_id' => $filler->id,
                'is_published' => true,
                'is_highlighted' => true,
                'featured_order' => $order,
                'image_path' => $path,
            ]);
            Storage::disk('public')->put($path, $this->realPng(800, 600));
        }
    }

    public function test_landscape_it_development_cover_gets_browser_treatment_with_left_top_crop(): void
    {
        Storage::fake('public');
        $this->fillHeroAndSecondarySlots();
        $category = Category::factory()->create(['slug' => 'it-development']);
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'is_published' => true,
            'is_highlighted' => true,
            'featured_order' => 3,
            'image_path' => 'projects/wide-app.webp',
        ]);
        // 16:9-ish, wider than the 4/3 supporting-row canvas -> object-left-top branch.
        Storage::disk('public')->put('projects/wide-app.webp', $this->realPng(1600, 900));

        $card = $this->extractCardMarkup($this->get('/')->getContent(), $project->id);

        $this->assertStringContainsString('object-left-top', $card);
    }

    public function test_graphic_design_cover_gets_artwork_treatment_with_top_crop(): void
    {
        Storage::fake('public');
        $this->fillHeroAndSecondarySlots();
        $category = Category::factory()->create(['slug' => 'graphic-design']);
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'is_published' => true,
            'is_highlighted' => true,
            'featured_order' => 3,
            'image_path' => 'projects/artwork.webp',
        ]);
        Storage::disk('public')->put('projects/artwork.webp', $this->realPng(1200, 900));

        $card = $this->extractCardMarkup($this->get('/')->getContent(), $project->id);

        $this->assertStringContainsString('object-top', $card);
        $this->assertStringNotContainsString('object-[center_20%]', $card);
        $this->assertStringNotContainsString('object-left-top', $card);
    }

    public function test_portrait_uiux_cover_gets_phone_treatment(): void
    {
        Storage::fake('public');
        $this->fillHeroAndSecondarySlots();
        $category = Category::factory()->create(['slug' => 'uiux-design']);
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'is_published' => true,
            'is_highlighted' => true,
            'featured_order' => 3,
            'image_path' => 'projects/app-screen.webp',
        ]);
        Storage::disk('public')->put('projects/app-screen.webp', $this->realPng(400, 800));

        $card = $this->extractCardMarkup($this->get('/')->getContent(), $project->id);

        $this->assertStringContainsString('object-[center_20%]', $card);
    }

    /**
     * A cover whose file can't be read (missing/corrupt) at request time
     * must not break the homepage -- it just falls back to the generic
     * 'browser'/object-top treatment, same as the old inline
     * getimagesize() call already handled via its `$dimensions && ...`
     * guards.
     */
    public function test_unreadable_cover_falls_back_safely_without_error(): void
    {
        Storage::fake('public');
        $this->fillHeroAndSecondarySlots();
        $category = Category::factory()->create(['slug' => 'it-development']);
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'is_published' => true,
            'is_highlighted' => true,
            'featured_order' => 3,
            'image_path' => 'projects/missing.webp',
        ]);
        // Deliberately never written to the fake disk.

        $response = $this->get('/');

        $response->assertOk();
        $card = $this->extractCardMarkup($response->getContent(), $project->id);
        $this->assertStringContainsString('object-top', $card);
    }
}
