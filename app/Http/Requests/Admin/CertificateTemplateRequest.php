<?php

namespace App\Http\Requests\Admin;

use App\Models\CertificateTemplate;
use App\Services\Certificates\CertificateImageService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CertificateTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'show_qr_code' => $this->boolean('show_qr_code'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isCreating = ! $this->route('certificate_template') instanceof CertificateTemplate;

        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'image' => [
                $isCreating ? 'required' : 'nullable',
                'image',
                'mimes:'.implode(',', config('academy.uploads.certificate_mimes')),
                'max:'.config('academy.uploads.certificate_max_kb'),
            ],
            'is_active' => ['boolean'],
            'show_qr_code' => ['boolean'],
            'signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'signature_x' => ['nullable', 'numeric', 'between:0,100', 'required_with:signature'],
            'signature_y' => ['nullable', 'numeric', 'between:0,100', 'required_with:signature'],
            'signature_width' => ['nullable', 'numeric', 'between:2,60', 'required_with:signature'],
            'fields' => ['required', 'array'],
        ];

        foreach (CertificateTemplate::REQUIRED_FIELDS as $key) {
            $rules += $this->fieldRules($key, required: true);
        }

        foreach (CertificateTemplate::OPTIONAL_FIELDS as $key) {
            $rules += $this->fieldRules($key, required: false);
        }

        return $rules;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    private function fieldRules(string $key, bool $required): array
    {
        $presence = $required ? 'required' : 'nullable';

        return [
            "fields.{$key}" => [$presence, 'array'],
            "fields.{$key}.enabled" => ['nullable', 'boolean'],
            "fields.{$key}.x" => [$presence, 'numeric', 'between:0,100'],
            "fields.{$key}.y" => [$presence, 'numeric', 'between:0,100'],
            "fields.{$key}.font" => [$presence, Rule::in(CertificateImageService::availableFonts())],
            "fields.{$key}.font_size" => [$presence, 'integer', 'min:8', 'max:400'],
            "fields.{$key}.color" => [$presence, 'regex:/^#[0-9a-fA-F]{6}$/'],
            "fields.{$key}.align" => [$presence, Rule::in(['left', 'center', 'right'])],
        ];
    }
}
