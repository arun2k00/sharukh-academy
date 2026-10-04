<?php

namespace App\Http\Requests\Tutor;

use App\Enums\CourseAccessType;
use App\Enums\CourseCompletionMode;
use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Rules\YouTubeUrl;
use App\Support\UploadRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * A Trainer's own course form. Same shape as Admin\CourseRequest, minus
 * `instructor_id` — a Trainer's course is always attributed to their own
 * linked Instructor profile, forced server-side in the controller, never
 * picked from a dropdown of every instructor in the system.
 */
class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $course = $this->route('course');

        return $course instanceof Course
            ? $this->user()->can('update', $course)
            : $this->user()->can('create', Course::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => str($this->input('slug') ?: $this->input('title'))->slug()->value(),
            'outcomes' => $this->linesToArray($this->input('outcomes')),
            'requirements' => $this->linesToArray($this->input('requirements')),
            'audience' => $this->linesToArray($this->input('audience')),
            'is_featured' => $this->boolean('is_featured'),
            'certificate_enabled' => $this->boolean('certificate_enabled'),
            'completion_requires_live_attendance' => $this->boolean('completion_requires_live_attendance'),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $course = $this->route('course');

        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => [
                'required', 'string', 'max:180', 'regex:/^[a-z0-9-]+$/',
                Rule::unique('courses', 'slug')->ignore($course)->withoutTrashed(),
            ],
            'short_description' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')],
            'thumbnail' => UploadRules::image(),
            'preview_video_url' => ['nullable', 'string', 'max:255', new YouTubeUrl],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0', 'max:9999999', 'gte:price'],
            'level' => ['required', new Enum(CourseLevel::class)],
            'language' => ['required', 'string', 'max:40'],
            'status' => ['required', new Enum(CourseStatus::class)],
            'is_featured' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'outcomes' => ['nullable', 'array', 'max:20'],
            'outcomes.*' => ['string', 'max:255'],
            'requirements' => ['nullable', 'array', 'max:20'],
            'requirements.*' => ['string', 'max:255'],
            'audience' => ['nullable', 'array', 'max:20'],
            'audience.*' => ['string', 'max:255'],
            'certificate_enabled' => ['boolean'],
            'completion_threshold' => ['required', 'integer', 'min:1', 'max:100'],
            'certificate_template_id' => ['nullable', Rule::exists('certificate_templates', 'id')],

            'access_type' => ['required', new Enum(CourseAccessType::class)],
            'access_duration_days' => ['nullable', 'required_if:access_type,limited_duration', 'integer', 'min:1', 'max:3650'],
            'access_fixed_expiry_date' => ['nullable', 'required_if:access_type,fixed_expiry', 'date', 'after:today'],

            'completion_mode' => ['required', new Enum(CourseCompletionMode::class)],
            'completion_requires_live_attendance' => ['boolean'],

            'banner' => UploadRules::image(),
        ];
    }

    /**
     * @return list<string>|null
     */
    private function linesToArray(mixed $value): ?array
    {
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
