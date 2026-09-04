<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Archive Taxonomy Restructure: the former single "IT & Development"
 * category (7 substantially different projects) was split into "Web &
 * Systems" (slug 'it-development', kept for backward compatibility --
 * see CategoryController's class docblock) and a new "AI & Data" category
 * (slug 'ai-data'). This covers the resulting 4-chapter Archive, the
 * Homepage's four-panel accordion representing each discipline in its own
 * panel, Project Detail category labels, and localization of the two
 * category names -- all against a small, fully self-contained dataset
 * rather than the real production categories, so this suite keeps passing
 * regardless of future production data changes.
 */
class ArchiveTaxonomyTest extends TestCase
{
    use RefreshDatabase;

    private function seedFourCategoryTaxonomy(): array
    {
        $graphicDesign = Category::factory()->create(['slug' => 'graphic-design', 'name' => 'Graphic Design']);
        $uiux = Category::factory()->create(['slug' => 'uiux-design', 'name' => 'UI/UX Design']);
        $webSystems = Category::factory()->create(['slug' => 'it-development', 'name' => 'Web & Systems']);
        $aiData = Category::factory()->create(['slug' => 'ai-data', 'name' => 'AI & Data']);

        Project::factory()->create(['category_id' => $graphicDesign->id, 'is_published' => true, 'title' => 'GD One']);
        Project::factory()->create(['category_id' => $uiux->id, 'is_published' => true, 'title' => 'UIUX One']);

        $websystemsProjects = collect(['Web Systems Alpha', 'Web Systems Beta'])
            ->map(fn ($title) => Project::factory()->create(['category_id' => $webSystems->id, 'is_published' => true, 'title' => $title]));

        $aiDataProjects = collect(['AI Data Alpha', 'AI Data Beta', 'AI Data Gamma'])
            ->map(fn ($title) => Project::factory()->create(['category_id' => $aiData->id, 'is_published' => true, 'title' => $title]));

        return compact('graphicDesign', 'uiux', 'webSystems', 'aiData', 'websystemsProjects', 'aiDataProjects');
    }

    public function test_archive_shows_four_chapters_in_order(): void
    {
        $this->seedFourCategoryTaxonomy();

        $html = $this->get('/projects')->assertOk()->getContent();

        $graphicPos = strpos($html, 'id="graphic-design"');
        $uiuxPos = strpos($html, 'id="uiux-design"');
        $webSystemsPos = strpos($html, 'id="it-development"');
        $aiDataPos = strpos($html, 'id="ai-data"');

        $this->assertNotFalse($graphicPos, 'Graphic Design chapter section missing.');
        $this->assertNotFalse($uiuxPos, 'UI/UX Design chapter section missing.');
        $this->assertNotFalse($webSystemsPos, 'Web & Systems chapter section missing.');
        $this->assertNotFalse($aiDataPos, 'AI & Data chapter section missing.');

        $this->assertTrue($graphicPos < $uiuxPos && $uiuxPos < $webSystemsPos && $webSystemsPos < $aiDataPos,
            'Chapters are not in the expected order: Graphic Design, UI/UX Design, Web & Systems, AI & Data.');

        $this->assertStringContainsString('Web &amp; Systems', $html);
        $this->assertStringContainsString('AI &amp; Data', $html);
    }

    public function test_web_systems_chapter_contains_only_its_own_projects(): void
    {
        $seed = $this->seedFourCategoryTaxonomy();

        $html = $this->get('/projects')->getContent();

        $webSystemsSection = substr($html, strpos($html, 'id="it-development"'), strpos($html, 'id="ai-data"') - strpos($html, 'id="it-development"'));

        $this->assertStringContainsString('Web Systems Alpha', $webSystemsSection);
        $this->assertStringContainsString('Web Systems Beta', $webSystemsSection);
        $this->assertStringNotContainsString('AI Data Alpha', $webSystemsSection);
    }

    public function test_ai_data_chapter_contains_only_its_own_projects(): void
    {
        $this->seedFourCategoryTaxonomy();

        $html = $this->get('/projects')->getContent();

        $aiDataSection = substr($html, strpos($html, 'id="ai-data"'));

        $this->assertStringContainsString('AI Data Alpha', $aiDataSection);
        $this->assertStringContainsString('AI Data Beta', $aiDataSection);
        $this->assertStringContainsString('AI Data Gamma', $aiDataSection);
        $this->assertStringNotContainsString('Web Systems Alpha', $aiDataSection);
    }

