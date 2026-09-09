<?php

namespace App\Support;

/**
 * Upload validation rules built from `config/academy.php`.
 *
 * `mimes` inspects the file contents rather than trusting the filename, and the
 * allow-list keeps executable and unknown formats out of storage.
 */
final class UploadRules
{
    /**
     * @return list<string>
     */
    public static function image(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'image',
            'mimes:'.implode(',', config('academy.uploads.image_mimes')),
            'max:'.config('academy.uploads.image_max_kb'),
        ];
    }

    /**
     * @return list<string>
     */
    public static function resource(bool $required = true): array
    {
        return [
            $required ? 'required' : 'nullable',
            'file',
            'mimes:'.implode(',', config('academy.uploads.resource_mimes')),
            'max:'.config('academy.uploads.resource_max_kb'),
        ];
    }
}
