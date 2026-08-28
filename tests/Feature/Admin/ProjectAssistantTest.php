<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * HTTP-level coverage for the AI Project Assistant endpoints. Every case
 * mocks Gemini via Http::fake() -- no real API quota is spent running this
 * suite (Section 87).
 */
class ProjectAssistantTest extends TestCase
{
    use RefreshDatabase;

    private function analysisPayload(array $overrides = []): array
    {
        return array_merge([
            'confirmed_facts' => ['Title: Test Project'],
            'visual_observations' => [],
            'inferences_needing_confirmation' => [],
            'unknowns' => ['Client budget'],
            'understanding' => [
                'visual_direction' => 'needs_context', 'role' => 'needs_context', 'objective' => 'needs_context',
                'main_challenge' => 'needs_context', 'process' => 'needs_context', 'outputs' => 'needs_context', 'measured_results' => 'not_available',
            ],
            'questions' => ['What was the client budget?'],
            'ready_for_draft' => false,
        ], $overrides);
    }

    private function fakeAnalysis(array $overrides = []): void
    {
        Http::fake(['*/interactions' => Http::response([
            'id' => 'v1_fake_interaction',
            'steps' => [['type' => 'model_output', 'content' => [['type' => 'text', 'text' => json_encode($this->analysisPayload($overrides))]]]],
        ], 200)]);
    }

    private function draftPayload(): array
    {
        return [
            'en' => ['description' => 'A test project.', 'problem' => 'Needed a solution.', 'process' => 'Built it.', 'result' => 'Delivered.'],
            'id' => ['description' => 'Proyek uji.', 'problem' => 'Butuh solusi.', 'process' => 'Membangunnya.', 'result' => 'Selesai.'],
            'assumptions' => [],
            'missing_information' => [],
            'fact_corrections' => [],
        ];
    }

    /**
     * Queues each payload as the next interactions-endpoint response, in
     * order. Two separate Http::fake([...]) calls in one test is NOT
     * equivalent to this -- the first-registered stub for a repeated URL
     * pattern can keep matching later requests too, which silently fed
     * the wrong schema (analysis shape) to a later draft-generation call
     * in an earlier version of this test. A sequence is unambiguous.
     */
    private function fakeSequence(array $payloads): void
    {
        $sequence = Http::fakeSequence('*/interactions');

        foreach ($payloads as $i => $payload) {
            $sequence->push([
                'id' => 'v1_fake_'.$i,
                'steps' => [['type' => 'model_output', 'content' => [['type' => 'text', 'text' => json_encode($payload)]]]],
            ], 200);
        }
    }

    // --- Auth protection (Section 6) -----------------------------------

