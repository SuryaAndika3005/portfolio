<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Global Language Catalog System — project-content side. Static UI-string
 * locale behavior (switching, persistence, unsupported-locale rejection,
 * missing-translation fallback for plain __() strings) is already covered
 * by LocalizationTest; this file covers Project::localized() and its
 * effect on the public project detail page and its metadata.
 *
 * lang/id/project_content.php and lang/en/project_content.php are keyed
 * by the REAL project IDs used in this portfolio's actual data (21, 22,
 * 1, ...), not by whatever auto-increment ID a factory would otherwise
 * assign under RefreshDatabase's empty test DB -- so every project here
 * is created with an explicit ->create(['id' => ...]) matching those
 * catalog entries. $guarded = [] on the model allows id through mass
 * assignment; SQLite/MySQL both accept an explicit PK on insert.
 */
class ProjectLocalizationTest extends TestCase
{
    use RefreshDatabase;

    private function project(int $id, array $overrides = []): Project
    {
        $category = Category::factory()->create(['slug' => 'it-development']);

        return Project::factory()->create(array_merge([
            'id' => $id,
            'category_id' => $category->id,
            'is_published' => true,
            'description' => 'Vision AI is a web-based attendance verification system that combines face recognition, guided multi-sample enrollment, and hybrid liveness checks.',
            'problem' => 'Face-based attendance needs to solve more than simply detecting a face.',
            'process' => 'I audited the complete computer-vision pipeline and measured individual processing stages.',
            'result' => 'Vision AI V2 provides an end-to-end attendance workflow.',
        ], $overrides));
    }

    public function test_featured_project_description_switches_locale(): void
    {
        $project = $this->project(21);

        app()->setLocale('en');
        $this->assertStringContainsString('web-based attendance verification system', $project->localized('description'));

        app()->setLocale('id');
        $this->assertStringContainsString('sistem verifikasi presensi berbasis web', $project->localized('description'));
    }

    public function test_featured_project_problem_process_result_switch_locale(): void
    {
        $project = $this->project(21);

        app()->setLocale('id');
        $this->assertStringContainsString('Presensi berbasis wajah', $project->localized('problem'));
        $this->assertStringContainsString('mengaudit keseluruhan pipeline', $project->localized('process'));
        $this->assertStringContainsString('menyediakan alur kerja presensi', $project->localized('result'));
    }

    public function test_missing_project_translation_falls_back_to_db(): void
    {
        // ID 999 has no entry anywhere in lang/{locale}/project_content.php.
        $project = $this->project(999, [
            'description' => 'A plain untranslated description.',
            'problem' => 'A plain untranslated problem.',
        ]);

        app()->setLocale('id');
        $this->assertSame($project->description, $project->localized('description'));
        $this->assertSame($project->problem, $project->localized('problem'));
    }

    public function test_missing_field_within_a_translated_project_falls_back_to_db(): void
    {
        // Injects an isolated, ephemeral translation for one field only
        // (via the translator directly, never touching the real catalog
        // file) so this test's "one field translated, one field not"
        // premise stays true regardless of how complete the real
        // lang/id/project_content.php entries happen to be at any given
        // time -- every currently-published project now has all 5 fields
        // translated, so there is no real-world example of a partial
        // entry left to point at.
        app('translator')->addLines(['project_content.998.description' => 'Deskripsi terjemahan saja.'], 'id');
        $project = $this->project(998, [
            'description' => 'Untranslated DB description.',
            'problem' => 'Untranslated DB problem.',
        ]);

        app()->setLocale('id');
        $this->assertSame('Deskripsi terjemahan saja.', $project->localized('description'));
        // problem has no injected translation -- must fall back to DB.
        $this->assertSame($project->problem, $project->localized('problem'));
    }

