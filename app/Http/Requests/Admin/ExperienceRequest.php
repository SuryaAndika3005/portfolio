<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExperienceRequest extends FormRequest
{
    /**
     * The exact three values already stored in the experiences.category
     * column (verified against live data) — reused, not reinvented. The
     * homepage groups by category === 'Professional Work' vs. everything
     * else, so only these three are meaningful; a fourth value would just
     * silently fall into "Leadership & Organizations" without anyone
     * having chosen that on purpose.
     */
    public const CATEGORIES = ['Professional Work', 'Organization', 'Events'];

    public function authorize(): bool
    {
        return true; // route is behind the 'auth' middleware
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            // Free-text period (e.g. "Jun 2025 - Present", "2024 - 2025") —
            // there is no structured start/end date column, so no date
            // ordering validation applies here.
            'duration' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
