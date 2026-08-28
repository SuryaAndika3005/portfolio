<?php

namespace Tests\Unit\Services\Gemini;

use App\Services\Gemini\GeminiClient;
use App\Services\Gemini\GeminiException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Every case here is mocked via Http::fake() -- no real Gemini quota is
 * spent running this suite (Section 87). GeminiClient is constructed
 * directly (not resolved from the container) so each test controls its
 * own timeout/model without touching real config/env.
 */
class GeminiClientTest extends TestCase
{
    private function client(int $timeout = 5): GeminiClient
    {
        return new GeminiClient(
            apiKey: 'fake-test-key',
            model: 'gemini-3.7-flash',
            baseUrl: 'https://generativelanguage.googleapis.com/v1beta',
            apiRevision: '2026-05-20',
            timeout: $timeout,
        );
    }

    public function test_successful_text_request_returns_id_and_text(): void
    {
        Http::fake([
            '*/interactions' => Http::response([
                'id' => 'v1_abc123',
                'status' => 'completed',
                'steps' => [
                    ['type' => 'model_output', 'content' => [['type' => 'text', 'text' => 'Hello back']]],
                ],
            ], 200),
        ]);

        $result = $this->client()->send('Hello', 'test_op');

        $this->assertSame('v1_abc123', $result['id']);
        $this->assertSame('Hello back', $result['text']);
        $this->assertNull($result['data']);
        Http::assertSentCount(1);
    }

    public function test_structured_output_is_json_decoded(): void
    {
        Http::fake([
            '*/interactions' => Http::response([
                'id' => 'v1_xyz',
                'steps' => [
                    ['type' => 'model_output', 'content' => [['type' => 'text', 'text' => '{"foo":"bar","n":3}']]],
                ],
            ], 200),
        ]);

        $result = $this->client()->send('Give me JSON', 'test_op', responseSchema: ['type' => 'object']);

        $this->assertSame(['foo' => 'bar', 'n' => 3], $result['data']);
    }

    public function test_request_sends_correct_headers_and_payload(): void
    {
        Http::fake([
            '*/interactions' => Http::response([
                'id' => 'v1_1',
                'steps' => [['type' => 'model_output', 'content' => [['type' => 'text', 'text' => 'ok']]]],
            ], 200),
        ]);

        $this->client()->send('hi', 'test_op', systemInstruction: 'be nice', previousInteractionId: 'v1_prev');

        Http::assertSent(function ($request) {
            return $request->hasHeader('x-goog-api-key', 'fake-test-key')
                && $request->hasHeader('Api-Revision', '2026-05-20')
                && $request['model'] === 'gemini-3.7-flash'
                && $request['input'] === 'hi'
                && $request['system_instruction'] === 'be nice'
                && $request['previous_interaction_id'] === 'v1_prev';
        });
    }

    public function test_malformed_json_in_structured_output_fails_safely(): void
    {
        Http::fake([
            '*/interactions' => Http::response([
                'id' => 'v1_bad',
                'steps' => [['type' => 'model_output', 'content' => [['type' => 'text', 'text' => 'not valid json{{{']]]],
            ], 200),
        ]);

        $this->expectException(GeminiException::class);
        $this->client()->send('x', 'test_op', responseSchema: ['type' => 'object']);
    }

    public function test_missing_model_output_step_fails_safely(): void
    {
        Http::fake([
            '*/interactions' => Http::response(['id' => 'v1_empty', 'steps' => []], 200),
        ]);

        $this->expectException(GeminiException::class);
        $this->client()->send('x', 'test_op');
    }

    public function test_missing_interaction_id_fails_safely(): void
    {
        Http::fake([
            '*/interactions' => Http::response([
                'steps' => [['type' => 'model_output', 'content' => [['type' => 'text', 'text' => 'hi']]]],
            ], 200),
        ]);

        $this->expectException(GeminiException::class);
        $this->client()->send('x', 'test_op');
    }

