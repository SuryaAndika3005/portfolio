<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route is behind the 'auth' middleware
    }

    /**
     * Name only. Slug is intentionally NOT a submittable field — see
     * CategoryController's class docblock for why editing it is unsafe
     * with the current public architecture.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($this->route('category'))],
        ];
    }
}
