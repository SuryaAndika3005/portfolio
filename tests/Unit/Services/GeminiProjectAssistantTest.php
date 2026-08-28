<?php

namespace Tests\Unit\Services;

use App\Services\Gemini\GeminiClient;
use App\Services\Gemini\GeminiException;
use App\Services\GeminiProjectAssistant;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Exercises GeminiProjectAssistant's schema validation and shape-handling
 * against mocked Interactions API responses (Section 87 — no real Gemini
 * quota spent). Uses the real GeminiClient underneath (also mocked at the
 * HTTP layer) so the whole normalize -> assertShape pipeline is covered,
 * not just the assistant's own prompt-building.
 */
class GeminiProjectAssistantTest extends TestCase
{
    private function assistant(): GeminiProjectAssistant
    {
        $client = new GeminiClient(apiKey: 'fake-key', model: 'gemini-3.7-flash', timeout: 5);

        return new GeminiProjectAssistant($client);
    }

    private function fakeStructured(array $data, string $id = 'v1_test'): void
    {
        Http::fake([
            '*/interactions' => Http::response([
                'id' => $id,
                'steps' => [['type' => 'model_output', 'content' => [['type' => 'text', 'text' => json_encode($data)]]]],
            ], 200),
        ]);
    }

    public function test_analyze_returns_normalized_understanding(): void
    {
        $this->fakeStructured([
            'confirmed_facts' => ['Title: Coffee Poster'],
            'visual_observations' => ['Warm color palette'],
            'inferences_needing_confirmation' => ['Likely aimed at local customers'],
            'unknowns' => ['Client name'],
            'understanding' => [
                'visual_direction' => 'observed',
                'role' => 'needs_context',
                'objective' => 'partial',
                'main_challenge' => 'needs_context',
                'process' => 'needs_context',
                'outputs' => 'observed',
                'measured_results' => 'not_available',
            ],
            'questions' => ['Who was the client?'],
            'ready_for_draft' => false,
        ]);

        $result = $this->assistant()->analyze(['project_name' => 'Coffee Poster'], []);

        $this->assertSame('v1_test', $result['interaction_id']);
        $this->assertSame(['Title: Coffee Poster'], $result['confirmed_facts']);
        $this->assertFalse($result['ready_for_draft']);
        $this->assertSame('observed', $result['understanding']['visual_direction']);
    }

    public function test_analyze_rejects_response_missing_required_key(): void
    {
        $this->fakeStructured([
            'confirmed_facts' => [],
            'visual_observations' => [],
            'inferences_needing_confirmation' => [],
            'unknowns' => [],
            // 'understanding' deliberately omitted
            'questions' => [],
            'ready_for_draft' => true,
        ]);

        $this->expectException(GeminiException::class);
        $this->assistant()->analyze([], []);
    }

    public function test_analyze_rejects_response_with_wrong_type(): void
    {
        $this->fakeStructured([
            'confirmed_facts' => 'should be an array, not a string',
            'visual_observations' => [],
            'inferences_needing_confirmation' => [],
            'unknowns' => [],
            'understanding' => [
                'visual_direction' => 'observed', 'role' => 'observed', 'objective' => 'observed',
                'main_challenge' => 'observed', 'process' => 'observed', 'outputs' => 'observed', 'measured_results' => 'observed',
            ],
            'questions' => [],
            'ready_for_draft' => true,
        ]);

        $this->expectException(GeminiException::class);
        $this->assistant()->analyze([], []);
    }

    public function test_analyze_rejects_invalid_enum_value(): void
    {
        $this->fakeStructured([
            'confirmed_facts' => [], 'visual_observations' => [], 'inferences_needing_confirmation' => [], 'unknowns' => [],
            'understanding' => [
                'visual_direction' => 'definitely_maybe', // not a real enum value
                'role' => 'observed', 'objective' => 'observed', 'main_challenge' => 'observed',
                'process' => 'observed', 'outputs' => 'observed', 'measured_results' => 'observed',
            ],
            'questions' => [], 'ready_for_draft' => true,
        ]);

        $this->expectException(GeminiException::class);
        $this->assistant()->analyze([], []);
    }

