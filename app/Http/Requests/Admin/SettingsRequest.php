<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
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
            'site_tagline' => ['nullable', 'string', 'max:180'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'contact_address' => ['nullable', 'string', 'max:400'],
            'social_linkedin' => ['nullable', 'url:https', 'max:255'],
            'social_youtube' => ['nullable', 'url:https', 'max:255'],
            'social_instagram' => ['nullable', 'url:https', 'max:255'],
            'social_x' => ['nullable', 'url:https', 'max:255'],
            'hero_heading' => ['nullable', 'string', 'max:180'],
            'hero_subheading' => ['nullable', 'string', 'max:400'],
            'footer_note' => ['nullable', 'string', 'max:400'],
        ];
    }
}
