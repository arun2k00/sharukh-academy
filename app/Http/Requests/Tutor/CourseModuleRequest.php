<?php

namespace App\Http\Requests\Tutor;

use App\Enums\ModuleUnlockType;
use App\Models\CourseModule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CourseModuleRequest extends FormRequest
{
    /**
     * Unlike Admin\CourseModuleRequest (which checks the global `create`
     * ability on Course), this resolves the specific course from the route
     * — required so an owner-aware check actually means something on create.
     */
    public function authorize(): bool
    {
        $module = $this->route('module');

        if ($module instanceof CourseModule) {
            return $this->user()->can('update', $module->loadMissing('course')->course);
        }

        $course = $this->route('course');

        return $course !== null && $this->user()->can('update', $course);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'unlock_type' => $this->input('unlock_type', 'immediate'),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'unlock_type' => ['required', new Enum(ModuleUnlockType::class)],
            'unlock_after_days' => ['nullable', 'required_if:unlock_type,days_after_enrollment', 'integer', 'min:1', 'max:3650'],
            'unlock_at' => ['nullable', 'required_if:unlock_type,fixed_date', 'date'],
        ];
    }
}
