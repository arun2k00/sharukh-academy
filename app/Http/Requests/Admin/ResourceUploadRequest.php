<?php

namespace App\Http\Requests\Admin;

use App\Support\UploadRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResourceUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'file' => UploadRules::resource(),
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
