<?php

namespace App\Services\Gemini;

/**
 * One image ready to attach to a Gemini multimodal request — already read
 * into memory as raw bytes (never written to disk by this layer, never
 * stored in the session; see ProjectAssistantSession). $label is a short,
 * human-readable description ("Main visual", "Gallery 3") used only for
 * transparency in the UI/log, never sent to Gemini as part of the image.
 */
readonly class ImageInput
{
    public function __construct(
        public string $bytes,
        public string $mimeType,
        public string $label,
        public ?string $existingPath = null,
    ) {
    }

    public function toRequestPart(): array
    {
        return [
            'type' => 'image',
            'data' => base64_encode($this->bytes),
            'mime_type' => $this->mimeType,
        ];
    }
}
