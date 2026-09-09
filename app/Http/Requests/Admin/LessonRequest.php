<?php

namespace App\Http\Requests\Admin;

use App\Enums\LessonType;
use App\Models\Course;
use App\Models\Lesson;
use App\Rules\YouTubeUrl;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class LessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        $lesson = $this->route('lesson');

        return $lesson instanceof Lesson
            ? $this->user()->can('update', $lesson)
            : $this->user()->can('create', Lesson::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => str($this->input('slug') ?: $this->input('title'))->slug()->value(),
            'is_preview' => $this->boolean('is_preview'),
            'is_published' => $this->boolean('is_published'),
            'is_required' => $this->boolean('is_required', true),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $lesson = $this->route('lesson');
        $courseId = $this->course()->id;

        return [
            'course_module_id' => [
                'required',
                Rule::exists('course_modules', 'id')->where('course_id', $courseId),
            ],
            'title' => ['required', 'string', 'max:180'],
            'slug' => [
                'required', 'string', 'max:180', 'regex:/^[a-z0-9-]+$/',
                Rule::unique('lessons', 'slug')->where('course_id', $courseId)->ignore($lesson),
            ],
            'type' => ['required', new Enum(LessonType::class)],
            'description' => ['nullable', 'string', 'max:2000'],
            'content' => ['nullable', 'string', 'max:60000'],
            'video_url' => ['nullable', 'string', 'max:255', new YouTubeUrl],
            'live_class_id' => [
                'nullable',
                Rule::exists('live_classes', 'id')->where('course_id', $courseId),
            ],
            'duration_minutes' => ['required', 'integer', 'min:0', 'max:1440'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_preview' => ['boolean'],
            'is_published' => ['boolean'],
            'is_required' => ['boolean'],
        ];
    }

    /**
     * Each lesson type has one field it cannot function without.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('type')) {
                    return;
                }

                $type = LessonType::tryFrom((string) $this->input('type'));

                match ($type) {
                    LessonType::Video => blank($this->input('video_url'))
                        ? $validator->errors()->add('video_url', 'A recorded lesson needs a YouTube link.')
                        : null,
                    LessonType::Live => blank($this->input('live_class_id'))
                        ? $validator->errors()->add('live_class_id', 'Choose the live class this lesson points at.')
                        : null,
                    LessonType::Text => blank($this->input('content'))
                        ? $validator->errors()->add('content', 'A text lesson needs some content.')
                        : null,
                    default => null,
                };
            },
        ];
    }

    /**
     * The course being edited, taken from the route on create and from the
     * lesson itself on update.
     */
    public function course(): Course
    {
        $lesson = $this->route('lesson');

        if ($lesson instanceof Lesson) {
            return $lesson->course;
        }

        return $this->route('course');
    }
}