    public function test_guest_cannot_reach_analyze_endpoint(): void
    {
        $this->post(route('admin.projects.assistant.analyze'))->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_reach_reply_endpoint(): void
    {
        $this->post(route('admin.projects.assistant.reply'))->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_reach_draft_endpoint(): void
    {
        $this->post(route('admin.projects.assistant.draft'))->assertRedirect(route('admin.login'));
    }

    // --- Analyze ---------------------------------------------------------

    public function test_analyze_returns_understanding_for_authenticated_admin(): void
    {
        $this->fakeAnalysis();

        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.analyze'), ['title' => 'Test Project']);

        $response->assertOk();
        $response->assertJsonPath('understanding.status.visual_direction', 'needs_context');
        $response->assertJsonPath('unknowns.0', 'Client budget');
    }

    public function test_analyze_rejects_oversized_image(): void
    {
        $file = UploadedFile::fake()->image('huge.jpg')->size(5000); // > 4096 KB cap

        $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.analyze'), ['image' => $file])
            ->assertSessionHasErrors('image');
    }

    public function test_analyze_rejects_non_image_upload(): void
    {
        $file = UploadedFile::fake()->create('script.php', 10, 'application/x-php');

        $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.analyze'), ['image' => $file])
            ->assertSessionHasErrors('image');
    }

    public function test_analyze_only_sends_images_the_project_actually_owns(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/owned.jpg', 'fake-bytes');
        Storage::disk('public')->put('projects/not-owned.jpg', 'fake-bytes');

        $category = Category::factory()->create();
        $project = Project::factory()->create(['category_id' => $category->id, 'image_path' => 'projects/owned.jpg']);

        $this->fakeAnalysis();

        $response = $this->actingAs(User::factory()->create())->post(
            route('admin.projects.assistant.project-analyze', $project),
            ['existing_images' => ['projects/owned.jpg', 'projects/not-owned.jpg']],
        );

        $response->assertOk();
        // Only the path this project actually owns should have been resolved
        // and sent -- "not-owned" must never appear in what was analyzed.
        $usedLabels = $response->json('images_used');
        $this->assertCount(1, $usedLabels);
    }

    /**
     * No real Project in this app currently has more than 5 images total
     * (main + gallery), so this cross-source cap (Section 48 of the
     * finalization patch) can't be exercised live -- covered here instead.
     * Each individual source (gallery[], existing_images[]) stays within
     * its own per-field validation cap of 8, but combined with the main +
     * cover uploads the total is 10 -- resolveImages() must still truncate
     * to MAX_IMAGES (8) rather than silently sending everything.
     */
    public function test_analyze_caps_combined_images_across_all_sources(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/existing1.jpg', 'x');
        Storage::disk('public')->put('projects/existing2.jpg', 'x');
        Storage::disk('public')->put('projects/existing3.jpg', 'x');
        Storage::disk('public')->put('projects/existing4.jpg', 'x');

        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'image_path' => 'projects/existing1.jpg',
            'cover_image_path' => 'projects/existing2.jpg',
            'gallery_images' => ['projects/existing3.jpg', 'projects/existing4.jpg'],
        ]);

        $this->fakeAnalysis();

        $response = $this->actingAs(User::factory()->create())->post(
            route('admin.projects.assistant.project-analyze', $project),
            [
                'image' => UploadedFile::fake()->image('new-main.jpg'),
                'cover_image' => UploadedFile::fake()->image('new-cover.jpg'),
                'gallery' => [
                    UploadedFile::fake()->image('g1.jpg'),
                    UploadedFile::fake()->image('g2.jpg'),
                    UploadedFile::fake()->image('g3.jpg'),
                    UploadedFile::fake()->image('g4.jpg'),
                ],
                'existing_images' => [
                    'projects/existing1.jpg',
                    'projects/existing2.jpg',
                    'projects/existing3.jpg',
                    'projects/existing4.jpg',
                ],
            ],
        );

        // 2 (image+cover) + 4 (gallery) + 4 (existing) = 10 candidates,
        // truncated to 8 -- never silently sent in full.
        $response->assertOk();
        $this->assertCount(8, $response->json('images_used'));
    }

    // --- Reply / draft require an active analysis first -------------------

