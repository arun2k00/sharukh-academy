<?php

namespace App\Http\Requests\Admin;

use App\Support\UploadRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => str($this->input('slug') ?: $this->input('name'))->slug()->value(),
            'is_featured' => $this->boolean('is_featured'),
            'credentials' => $this->credentialLines(),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'required', 'string', 'max:120', 'regex:/^[a-z0-9-]+$/',
                Rule::unique('instructors', 'slug')->ignore($this->route('instructor')),
            ],
            'headline' => ['nullable', 'string', 'max:180'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'avatar' => UploadRules::image(),
            'credentials' => ['nullable', 'array', 'max:12'],
            'credentials.*' => ['string', 'max:180'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:80'],
            'linkedin_url' => ['nullable', 'url:https', 'max:255'],
            'website_url' => ['nullable', 'url:http,https', 'max:255'],
            'is_featured' => ['boolean'],
        ];
    }

    /**
     * @return list<string>|null
     */
    private function credentialLines(): ?array
    {
        $value = $this->input('credentials');

        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