    public function test_published_counts_are_dynamic_not_hardcoded(): void
    {
        $seed = $this->seedFourCategoryTaxonomy();
        // One more Web & Systems project, one unpublished AI & Data project
        // (must not count) -- proves the displayed numbers are read live
        // from the database, not a fixed string.
        Project::factory()->create(['category_id' => $seed['webSystems']->id, 'is_published' => true, 'title' => 'Web Systems Gamma']);
        Project::factory()->create(['category_id' => $seed['aiData']->id, 'is_published' => false, 'title' => 'Unpublished AI Project']);

        $html = $this->get('/projects')->getContent();

        $webSystemsSection = substr($html, strpos($html, 'id="it-development"'), strpos($html, 'id="ai-data"') - strpos($html, 'id="it-development"'));
        $aiDataSection = substr($html, strpos($html, 'id="ai-data"'));

        $this->assertStringContainsString('3 Projects', $webSystemsSection);
        $this->assertStringContainsString('3 Projects', $aiDataSection);
        $this->assertStringNotContainsString('Unpublished AI Project', $aiDataSection);
    }

    public function test_homepage_selected_works_is_unaffected_by_category_taxonomy(): void
    {
        $seed = $this->seedFourCategoryTaxonomy();

        // Highlight one project from each of the two split categories --
        // buildFeaturedProjects() is is_highlighted/featured_order driven,
        // never category-slug driven, so both must appear regardless of
        // which side of the split they're on.
        $seed['websystemsProjects']->first()->update(['is_highlighted' => true, 'featured_order' => 1]);
        $seed['aiDataProjects']->first()->update(['is_highlighted' => true, 'featured_order' => 2]);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Web Systems Alpha', $html);
        $this->assertStringContainsString('AI Data Alpha', $html);
    }

    public function test_homepage_has_four_separate_discipline_panels(): void
    {
        $this->seedFourCategoryTaxonomy();

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertSame(4, substr_count($html, 'data-accordion-panel'));
        $this->assertStringContainsString('data-category="it-development"', $html);
        $this->assertStringContainsString('data-category="ai-data"', $html);
        $this->assertStringContainsString('alt="Web Systems Alpha"', $html);
        $this->assertStringContainsString('alt="AI Data Alpha"', $html);
    }

    public function test_ai_archive_uses_only_raw_covers_and_three_columns_on_large_screens(): void
    {
        $this->seedFourCategoryTaxonomy();
        $html = $this->get('/projects')->assertOk()->getContent();
        $section = explode('</section>', explode('id="ai-data"', $html, 2)[1], 2)[0];
        $this->assertSame(3, substr_count($section, 'data-cover-treatment="raw"'));
        $this->assertStringContainsString('xl:grid-cols-3', $section);
        $this->assertStringNotContainsString('bg-emerald-50', $section);
        $this->assertStringContainsString('aspect-[16/10]', $section);
    }

    public function test_project_detail_shows_the_correct_new_category_label(): void
    {
        $seed = $this->seedFourCategoryTaxonomy();

        $webSystemsProject = $seed['websystemsProjects']->first();
        $aiDataProject = $seed['aiDataProjects']->first();

        $this->get('/project/'.$webSystemsProject->id)->assertOk()->assertSee('Web & Systems');
        $this->get('/project/'.$aiDataProject->id)->assertOk()->assertSee('AI & Data');
    }

    public function test_category_names_localize_on_project_detail_and_archive(): void
    {
        $seed = $this->seedFourCategoryTaxonomy();
        $webSystemsProject = $seed['websystemsProjects']->first();

        $this->get(route('lang.switch', ['locale' => 'id']));

        $this->get('/project/'.$webSystemsProject->id)->assertOk()->assertSee('Web &amp; Sistem', false);
        $this->get('/projects')->assertOk()->assertSee('Web &amp; Sistem', false)->assertSee('AI &amp; Data', false);
    }

    /**
     * The 'it-development' slug (and therefore the #it-development anchor
     * on the Archive page) was deliberately kept rather than renamed to
     * something like 'web-systems' when the category was split -- purely a
     * display-name change, not a slug change (see CategoryController's
     * docblock and projects.blade.php's $chapters comment). This proves an
     * old bookmark or internal link to /projects#it-development still
     * lands on a real section, not nowhere.
     */
    public function test_old_it_development_anchor_still_resolves_to_a_real_section(): void
    {
        $this->seedFourCategoryTaxonomy();

        $html = $this->get('/projects')->getContent();

        $this->assertStringContainsString('id="it-development"', $html);
        $this->assertStringContainsString('data-chapter-section', $html);
        // The topbar's own chapter link still targets that same slug.
        $this->assertStringContainsString('data-target="it-development"', $html);
    }
}