    public function test_reply_without_prior_analysis_returns_friendly_error(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.reply'), ['message' => 'hello'])
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'Analyze the project'));
    }

    public function test_generate_draft_without_prior_analysis_returns_friendly_error(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.draft'))
            ->assertStatus(422);
    }

    public function test_refine_without_prior_draft_returns_friendly_error(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.refine'), ['feedback' => 'shorter please'])
            ->assertStatus(422);
    }

    // --- Full happy path + DB safety (Sections 52, 72, 96) ----------------

    public function test_analyze_then_draft_never_touches_the_database(): void
    {
        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'description' => 'Original human-written description.',
        ]);
        $user = $this->actingAs(User::factory()->create());

        $this->fakeSequence([
            $this->analysisPayload(['ready_for_draft' => true]),
            $this->draftPayload(),
        ]);

        $user->post(route('admin.projects.assistant.project-analyze', $project), ['title' => $project->title])->assertOk();
        $draftResponse = $user->post(route('admin.projects.assistant.project-draft', $project));
        $draftResponse->assertOk();
        $draftResponse->assertJsonPath('en.description', 'A test project.');

        // The whole point of "Apply Draft never auto-saves": the Project
        // row must be byte-for-byte unchanged after analyze + generateDraft.
        $this->assertSame('Original human-written description.', $project->fresh()->description);
    }

    public function test_refine_never_touches_the_database(): void
    {
        $category = Category::factory()->create();
        $project = Project::factory()->create(['category_id' => $category->id, 'problem' => 'Original problem text.']);
        $user = $this->actingAs(User::factory()->create());

        $this->fakeSequence([
            $this->analysisPayload(['ready_for_draft' => true]),
            $this->draftPayload(),
            [
                'en' => ['description' => 'A test project.', 'problem' => 'A shorter problem.', 'process' => 'Built it.', 'result' => 'Delivered.'],
                'id' => ['description' => 'Proyek uji.', 'problem' => 'Masalah singkat.', 'process' => 'Membangunnya.', 'result' => 'Selesai.'],
                'assumptions' => [], 'missing_information' => [], 'fact_corrections' => [],
            ],
        ]);

        $user->post(route('admin.projects.assistant.project-analyze', $project))->assertOk();
        $user->post(route('admin.projects.assistant.project-draft', $project))->assertOk();
        $refineResponse = $user->post(route('admin.projects.assistant.project-refine', $project), ['feedback' => 'Make the problem shorter.']);
        $refineResponse->assertOk();
        $refineResponse->assertJsonPath('en.problem', 'A shorter problem.');
        $refineResponse->assertJsonPath('changed_fields', fn ($f) => in_array('en.problem', $f, true));

        $this->assertSame('Original problem text.', $project->fresh()->problem);
    }

    // --- Reset -------------------------------------------------------------

    public function test_reset_clears_assistant_state(): void
    {
        $user = $this->actingAs(User::factory()->create());
        $this->fakeAnalysis();
        $user->post(route('admin.projects.assistant.analyze'), ['title' => 'X'])->assertOk();

        $user->post(route('admin.projects.assistant.reset'))->assertOk()->assertJson(['ok' => true]);

        // With state cleared, reply() must once again require a fresh analysis.
        $user->post(route('admin.projects.assistant.reply'), ['message' => 'hi'])->assertStatus(422);
    }

    // --- Cover apply is the one action that does persist (Section 65) -----

    public function test_apply_cover_rejects_a_path_the_project_does_not_own(): void
    {
        $category = Category::factory()->create();
        $project = Project::factory()->create(['category_id' => $category->id]);

        $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.apply-cover', $project), ['path' => 'projects/someone-elses-file.jpg'])
            ->assertStatus(422);

        $this->assertNull($project->fresh()->cover_image_path);
    }

    public function test_apply_cover_updates_an_owned_path(): void
    {
        $category = Category::factory()->create();
        $project = Project::factory()->create(['category_id' => $category->id, 'gallery_images' => ['storage/projects/gallery1.jpg']]);

        $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.apply-cover', $project), ['path' => 'storage/projects/gallery1.jpg'])
            ->assertOk()
            ->assertJson(['ok' => true, 'cover_image_path' => 'storage/projects/gallery1.jpg']);

        $this->assertSame('storage/projects/gallery1.jpg', $project->fresh()->cover_image_path);
    }

    // --- Rate limiting (Section 8) ------------------------------------

    public function test_rapid_repeated_requests_are_rate_limited(): void
    {
        $this->fakeAnalysis();
        $user = $this->actingAs(User::factory()->create());

        $lastResponse = null;
        for ($i = 0; $i < 21; $i++) {
            $lastResponse = $user->post(route('admin.projects.assistant.analyze'), ['title' => 'X']);
        }

        $lastResponse->assertStatus(429);
    }
}