    public function test_generate_draft_returns_bilingual_fields(): void
    {
        $draft = [
            'en' => ['description' => 'A poster.', 'problem' => 'Needed a poster.', 'process' => 'Designed it.', 'result' => 'Delivered a poster.'],
            'id' => ['description' => 'Sebuah poster.', 'problem' => 'Butuh poster.', 'process' => 'Mendesainnya.', 'result' => 'Poster selesai.'],
            'assumptions' => [],
            'missing_information' => ['Client name'],
            'fact_corrections' => [],
        ];
        $this->fakeStructured($draft);

        $result = $this->assistant()->generateDraft(['project_name' => 'Test'], 'v1_prev');

        $this->assertSame('A poster.', $result['en']['description']);
        $this->assertSame('Sebuah poster.', $result['id']['description']);
        $this->assertSame(['Client name'], $result['missing_information']);
    }

    public function test_generate_draft_rejects_missing_language_block(): void
    {
        $this->fakeStructured([
            'en' => ['description' => 'x', 'problem' => 'x', 'process' => 'x', 'result' => 'x'],
            // 'id' missing entirely
            'assumptions' => [], 'missing_information' => [], 'fact_corrections' => [],
        ]);

        $this->expectException(GeminiException::class);
        $this->assistant()->generateDraft([], 'v1_prev');
    }

    public function test_rank_covers_rejects_out_of_range_index(): void
    {
        $this->fakeStructured(['rankings' => [['index' => 5, 'rank' => 1, 'reason' => 'looks nice']]]);

        $images = [
            new \App\Services\Gemini\ImageInput(bytes: 'a', mimeType: 'image/jpeg', label: 'One'),
            new \App\Services\Gemini\ImageInput(bytes: 'b', mimeType: 'image/jpeg', label: 'Two'),
        ];

        $this->expectException(GeminiException::class);
        $this->assistant()->rankCovers($images, []);
    }

    public function test_rank_covers_rejects_duplicate_index(): void
    {
        $this->fakeStructured(['rankings' => [
            ['index' => 0, 'rank' => 1, 'reason' => 'a'],
            ['index' => 0, 'rank' => 2, 'reason' => 'b'],
        ]]);

        $images = [
            new \App\Services\Gemini\ImageInput(bytes: 'a', mimeType: 'image/jpeg', label: 'One'),
            new \App\Services\Gemini\ImageInput(bytes: 'b', mimeType: 'image/jpeg', label: 'Two'),
        ];

        $this->expectException(GeminiException::class);
        $this->assistant()->rankCovers($images, []);
    }

    public function test_rank_covers_returns_sorted_rankings(): void
    {
        $this->fakeStructured(['rankings' => [
            ['index' => 1, 'rank' => 2, 'reason' => 'Second best.'],
            ['index' => 0, 'rank' => 1, 'reason' => 'Clearer subject at thumbnail size.'],
        ]]);

        $images = [
            new \App\Services\Gemini\ImageInput(bytes: 'a', mimeType: 'image/jpeg', label: 'One'),
            new \App\Services\Gemini\ImageInput(bytes: 'b', mimeType: 'image/jpeg', label: 'Two'),
        ];

        $result = $this->assistant()->rankCovers($images, []);

        $this->assertSame(1, $result['rankings'][0]['rank']);
        $this->assertSame(0, $result['rankings'][0]['index']);
        $this->assertSame('Clearer subject at thumbnail size.', $result['rankings'][0]['reason']);
        $this->assertSame(2, $result['rankings'][1]['rank']);
    }

    public function test_suggest_gallery_order_rejects_non_permutation(): void
    {
        $this->fakeStructured(['ordered_indices' => [0, 0, 2], 'reasoning_summary' => []]);

        $images = [
            new \App\Services\Gemini\ImageInput(bytes: 'a', mimeType: 'image/jpeg', label: 'One'),
            new \App\Services\Gemini\ImageInput(bytes: 'b', mimeType: 'image/jpeg', label: 'Two'),
            new \App\Services\Gemini\ImageInput(bytes: 'c', mimeType: 'image/jpeg', label: 'Three'),
        ];

        $this->expectException(GeminiException::class);
        $this->assistant()->suggestGalleryOrder($images, []);
    }

