<?php

namespace App\Http\Requests\Admin;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class EnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $enrollment = $this->route('enrollment');

        return $enrollment instanceof Enrollment
            ? $this->user()->can('update', $enrollment)
            : $this->user()->can('create', Enrollment::class);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isCreating = ! $this->route('enrollment') instanceof Enrollment;

        return [
            'user_id' => [
                Rule::requiredIf($isCreating),
                Rule::exists('users', 'id')->withoutTrashed(),
                // The unique index also enforces this; catching it here turns a
                // database error into a readable form message.
                Rule::when($isCreating, [
                    Rule::unique('enrollments', 'user_id')->where('course_id', $this->input('course_id')),
                ]),
            ],
            'course_id' => [
                Rule::requiredIf($isCreating),
                Rule::exists('courses', 'id')->withoutTrashed(),
            ],
            'status' => ['required', new Enum(EnrollmentStatus::class)],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.unique' => 'That student already has an enrolment record for this course. Edit it instead.',
        ];
    }
}
