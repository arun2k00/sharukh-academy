<?php

namespace App\Http\Requests\Tutor;

use Illuminate\Foundation\Http\FormRequest;

class InviteCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $course = $this->route('course');

        return $course !== null && $this->user()->can('update', $course);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'max_uses' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