    public function test_suggest_gallery_order_accepts_valid_permutation(): void
    {
        $this->fakeStructured([
            'ordered_indices' => [2, 0, 1],
            'reasoning_summary' => [
                ['index' => 2, 'reason' => 'Strong opening visual.'],
                ['index' => 0, 'reason' => 'Detail shot.'],
                ['index' => 1, 'reason' => 'Closing frame.'],
            ],
        ]);

        $images = [
            new \App\Services\Gemini\ImageInput(bytes: 'a', mimeType: 'image/jpeg', label: 'One'),
            new \App\Services\Gemini\ImageInput(bytes: 'b', mimeType: 'image/jpeg', label: 'Two'),
            new \App\Services\Gemini\ImageInput(bytes: 'c', mimeType: 'image/jpeg', label: 'Three'),
        ];

        $result = $this->assistant()->suggestGalleryOrder($images, []);

        $this->assertSame([2, 0, 1], $result['ordered_indices']);
    }

    public function test_suggest_tools_returns_suggestions(): void
    {
        $this->fakeStructured(['suggested_tools' => [
            ['name' => 'Figma', 'reason' => 'Process description mentions prototyping in Figma.'],
        ]]);

        $result = $this->assistant()->suggestTools(['process' => 'Prototyped in Figma before handoff.'], ['Figma', 'Photoshop']);

        $this->assertSame('Figma', $result['suggested_tools'][0]['name']);
    }

    public function test_review_quality_returns_structured_issues(): void
    {
        $this->fakeStructured([
            'summary' => 'Solid overall, Process reads as tool-centric.',
            'strengths' => ['Problem is specific and grounded.'],
            'issues' => [
                ['field' => 'process', 'type' => 'too_tool_centric', 'message' => 'Lists tools without explaining decisions.', 'severity' => 'medium'],
                ['field' => 'result', 'type' => 'unsupported_metric', 'message' => '"increased engagement by 40%" has no confirmed source.', 'severity' => 'high'],
            ],
            'recommended_actions' => ['Refine Process to focus on decisions, not tools.'],
        ]);

        $result = $this->assistant()->reviewQuality(['description' => 'x', 'problem' => 'x', 'process' => 'x', 'result' => 'x']);

        $this->assertCount(2, $result['issues']);
        $this->assertSame('process', $result['issues'][0]['field']);
        $this->assertSame('high', $result['issues'][1]['severity']);
    }

    public function test_generate_seo_returns_structured_suggestions(): void
    {
        $this->fakeStructured([
            'title' => 'LuxSuits Branding | Surya Andika',
            'meta_description' => 'Visual identity and packaging for LuxSuits, a formal-wear rental brand.',
            'social_description' => 'A look at the branding work for LuxSuits.',
            'notes' => [],
        ]);

        $result = $this->assistant()->generateSeo(['project_name' => 'LuxSuits Branding']);

        $this->assertSame('LuxSuits Branding | Surya Andika', $result['title']);
        $this->assertSame([], $result['notes']);
    }

    public function test_reply_carries_fact_corrections_through(): void
    {
        $this->fakeStructured([
            'message' => 'Got it, updating that.',
            'new_confirmed_facts' => [],
            'fact_corrections' => [['about' => 'rebrand', 'correction' => 'The client did not ask for a rebrand.']],
            'updated_unknowns' => [],
            'understanding' => [
                'visual_direction' => 'observed', 'role' => 'observed', 'objective' => 'observed',
                'main_challenge' => 'observed', 'process' => 'observed', 'outputs' => 'observed', 'measured_results' => 'observed',
            ],
            'next_questions' => [],
            'ready_for_draft' => true,
        ]);

        $result = $this->assistant()->reply('Actually it was not a rebrand.', 'v1_prev', []);

        $this->assertCount(1, $result['fact_corrections']);
        $this->assertSame('rebrand', $result['fact_corrections'][0]['about']);
    }

    // --- Provider-usage audit (V1.3): model routing --------------------

