<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route is behind the 'auth' middleware
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'problem' => ['nullable', 'string'],
            'process' => ['nullable', 'string'],
            'result' => ['nullable', 'string'],
            'role' => ['nullable', 'string', 'max:255'],
            'client' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'string', 'max:4'],
            'tools' => ['nullable', 'array'],
            'tools.*' => ['string', 'max:255'],
            'tools_custom' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:4096'],
            'remove_gallery' => ['nullable', 'array'],
            'remove_gallery.*' => ['string'],
            'is_highlighted' => ['sometimes', 'boolean'],
            'featured_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_highlighted' => $this->boolean('is_highlighted'),
        ]);
    }

    /**
     * Merge the checklist tools with any freeform "other tools" text into
     * the single comma-separated string the `tools` column stores.
     */
    public function toolsString(): ?string
    {
        $checked = collect($this->input('tools', []));

        $custom = collect(explode(',', (string) $this->input('tools_custom')))
            ->map(fn ($t) => trim($t))
            ->filter();

        $merged = $checked->merge($custom)->filter()->unique()->values();

        return $merged->isEmpty() ? null : $merged->implode(', ');
    }
}
