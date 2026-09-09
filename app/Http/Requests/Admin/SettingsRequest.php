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
            'academy_name' => ['nullable', 'string', 'max:80'],
            'color_primary' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'color_secondary' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:1024'],
            'favicon' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:256'],
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