    public function test_project_22_now_uses_english_db_default_like_every_other_project(): void
    {
        // Curation follow-up (Section 5): Project 22's DB `description`
        // used to be natively Indonesian and needed an English override
        // to work correctly. It was normalized to English -- verify that
        // normalization actually took: the DB record itself must now be
        // English, and lang/en/project_content.php must carry no override
        // for it any more (the file is asserted empty for id 22 as a
        // proxy for that -- it's a plain return [];).
        $project = $this->project(22, [
            'description' => 'Dashboard Kreatif 523 Studio is an internal operational dashboard built to connect creative workflows.',
        ]);

        app()->setLocale('en');
        $this->assertSame($project->description, $project->localized('description'));
        $this->assertStringContainsString('internal operational dashboard', $project->localized('description'));

        $enOverrides = require lang_path('en/project_content.php');
        $this->assertArrayNotHasKey(22, $enOverrides);

        app()->setLocale('id');
        $this->assertStringContainsString('dashboard operasional internal', $project->localized('description'));
    }

    public function test_localized_never_leaks_a_raw_translation_key(): void
    {
        $project = $this->project(999);
        app()->setLocale('id');

        foreach (['role', 'description', 'problem', 'process', 'result'] as $field) {
            $value = $project->localized($field);
            if ($value !== null) {
                $this->assertStringNotContainsString('project_content.', $value);
            }
        }
    }

    public function test_project_page_metadata_and_json_ld_follow_locale(): void
    {
        $this->project(21);
        $this->get(route('lang.switch', ['locale' => 'id']));

        $response = $this->get('/project/21');

        $response->assertOk();
        $response->assertSee('sistem verifikasi presensi berbasis web', false);
        $response->assertSee('"description":"Vision AI adalah sistem verifikasi', false);
    }

    public function test_switching_locale_on_a_project_page_stays_on_the_same_project(): void
    {
        $this->project(21);

        $response = $this->get(route('lang.switch', ['locale' => 'id', 'next' => '/project/21']));

        $response->assertRedirect('/project/21');
    }

    public function test_a_non_featured_project_now_has_indonesian_content(): void
    {
        // Project 1 (LuxSuits) was never one of the 5 originally-Featured
        // projects -- this pins down that Language Content Completion
        // actually extended real coverage rather than leaving everything
        // outside Featured on DB-only fallback.
        $project = $this->project(1, [
            'role' => 'Graphic Designer',
            'description' => 'Visual promotional design for LuxSuits, a formal-wear rental brand.',
        ]);

        app()->setLocale('id');
        $this->assertSame('Desainer Grafis', $project->localized('role'));
        $this->assertStringContainsString('LuxSuits', $project->localized('description'));
        $this->assertStringContainsString('penyewaan pakaian formal', $project->localized('description'));
    }

    public function test_every_currently_published_project_has_complete_indonesian_content(): void
    {
        // Reads the real catalog file directly (not through the model --
        // no DB round-trip needed) and asserts every currently-published
        // project ID has all 5 required fields present and non-empty.
        // Projects 6 and 8 are deliberately excluded: both are
        // unpublished and already 404 publicly, so they are not required
        // to have translations (Section 9 of the batch brief).
        $publishedIds = [1, 2, 3, 4, 5, 7, 9, 10, 11, 12, 13, 14, 21, 22];
        $catalog = require lang_path('id/project_content.php');
        $requiredFields = ['role', 'description', 'problem', 'process', 'result'];

        foreach ($publishedIds as $id) {
            $this->assertArrayHasKey($id, $catalog, "Published project {$id} has no Indonesian catalog entry.");
            foreach ($requiredFields as $field) {
                $this->assertNotEmpty(
                    $catalog[$id][$field] ?? null,
                    "Published project {$id} is missing a translated '{$field}'."
                );
            }
        }

        foreach ([6, 8] as $unpublishedId) {
            $this->assertArrayNotHasKey($unpublishedId, $catalog);
        }
    }

    public function test_category_localization_is_consistent_across_public_surfaces(): void
    {
        $category = Category::factory()->create(['slug' => 'it-development', 'name' => 'IT & Development']);
        Project::factory()->create([
            'id' => 7001,
            'category_id' => $category->id,
            'is_published' => true,
        ]);

        $this->get(route('lang.switch', ['locale' => 'id']));

        $detail = $this->get('/project/7001');
        $archive = $this->get('/projects');

        $detail->assertOk()->assertSee('IT &amp; Pengembangan', false);
        $archive->assertOk()->assertSee('IT &amp; Pengembangan', false);
    }
}
