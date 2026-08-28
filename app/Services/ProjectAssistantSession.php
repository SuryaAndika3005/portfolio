<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

/**
 * Transient, server-side state for one active AI Project Assistant
 * conversation (Sections 36-38). No migration/database table exists for
 * this on purpose — a real Project's own columns are the only durable
 * storage the Assistant ever writes to, and only via the normal Apply +
 * Save flow. This class only ever holds small structured data (facts,
 * the last understanding/draft snapshot, a capped conversation transcript,
 * and the Gemini interaction id) — it never stores image bytes/base64
 * (Section 37).
 *
 * Scoped by $contextKey ('create' or "project:{id}") so switching between
 * Create and a specific Project's Edit page — or between two different
 * Projects — never mixes one project's facts/draft into another's. A
 * mismatched context key is treated the same as an explicit Reset.
 */
class ProjectAssistantSession
{
    private const SESSION_KEY = 'ai_assistant';

    private const MAX_TRANSCRIPT_ENTRIES = 24;

    public function __construct(private readonly string $contextKey)
    {
        $state = Session::get(self::SESSION_KEY);

        if (! is_array($state) || ($state['context_key'] ?? null) !== $this->contextKey) {
            $this->reset();
        }
    }

    public function reset(): void
    {
        Session::put(self::SESSION_KEY, [
            'context_key' => $this->contextKey,
            'interaction_id' => null,
            'facts' => [],
            'understanding' => null,
            'draft' => null,
            'transcript' => [],
        ]);
    }

    public function interactionId(): ?string
    {
        return $this->state()['interaction_id'];
    }

    public function setInteractionId(?string $id): void
    {
        $this->put('interaction_id', $id);
    }

    public function facts(): array
    {
        return $this->state()['facts'] ?? [];
    }

    /**
     * Shallow-merges new fact values in. Array-valued facts (visual
     * observations, unknowns, confirmed tools, etc.) are merged and
     * de-duplicated rather than replaced, so earlier turns' context is
     * never silently dropped by a later, partial response.
     */
    public function mergeFacts(array $newFacts): void
    {
        $facts = $this->facts();

        foreach ($newFacts as $key => $value) {
            if (is_array($value) && isset($facts[$key]) && is_array($facts[$key])) {
                $facts[$key] = array_values(array_unique(array_merge($facts[$key], $value), SORT_REGULAR));
            } else {
                $facts[$key] = $value;
            }
        }

        $this->put('facts', $facts);
    }

    /**
     * Exact overwrite for a single fact key — used for values Gemini
     * returns as its complete current list each turn (e.g. "unknowns"),
     * where accumulating/merging stale entries via mergeFacts() would be
     * wrong (an unknown that just got answered must actually disappear).
     */
    public function setFact(string $key, mixed $value): void
    {
        $facts = $this->facts();
        $facts[$key] = $value;
        $this->put('facts', $facts);
    }

    public function understanding(): ?array
    {
        return $this->state()['understanding'];
    }

    public function setUnderstanding(array $understanding): void
    {
        $this->put('understanding', $understanding);
    }

    public function draft(): ?array
    {
        return $this->state()['draft'];
    }

    public function setDraft(array $draft): void
    {
        $this->put('draft', $draft);
    }

    public function transcript(): array
    {
        return $this->state()['transcript'] ?? [];
    }

    public function appendTranscript(string $role, string $text): void
    {
        $transcript = $this->transcript();
        $transcript[] = ['role' => $role, 'text' => $text];

        if (count($transcript) > self::MAX_TRANSCRIPT_ENTRIES) {
            $transcript = array_slice($transcript, -self::MAX_TRANSCRIPT_ENTRIES);
        }

        $this->put('transcript', $transcript);
    }

    private function state(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    private function put(string $key, mixed $value): void
    {
        $state = $this->state();
        $state[$key] = $value;
        Session::put(self::SESSION_KEY, $state);
    }
}
