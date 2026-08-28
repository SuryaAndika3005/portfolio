<?php

namespace App\Services\Gemini;

use Exception;

/**
 * Carries a message that is always safe to show directly to the Admin user
 * (never a raw provider payload, stack trace, or API key) plus optional
 * technical context for the log entry the controller writes separately.
 */
class GeminiException extends Exception
{
    public function __construct(
        string $userMessage,
        public readonly ?string $technicalReason = null,
        public readonly ?int $statusCode = null,
    ) {
        parent::__construct($userMessage);
    }
}
