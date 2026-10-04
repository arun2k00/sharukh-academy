<?php

namespace App\Http\Requests\Tutor;

use App\Enums\LiveClassStatus;
use App\Models\Course;
use App\Models\LiveClass;
use App\Rules\ZoomUrl;
use App\Support\UploadRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

/**
 * Same shape as Admin\LiveClassRequest, minus `instructor_id` (forced
 * server-side to the Trainer's own profile). The plain `exists` rule on
 * `course_id` says nothing about ownership — any existing course id would
 * pass it — so an `after()` hook rejects a course this Trainer doesn't own.
 */
class LiveClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        $liveClass = $this->route('liveClass');

        return $liveClass instanceof LiveClass
            ? $this->user()->can('update', $liveClass)
            : $this->user()->can('create', LiveClass::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => str($this->input('slug') ?: $this->input('title'))->slug()->value(),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => ['required', Rule::exists('courses', 'id')->withoutTrashed()],
            'title' => ['required', 'string', 'max:180'],
            'slug' => [
                'required', 'string', 'max:180', 'regex:/^[a-z0-9-]+$/',
                Rule::unique('live_classes', 'slug')->ignore($this->route('liveClass'))->withoutTrashed(),
            ],
            'description' => ['nullable', 'string', 'max:4000'],
            'preparation_notes' => ['nullable', 'string', 'max:4000'],
            'scheduled_at' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:600'],
            'zoom_url' => ['nullable', 'string', 'max:500', new ZoomUrl],
            'zoom_meeting_id' => ['nullable', 'string', 'max:64'],
            'zoom_passcode' => ['nullable', 'string', 'max:64'],
            'recording_url' => ['nullable', 'url:https', 'max:500'],
            'status' => ['required', new Enum(LiveClassStatus::class)],
            'is_featured' => ['boolean'],
            'banner' => UploadRules::image(),
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('course_id') || $this->user()->isAdmin()) {
                    return;
                }

                $course = Course::find($this->input('course_id'));

                if ($course !== null && ! $course->isOwnedBy($this->user())) {
                    $validator->errors()->add('course_id', 'You may only schedule live classes for your own courses.');
                }
            },
        ];
    }
}
