<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * HTTP-level coverage for the V1.2 AI Assistant endpoints (Tool
 * Suggestions, AI Suggested Gallery Order, Quality Review, SEO Assistant,
 * and the upgraded ranked Cover recommendation). Every case mocks Gemini
 * via Http::fake() — no real API quota spent (Section 74).
 */
class ProjectV12AssistantTest extends TestCase
{
    use RefreshDatabase;

    private function fakeStructured(array $data): void
    {
        Http::fake(['*/interactions' => Http::response([
            'id' => 'v1_fake_interaction',
            'steps' => [['type' => 'model_output', 'content' => [['type' => 'text', 'text' => json_encode($data)]]]],
        ], 200)]);
    }

    private function project(array $overrides = []): Project
    {
        $category = Category::factory()->create();

        return Project::factory()->create(array_merge(['category_id' => $category->id], $overrides));
    }

    // --- Auth protection (Section 69) -------------------------------------

    public function test_guest_cannot_reach_tools_endpoint(): void
    {
        $this->post(route('admin.projects.assistant.tools'))->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_reach_quality_review_endpoint(): void
    {
        $this->post(route('admin.projects.assistant.quality-review'))->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_reach_seo_endpoint(): void
    {
        $this->post(route('admin.projects.assistant.seo'))->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_reach_gallery_order_endpoint(): void
    {
        $project = $this->project(['gallery_images' => ['storage/projects/a.jpg', 'storage/projects/b.jpg']]);

        $this->post(route('admin.projects.assistant.gallery-order', $project))->assertRedirect(route('admin.login'));
    }

    // --- Tool Suggestions (Sections 17-21, 76) ------------------------------

    public function test_suggest_tools_returns_suggestions_and_does_not_touch_the_project(): void
    {
        $project = $this->project(['tools' => 'Figma']);
        $this->fakeStructured(['suggested_tools' => [
            ['name' => 'Illustrator', 'reason' => 'Process mentions vector illustration work.'],
        ]]);

        $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.project-tools', $project), ['title' => $project->title])
            ->assertOk()
            ->assertJsonPath('suggested_tools.0.name', 'Illustrator');

        // Confirming a suggestion happens client-side only — the endpoint
        // itself must never write to the tools column.
        $this->assertSame('Figma', $project->fresh()->tools);
    }

    public function test_suggest_tools_works_before_project_is_saved(): void
    {
        $this->fakeStructured(['suggested_tools' => []]);

        $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.tools'), ['title' => 'New Project', 'notes' => 'Built with Laravel and Tailwind.'])
            ->assertOk()
            ->assertJson(['suggested_tools' => []]);
    }

    /**
     * Provider-usage audit (V1.3, Section 11): a provider failure —
     * including the new conservative 429 handling — must still never
     * mutate the Project row, and the raw provider status/payload must
     * never reach the response.
     */
    public function test_suggest_tools_provider_429_does_not_mutate_the_project_and_hides_provider_details(): void
    {
        $project = $this->project(['tools' => 'Figma', 'updated_at' => now()->subDay()]);
        $originalUpdatedAt = $project->updated_at;

        Http::fake(['*/interactions' => Http::response(['error' => ['message' => 'quota exceeded, key=SECRET']], 429)]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.project-tools', $project), ['title' => $project->title]);

        $response->assertStatus(429);
        $this->assertStringNotContainsString('SECRET', $response->json('message'));
        $this->assertSame('Figma', $project->fresh()->tools);
        $this->assertTrue($originalUpdatedAt->equalTo($project->fresh()->updated_at));
    }

    // --- AI Suggested Gallery Order (Sections 12-16, 70, 76) ----------------

    public function test_gallery_order_rejects_project_with_fewer_than_two_images(): void
    {
        $project = $this->project(['gallery_images' => ['storage/projects/only.jpg']]);

        $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.gallery-order', $project))
            ->assertStatus(422);
    }

    public function test_gallery_order_ignores_a_foreign_path_and_uses_owned_order_instead(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/a.jpg', 'fake-bytes');
        Storage::disk('public')->put('projects/b.jpg', 'fake-bytes');

        $project = $this->project(['gallery_images' => ['storage/projects/a.jpg', 'storage/projects/b.jpg']]);
        $this->fakeStructured([
            'ordered_indices' => [1, 0],
            'reasoning_summary' => [
                ['index' => 1, 'reason' => 'Stronger opening.'],
                ['index' => 0, 'reason' => 'Supporting detail.'],
            ],
        ]);

        $response = $this->actingAs(User::factory()->create())->post(
            route('admin.projects.assistant.gallery-order', $project),
            // Submitted order includes a path this project does NOT own —
            // the whole submitted order must be rejected wholesale in
            // favor of the project's own stored order (Section 70).
            ['gallery_paths' => ['storage/projects/a.jpg', 'storage/projects/someone-elses.jpg']],
        )->assertOk();

        $this->assertSame(
            ['storage/projects/a.jpg', 'storage/projects/b.jpg'],
            $response->json('current_order'),
        );
    }

    public function test_gallery_order_returns_suggested_sequence_mapped_to_real_paths(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/a.jpg', 'fake-bytes');
        Storage::disk('public')->put('projects/b.jpg', 'fake-bytes');
        Storage::disk('public')->put('projects/c.jpg', 'fake-bytes');

        $project = $this->project(['gallery_images' => ['storage/projects/a.jpg', 'storage/projects/b.jpg', 'storage/projects/c.jpg']]);
        $this->fakeStructured([
            'ordered_indices' => [2, 0, 1],
            'reasoning_summary' => [
                ['index' => 2, 'reason' => 'Strong opening visual.'],
                ['index' => 0, 'reason' => 'Detail shot next.'],
                ['index' => 1, 'reason' => 'Closing frame.'],
            ],
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.gallery-order', $project))
            ->assertOk();

        $this->assertSame(
            ['storage/projects/c.jpg', 'storage/projects/a.jpg', 'storage/projects/b.jpg'],
            collect($response->json('suggested_order'))->pluck('path')->all(),
        );

        // Suggesting an order must never itself change what's persisted —
        // only an explicit Save Changes (with gallery_order[]) does that.
        $this->assertSame(
            ['storage/projects/a.jpg', 'storage/projects/b.jpg', 'storage/projects/c.jpg'],
            $project->fresh()->galleryImages(),
        );
    }

    public function test_gallery_order_rejects_malformed_ai_response_safely(): void
    {
        $project = $this->project(['gallery_images' => ['storage/projects/a.jpg', 'storage/projects/b.jpg']]);
        // ordered_indices is not a valid permutation of [0, 1].
        $this->fakeStructured(['ordered_indices' => [0, 0], 'reasoning_summary' => []]);

        $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.gallery-order', $project))
            ->assertStatus(502);

        $this->assertSame(
            ['storage/projects/a.jpg', 'storage/projects/b.jpg'],
            $project->fresh()->galleryImages(),
        );
    }

    // --- Quality Review (Sections 33-42, 77) --------------------------------

    public function test_quality_review_returns_structured_issues_and_does_not_touch_the_project(): void
    {
        $project = $this->project([
            'description' => 'A poster series.',
            'problem' => 'Needed a cohesive campaign.',
            'process' => 'Used Photoshop and Illustrator throughout.',
            'result' => 'Increased engagement by 40%.',
        ]);

        $this->fakeStructured([
            'summary' => 'Process reads as tool-centric; Result cites an unconfirmed metric.',
            'strengths' => ['Problem is specific and grounded.'],
            'issues' => [
                ['field' => 'process', 'type' => 'too_tool_centric', 'message' => 'Lists tools without explaining decisions.', 'severity' => 'medium'],
                ['field' => 'result', 'type' => 'unsupported_metric', 'message' => '"40%" has no confirmed source.', 'severity' => 'high'],
            ],
            'recommended_actions' => ['Refine Process to focus on decisions, not tools.'],
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.project-quality-review', $project))
            ->assertOk();

        $this->assertCount(2, $response->json('issues'));
        $this->assertSame('high', $response->json('issues.1.severity'));

        $fresh = $project->fresh();
        $this->assertSame('Used Photoshop and Illustrator throughout.', $fresh->process);
        $this->assertSame('Increased engagement by 40%.', $fresh->result);
    }

    public function test_quality_review_rejects_project_with_no_case_study_text(): void
    {
        $project = $this->project(['description' => null, 'problem' => null, 'process' => null, 'result' => null]);

        $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.project-quality-review', $project))
            ->assertStatus(422);
    }

    // --- SEO Assistant (Sections 43-50, 78) ---------------------------------

    public function test_seo_suggestions_are_returned_but_never_persisted(): void
    {
        $project = $this->project(['title' => 'LuxSuits Branding']);
        $this->fakeStructured([
            'title' => 'LuxSuits Branding | Surya Andika',
            'meta_description' => 'Visual identity and packaging for LuxSuits, a formal-wear rental brand.',
            'social_description' => 'A look at the branding work for LuxSuits.',
            'notes' => [],
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.project-seo', $project))
            ->assertOk()
            ->assertJsonStructure(['title', 'meta_description', 'social_description', 'notes']);

        // No seo_title/seo_description column exists — nothing to assert
        // was NOT written beyond confirming the Project itself is
        // unchanged (the response is the only place this data ever lives).
        $this->assertSame('LuxSuits Branding', $project->fresh()->title);
        $this->assertSame('LuxSuits Branding | Surya Andika', $response->json('title'));
    }

    public function test_seo_rejects_when_no_title_available(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.projects.assistant.seo'), [])
            ->assertStatus(422);
    }

    // --- Cover ranking (Sections 22-26, upgraded from V1.1) -----------------

    public function test_cover_ranking_returns_ranked_candidates(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/a.jpg', 'fake-bytes');
        Storage::disk('public')->put('projects/b.jpg', 'fake-bytes');

        $project = $this->project(['gallery_images' => ['storage/projects/a.jpg', 'storage/projects/b.jpg']]);
        $this->fakeStructured(['rankings' => [
            ['index' => 0, 'rank' => 1, 'reason' => 'Clearest subject.'],
            ['index' => 1, 'rank' => 2, 'reason' => 'Good alternative.'],
        ]]);

        $response = $this->actingAs(User::factory()->create())->post(
            route('admin.projects.assistant.project-cover', $project),
            ['existing_images' => ['storage/projects/a.jpg', 'storage/projects/b.jpg']],
        )->assertOk();

        $this->assertSame(1, $response->json('rankings.0.rank'));
        $this->assertSame('storage/projects/a.jpg', $response->json('rankings.0.path'));
    }

    // --- Rate limiting (shared limiter, Section 55) -------------------------

    public function test_v12_endpoints_share_the_existing_rate_limit(): void
    {
        $this->fakeStructured(['suggested_tools' => []]);
        $user = $this->actingAs(User::factory()->create());

        $lastResponse = null;
        for ($i = 0; $i < 21; $i++) {
            $lastResponse = $user->post(route('admin.projects.assistant.tools'), []);
        }

        $lastResponse->assertStatus(429);
    }
}
