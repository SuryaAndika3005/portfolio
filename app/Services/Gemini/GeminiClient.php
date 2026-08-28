<?php

namespace App\Services\Gemini;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Thin, dedicated transport for the Gemini Interactions API
 * (https://ai.google.dev/gemini-api/docs/interactions-overview). Owns only
 * the HTTP call, retry policy, and response-envelope parsing (the
 * steps[]/content[] wrapper introduced by the API's May 2026 revision) —
 * it has no idea what a "Project" is. GeminiProjectAssistant builds on top
 * of this with the actual case-study prompts/schemas.
 *
 * The API key is read once from config (server-side only) and never
 * appears in a log line, an exception message, or a response returned to
 * the browser.
 */
class GeminiClient
{
    // 429 is deliberately NOT in this list — rate-limit responses get their
    // own, more conservative policy below (see sendWithRetry()). Retrying a
    // 429 the same way as a transient 5xx is exactly what worsens RPM
    // exhaustion on the Free Tier: a burst of retries against an endpoint
    // that is already over its per-minute quota only makes the next
    // request more likely to 429 too.
    private const RETRYABLE_STATUSES = [500, 502, 503, 504];

    private const MAX_ATTEMPTS = 3; // 1 initial + 2 retries — 5xx/connection failures only, unchanged.

    private const RETRY_BACKOFF_MS = [400, 900];

    // A 429 is retried at most once, and only when Gemini's own response
    // tells us how long to wait (a Retry-After header, or the Google-style
    // error.details[].retryDelay field) AND that wait is short enough to
    // hold a synchronous web request open for. A longer stated wait, or no
    // wait hint at all, means failing gracefully immediately instead of
    // guessing — there is no way to "guess well" here, and guessing wrong
    // is the burst behavior this exists to prevent.
    private const RATE_LIMIT_MAX_RETRY_WAIT_SECONDS = 3.0;

    public function __construct(
        private readonly ?string $apiKey = null,
        private readonly string $model = 'gemini-3.7-flash',
        private readonly string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta',
        private readonly string $apiRevision = '2026-05-20',
        private readonly int $timeout = 45,
    ) {
    }

    public function model(): string
    {
        return $this->model;
    }

    /**
     * Returns a new client for a different model, keeping every other
     * transport setting (key/base URL/revision/timeout) identical. The one
     * place model routing is actually decided (GeminiProjectAssistant's
     * constructor) uses this instead of a second container binding, so
     * every model name still comes from one place — config/services.php —
     * never hardcoded in a controller (V1.3 provider-usage audit, Section 6).
     */
    public function withModel(string $model): self
    {
        return new self(
            apiKey: $this->apiKey,
            model: $model,
            baseUrl: $this->baseUrl,
            apiRevision: $this->apiRevision,
            timeout: $this->timeout,
        );
    }

    /**
     * Sends one Interactions API request and returns the model's text
     * output, already extracted from the steps[]/content[] envelope and
     * (when $expectJson is true) json_decode()'d and validated to be an
     * array. Never returns provider internals to the caller beyond the
     * interaction id, which callers persist to continue the conversation
     * via previous_interaction_id.
     *
     * @param  array  $input  Either a plain string or an array of content
     *                        parts (text/image), per the Interactions API.
     * @param  array|null  $responseSchema  JSON Schema; when given, the
     *                                      request asks for
     *                                      application/json output and
     *                                      the return value's ['data']
     *                                      is the decoded array.
     * @return array{id: string, text: ?string, data: ?array}
     *
     * @throws GeminiException
     */
    public function send(
        string|array $input,
        string $operation,
        ?string $systemInstruction = null,
        ?string $previousInteractionId = null,
        ?array $responseSchema = null,
    ): array {
        if (blank($this->apiKey)) {
            throw new GeminiException(
                'The AI Assistant is not configured yet. Ask the site owner to add a Gemini API key.',
                technicalReason: 'GOOGLE_API_KEY is not set.',
            );
        }

        $payload = ['model' => $this->model, 'input' => $input];

        if ($systemInstruction !== null) {
            $payload['system_instruction'] = $systemInstruction;
        }

        if ($previousInteractionId !== null) {
            $payload['previous_interaction_id'] = $previousInteractionId;
        }

        if ($responseSchema !== null) {
            $payload['response_format'] = [
                'type' => 'text',
                'mime_type' => 'application/json',
                'schema' => $responseSchema,
            ];
        }

        $requestId = (string) Str::uuid();
        $startedAt = microtime(true);

        [$response, $attempts] = $this->sendWithRetry($payload, $operation, $requestId);

        $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

        if (! $response->successful()) {
            $this->logFailure($operation, $requestId, $response->status(), $durationMs, $attempts);

            throw $this->exceptionForStatus($response->status(), $response->json('error.message'));
        }

        $body = $response->json();

        Log::info('gemini.request', [
            'operation' => $operation,
            'request_id' => $requestId,
            'model' => $this->model,
            'status' => $response->status(),
            'attempts' => $attempts,
            'duration_ms' => $durationMs,
            'interaction_id' => $body['id'] ?? null,
        ]);

        // Dev-only, per-attempt trace (Section 10 of the provider-usage
        // audit) — deliberately at debug level, not info/warning, so a
        // production LOG_LEVEL of info/warning (the normal default) never
        // emits it; only a local/dev setup with LOG_LEVEL=debug sees it.
        // Never includes the request/response body — just enough to
        // answer "what happened" (operation, model, attempt count, final
        // response class) without ever logging image bytes or the API key.
        Log::debug('gemini.attempt_trace', [
            'operation' => $operation,
            'request_id' => $requestId,
            'model' => $this->model,
            'attempts' => $attempts,
            'final_response_class' => $this->responseClass($response),
        ]);

        return $this->normalize($body, $responseSchema !== null);
    }

    /**
     * @return array{0: \Illuminate\Http\Client\Response, 1: int} The final
     *              response and how many HTTP attempts it took to get it.
     */
    private function sendWithRetry(array $payload, string $operation, string $requestId): array
    {
        $lastResponse = null;
        $rateLimitRetried = false;

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            try {
                $lastResponse = Http::withHeaders([
                    'x-goog-api-key' => $this->apiKey,
                    'Api-Revision' => $this->apiRevision,
                ])
                    ->timeout($this->timeout)
                    ->connectTimeout(10)
                    ->asJson()
                    ->post("{$this->baseUrl}/interactions", $payload);
            } catch (ConnectionException $e) {
                if ($attempt === self::MAX_ATTEMPTS) {
                    Log::warning('gemini.connection_failed', [
                        'operation' => $operation,
                        'request_id' => $requestId,
                        'attempt' => $attempt,
                    ]);

                    throw new GeminiException(
                        'AI request timed out. Please try again.',
                        technicalReason: 'ConnectionException after '.$attempt.' attempts',
                    );
                }

                usleep(self::RETRY_BACKOFF_MS[$attempt - 1] * 1000);

                continue;
            }

            if ($lastResponse->successful()) {
                return [$lastResponse, $attempt];
            }

            if ($lastResponse->status() === 429) {
                // At most one retry, and only when Gemini itself told us a
                // short, specific wait would help (Section 2). A second
                // 429 after that one retry fails immediately — never a
                // growing burst.
                if ($rateLimitRetried) {
                    return [$lastResponse, $attempt];
                }

                $waitSeconds = $this->rateLimitRetryWait($lastResponse);

                if ($waitSeconds === null) {
                    Log::info('gemini.rate_limited_no_retry', [
                        'operation' => $operation,
                        'request_id' => $requestId,
                        'reason' => 'no short Retry-After/retryDelay hint in the response',
                    ]);

                    return [$lastResponse, $attempt];
                }

                $rateLimitRetried = true;
                Log::info('gemini.rate_limited_retry', [
                    'operation' => $operation,
                    'request_id' => $requestId,
                    'wait_seconds' => $waitSeconds,
                ]);
                usleep((int) round($waitSeconds * 1_000_000));

                continue;
            }

            if (! in_array($lastResponse->status(), self::RETRYABLE_STATUSES, true)) {
                return [$lastResponse, $attempt];
            }

            if ($attempt === self::MAX_ATTEMPTS) {
                return [$lastResponse, $attempt];
            }

            usleep(self::RETRY_BACKOFF_MS[$attempt - 1] * 1000);
        }

        return [$lastResponse, self::MAX_ATTEMPTS];
    }

    /**
     * Reads how long Gemini itself says to wait before trying again — the
     * standard HTTP `Retry-After` header first, then the Google API error
     * envelope's `error.details[].retryDelay` (the format its
     * RESOURCE_EXHAUSTED errors actually use, e.g. "17s"). Returns null
     * (meaning: do not retry) when neither is present, or when the stated
     * wait is longer than we're willing to hold a synchronous request open
     * for — a long wait is still respected, just not by blocking here.
     */
    private function rateLimitRetryWait(\Illuminate\Http\Client\Response $response): ?float
    {
        $header = $response->header('Retry-After');

        if (is_string($header) && $header !== '' && is_numeric($header)) {
            return $this->withinRetryCap((float) $header);
        }

        foreach ((array) $response->json('error.details', []) as $detail) {
            $retryDelay = $detail['retryDelay'] ?? null;

            if (is_string($retryDelay) && preg_match('/^(\d+(?:\.\d+)?)s$/', $retryDelay, $matches)) {
                return $this->withinRetryCap((float) $matches[1]);
            }
        }

        return null;
    }

    private function withinRetryCap(float $seconds): ?float
    {
        return $seconds > 0 && $seconds <= self::RATE_LIMIT_MAX_RETRY_WAIT_SECONDS ? $seconds : null;
    }

    private function responseClass(\Illuminate\Http\Client\Response $response): string
    {
        return match (true) {
            $response->successful() => 'success',
            $response->status() === 429 => 'rate_limited',
            $response->status() >= 500 => 'server_error',
            default => 'client_error',
        };
    }

    /**
     * Extracts the final model_output text from the steps[] envelope and,
     * for structured-output requests, json_decode()s + shape-checks it.
     * Never trusts the response blindly (Section 80) — a response that
     * doesn't match the expected envelope fails safely via GeminiException
     * rather than surfacing a partial/garbled result to the Admin.
     */
    private function normalize(array $body, bool $expectJson): array
    {
        $interactionId = $body['id'] ?? null;

        if (! is_string($interactionId) || $interactionId === '') {
            throw new GeminiException(
                'AI Assistant received an unexpected response. Your Project form has not been changed.',
                technicalReason: 'Missing interaction id in response body.',
            );
        }

        $text = null;

        foreach (array_reverse($body['steps'] ?? []) as $step) {
            if (($step['type'] ?? null) !== 'model_output') {
                continue;
            }

            foreach ($step['content'] ?? [] as $part) {
                if (($part['type'] ?? null) === 'text' && is_string($part['text'] ?? null)) {
                    $text = $part['text'];
                    break 2;
                }
            }
        }

        if ($text === null) {
            throw new GeminiException(
                'AI Assistant did not return a usable response. Your Project form has not been changed.',
                technicalReason: 'No model_output text step found.',
            );
        }

        if (! $expectJson) {
            return ['id' => $interactionId, 'text' => $text, 'data' => null];
        }

        $decoded = json_decode($this->stripMarkdownFence($text), true);

        if (! is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            throw new GeminiException(
                'AI Assistant returned a response that could not be understood. Your Project form has not been changed.',
                technicalReason: 'json_decode failed for structured output: '.json_last_error_msg(),
            );
        }

        return ['id' => $interactionId, 'text' => $text, 'data' => $decoded];
    }

    /**
     * Some Flash-Lite-generation models are documented to occasionally wrap
     * structured-output text in a ```json ... ``` fence instead of returning
     * bare JSON, even when response_format asks for application/json — a
     * known reliability quirk, not something this app can prevent
     * server-side. Cheap, harmless to strip for every model (a fence never
     * appears in a correctly-formatted response, so this is a no-op there).
     */
    private function stripMarkdownFence(string $text): string
    {
        $trimmed = trim($text);

        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/s', $trimmed, $matches)) {
            return $matches[1];
        }

        return $text;
    }

    private function exceptionForStatus(int $status, ?string $providerMessage): GeminiException
    {
        return match (true) {
            $status === 401, $status === 403 => new GeminiException(
                'The AI Assistant is not authorized to reach Gemini right now.',
                technicalReason: $providerMessage,
                statusCode: $status,
            ),
            $status === 400 => new GeminiException(
                'The AI Assistant could not process that request.',
                technicalReason: $providerMessage,
                statusCode: $status,
            ),
            $status === 429 => new GeminiException(
                'The AI Assistant is receiving too many requests right now. Please wait a moment and try again.',
                technicalReason: $providerMessage,
                statusCode: $status,
            ),
            $status >= 500 => new GeminiException(
                'Gemini could not analyze this project right now. Your Project form has not been changed.',
                technicalReason: $providerMessage,
                statusCode: $status,
            ),
            default => new GeminiException(
                'AI request failed. Your Project form has not been changed.',
                technicalReason: $providerMessage,
                statusCode: $status,
            ),
        };
    }

    private function logFailure(string $operation, string $requestId, int $status, int $durationMs, int $attempts): void
    {
        Log::warning('gemini.request_failed', [
            'operation' => $operation,
            'request_id' => $requestId,
            'model' => $this->model,
            'status' => $status,
            'attempts' => $attempts,
            'duration_ms' => $durationMs,
        ]);
    }
}
