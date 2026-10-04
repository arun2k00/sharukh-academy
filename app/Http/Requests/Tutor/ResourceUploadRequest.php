<?php

namespace App\Http\Requests\Tutor;

use App\Support\UploadRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Admin\ResourceUploadRequest hardcodes `$this->user()->isAdmin()`, which
 * would 403 every Trainer outright. This resolves whichever parent (lesson
 * or live class) the route carries and defers to the owner-aware policy.
 */
class ResourceUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $parent = $this->route('lesson') ?? $this->route('liveClass');

        return $parent !== null && $this->user()->can('update', $parent);
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