    /**
     * suggestTools/reviewQuality/generateSeo never accept a
     * previousInteractionId — they're always a fresh, independent,
     * text-only turn, which is exactly what makes routing them to the
     * lighter configured model safe (Sections 4-6).
     */
    public function test_suggest_tools_uses_the_configured_light_model(): void
    {
        $this->fakeStructured(['suggested_tools' => []]);

        $this->assistant()->suggestTools([], ['Figma']);

        Http::assertSent(fn ($request) => $request['model'] === config('services.gemini.light_model'));
    }

    public function test_review_quality_uses_the_configured_light_model(): void
    {
        $this->fakeStructured([
            'summary' => 'x', 'strengths' => [], 'issues' => [], 'recommended_actions' => [],
        ]);

        $this->assistant()->reviewQuality(['description' => 'x']);

        Http::assertSent(fn ($request) => $request['model'] === config('services.gemini.light_model'));
    }

    public function test_generate_seo_uses_the_configured_light_model(): void
    {
        $this->fakeStructured(['title' => 'x', 'meta_description' => 'x', 'social_description' => 'x', 'notes' => []]);

        $this->assistant()->generateSeo(['project_name' => 'x']);

        Http::assertSent(fn ($request) => $request['model'] === config('services.gemini.light_model'));
    }

    /**
     * Every operation that opens or continues a previous_interaction_id
     * chain must stay on the client's own (default) model, never the
     * lighter one — switching models inside a live conversation chain is
     * not something this app assumes is safe (Section 5).
     */
    public function test_analyze_uses_the_default_model(): void
    {
        $this->fakeStructured([
            'confirmed_facts' => [], 'visual_observations' => [], 'inferences_needing_confirmation' => [], 'unknowns' => [],
            'understanding' => [
                'visual_direction' => 'observed', 'role' => 'observed', 'objective' => 'observed',
                'main_challenge' => 'observed', 'process' => 'observed', 'outputs' => 'observed', 'measured_results' => 'observed',
            ],
            'questions' => [], 'ready_for_draft' => true,
        ]);

        $this->assistant()->analyze([], []);

        Http::assertSent(fn ($request) => $request['model'] === 'gemini-3.7-flash');
    }

    public function test_reply_continuing_a_chain_uses_the_default_model_not_the_light_model(): void
    {
        $this->fakeStructured([
            'message' => 'ok', 'new_confirmed_facts' => [], 'fact_corrections' => [], 'updated_unknowns' => [],
            'understanding' => [
                'visual_direction' => 'observed', 'role' => 'observed', 'objective' => 'observed',
                'main_challenge' => 'observed', 'process' => 'observed', 'outputs' => 'observed', 'measured_results' => 'observed',
            ],
            'next_questions' => [], 'ready_for_draft' => true,
        ]);

        $this->assistant()->reply('hi', 'v1_prev', []);

        Http::assertSent(fn ($request) => $request['model'] === 'gemini-3.7-flash'
            && $request['previous_interaction_id'] === 'v1_prev');
    }

    public function test_refine_continuing_a_chain_uses_the_default_model_not_the_light_model(): void
    {
        $draft = [
            'en' => ['description' => 'x', 'problem' => 'x', 'process' => 'x', 'result' => 'x'],
            'id' => ['description' => 'x', 'problem' => 'x', 'process' => 'x', 'result' => 'x'],
            'assumptions' => [], 'missing_information' => [], 'fact_corrections' => [],
        ];
        $this->fakeStructured($draft);

        $this->assistant()->refine('shorter please', $draft, [], 'v1_prev');

        Http::assertSent(fn ($request) => $request['model'] === 'gemini-3.7-flash'
            && $request['previous_interaction_id'] === 'v1_prev');
    }

    public function test_rank_covers_continuing_a_chain_uses_the_default_model_not_the_light_model(): void
    {
        $this->fakeStructured(['rankings' => [['index' => 0, 'rank' => 1, 'reason' => 'clear']]]);

        $images = [
            new \App\Services\Gemini\ImageInput(bytes: 'a', mimeType: 'image/jpeg', label: 'One'),
            new \App\Services\Gemini\ImageInput(bytes: 'b', mimeType: 'image/jpeg', label: 'Two'),
        ];

        $this->assistant()->rankCovers($images, [], 'v1_prev');

        Http::assertSent(fn ($request) => $request['model'] === 'gemini-3.7-flash'
            && $request['previous_interaction_id'] === 'v1_prev');
    }
}
