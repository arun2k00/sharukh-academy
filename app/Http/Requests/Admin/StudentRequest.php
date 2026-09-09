<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\UploadRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $student = $this->route('student');

        return $student instanceof User
            ? $this->user()->can('update', $student)
            : $this->user()->can('viewAny', User::class);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $student = $this->route('student');
        $isCreating = ! $student instanceof User;

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($student)->withoutTrashed(),
            ],
            'password' => [$isCreating ? 'required' : 'nullable', 'confirmed', Password::defaults()],
            'role' => ['required', new Enum(UserRole::class)],
            'phone' => ['nullable', 'string', 'max:32'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar' => UploadRules::image(),
            'email_verified' => ['boolean'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $student = $this->route('student');

                // Removing your own administrator rights would lock you out of
                // the panel you are standing in.
                if ($student instanceof User
                    && $this->user()->is($student)
                    && $this->input('role') !== UserRole::Admin->value) {
                    $validator->errors()->add('role', 'You cannot remove your own administrator access.');
                }
            },
        ];
    }
}