    public function test_missing_api_key_fails_without_a_network_call(): void
    {
        Http::fake();

        $client = new GeminiClient(apiKey: null, model: 'gemini-3.7-flash');
        $this->expectException(GeminiException::class);

        try {
            $client->send('x', 'test_op');
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_400_is_not_retried(): void
    {
        Http::fake(['*/interactions' => Http::response(['error' => ['message' => 'bad request']], 400)]);

        try {
            $this->client()->send('x', 'test_op');
            $this->fail('Expected GeminiException');
        } catch (GeminiException $e) {
            Http::assertSentCount(1);
        }
    }

    public function test_401_is_not_retried(): void
    {
        Http::fake(['*/interactions' => Http::response(['error' => ['message' => 'unauthorized']], 401)]);

        try {
            $this->client()->send('x', 'test_op');
            $this->fail('Expected GeminiException');
        } catch (GeminiException $e) {
            Http::assertSentCount(1);
        }
    }

    /**
     * Provider-usage audit (V1.3, Section 2): a 429 with no Retry-After
     * header and no error.details[].retryDelay hint is not retried at
     * all — retrying blindly here is exactly the burst behavior that
     * worsens Free Tier RPM exhaustion, so this now fails on the first
     * attempt instead of hammering the endpoint two more times.
     */
    public function test_429_without_a_retry_hint_is_not_retried(): void
    {
        Http::fake(['*/interactions' => Http::response(['error' => ['message' => 'rate limited']], 429)]);

        try {
            $this->client()->send('x', 'test_op');
            $this->fail('Expected GeminiException');
        } catch (GeminiException $e) {
            $this->assertStringContainsString('too many requests', strtolower($e->getMessage()));
            Http::assertSentCount(1);
        }
    }

    public function test_429_with_a_short_retry_after_header_retries_once_and_recovers(): void
    {
        Http::fakeSequence('*/interactions')
            ->push(['error' => ['message' => 'rate limited']], 429, ['Retry-After' => '1'])
            ->push([
                'id' => 'v1_recovered',
                'steps' => [['type' => 'model_output', 'content' => [['type' => 'text', 'text' => 'ok']]]],
            ], 200);

        $result = $this->client()->send('x', 'test_op');

        $this->assertSame('v1_recovered', $result['id']);
        Http::assertSentCount(2);
    }

    public function test_429_with_a_short_retryDelay_in_error_details_retries_once_and_recovers(): void
    {
        Http::fakeSequence('*/interactions')
            ->push(['error' => ['message' => 'rate limited', 'details' => [
                ['@type' => 'type.googleapis.com/google.rpc.RetryInfo', 'retryDelay' => '1s'],
            ]]], 429)
            ->push([
                'id' => 'v1_recovered',
                'steps' => [['type' => 'model_output', 'content' => [['type' => 'text', 'text' => 'ok']]]],
            ], 200);

        $result = $this->client()->send('x', 'test_op');

        $this->assertSame('v1_recovered', $result['id']);
        Http::assertSentCount(2);
    }

    public function test_429_with_a_retry_after_longer_than_the_cap_is_not_retried(): void
    {
        Http::fake(['*/interactions' => Http::response(['error' => ['message' => 'rate limited']], 429, ['Retry-After' => '30'])]);

        try {
            $this->client()->send('x', 'test_op');
            $this->fail('Expected GeminiException');
        } catch (GeminiException $e) {
            Http::assertSentCount(1);
        }
    }

    /**
     * Even when every response keeps offering a short, retryable wait, a
     * single user action must never create a growing burst of retries
     * (Section 2's explicit requirement) — exactly one retry, ever, per
     * 429 streak.
     */
    public function test_429_is_never_retried_more_than_once_even_with_a_hint_each_time(): void
    {
        Http::fake(['*/interactions' => Http::response(['error' => ['message' => 'still rate limited']], 429, ['Retry-After' => '1'])]);

        try {
            $this->client()->send('x', 'test_op');
            $this->fail('Expected GeminiException');
        } catch (GeminiException $e) {
            Http::assertSentCount(2);
        }
    }

    public function test_429_does_not_mutate_or_expose_provider_internals(): void
    {
        Http::fake(['*/interactions' => Http::response(['error' => ['message' => 'rate limited, key=SECRET123']], 429)]);

        try {
            $this->client()->send('x', 'test_op');
            $this->fail('Expected GeminiException');
        } catch (GeminiException $e) {
            $this->assertStringNotContainsString('SECRET123', $e->getMessage());
        }
    }

    public function test_5xx_maps_to_a_safe_message_and_never_leaks_provider_payload(): void
    {
        Http::fake(['*/interactions' => Http::response(['error' => ['message' => 'internal server explosion, key=SECRET123']], 500)]);

        try {
            $this->client()->send('x', 'test_op');
            $this->fail('Expected GeminiException');
        } catch (GeminiException $e) {
            $this->assertStringNotContainsString('SECRET123', $e->getMessage());
            $this->assertStringContainsString('Project form has not been changed', $e->getMessage());
        }
    }

    /**
     * Provider-usage audit (V1.3, Section 2): "Do not weaken retries for
     * transient 5xx/network failures unless necessary" — only 429 got a
     * more conservative policy; a genuinely transient 5xx still gets the
     * original 3-attempt behavior, unchanged.
     */
    public function test_5xx_is_still_retried_up_to_three_attempts(): void
    {
        Http::fake(['*/interactions' => Http::response(['error' => ['message' => 'unavailable']], 503)]);

        try {
            $this->client()->send('x', 'test_op');
            $this->fail('Expected GeminiException');
        } catch (GeminiException $e) {
            Http::assertSentCount(3);
        }
    }

    public function test_withModel_returns_a_new_client_for_a_different_model_only(): void
    {
        Http::fake(['*/interactions' => Http::response([
            'id' => 'v1_light',
            'steps' => [['type' => 'model_output', 'content' => [['type' => 'text', 'text' => 'ok']]]],
        ], 200)]);

        $lightClient = $this->client()->withModel('gemini-3.5-flash-lite');
        $lightClient->send('x', 'test_op');

        $this->assertSame('gemini-3.5-flash-lite', $lightClient->model());
        $this->assertSame('gemini-3.7-flash', $this->client()->model());
        Http::assertSent(fn ($request) => $request['model'] === 'gemini-3.5-flash-lite'
            && $request->hasHeader('x-goog-api-key', 'fake-test-key'));
    }

    /**
     * A known Flash-Lite-generation reliability quirk (structured output
     * occasionally wrapped in a ```json fence instead of bare JSON) must
     * not break decoding — this is what makes routing light, independent
     * operations to a Flash-Lite model safe rather than merely assumed
     * safe (provider-usage audit, Section 3).
     */
    public function test_structured_output_wrapped_in_a_markdown_fence_still_decodes(): void
    {
        Http::fake([
            '*/interactions' => Http::response([
                'id' => 'v1_fenced',
                'steps' => [['type' => 'model_output', 'content' => [
                    ['type' => 'text', 'text' => "```json\n{\"foo\":\"bar\"}\n```"],
                ]]],
            ], 200),
        ]);

        $result = $this->client()->send('x', 'test_op', responseSchema: ['type' => 'object']);

        $this->assertSame(['foo' => 'bar'], $result['data']);
    }
}
