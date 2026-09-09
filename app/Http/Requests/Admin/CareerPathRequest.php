<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CareerPathRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => str($this->input('slug') ?: $this->input('title'))->slug()->value(),
            'is_published' => $this->boolean('is_published'),
            'is_featured' => $this->boolean('is_featured'),
            'bundle_course_id' => $this->input('bundle_course_id') ?: null,
            'courses' => array_values($this->input('courses', [])),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'slug' => [
                'required', 'string', 'max:150', 'regex:/^[a-z0-9-]+$/',
                Rule::unique('career_paths', 'slug')->ignore($this->route('career_path')),
            ],
            'subtitle' => ['nullable', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:2000'],
            'capstone_title' => ['nullable', 'string', 'max:150'],
            'capstone_description' => ['nullable', 'string', 'max:1000'],
            'bundle_course_id' => ['nullable', Rule::exists('courses', 'id')->withoutTrashed()],
            'is_published' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'courses' => ['array'],
            'courses.*.course_id' => [
                'required', 'distinct',
                Rule::exists('courses', 'id')->withoutTrashed(),
            ],
            'courses.*.stage_label' => ['required', 'string', 'max:60'],
            'courses.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
