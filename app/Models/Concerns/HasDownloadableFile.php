<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Number;

/**
 * Shared presentation for stored resource files.
 *
 * Files live on the private disk; download URLs are always produced by the
 * controllers that authorise them, never from the stored path.
 */
trait HasDownloadableFile
{
    /**
     * @return Attribute<string, never>
     */
    protected function sizeLabel(): Attribute
    {
        return Attribute::get(fn (): string => $this->file_size > 0
            ? Number::fileSize($this->file_size, precision: 1)
            : '—');
    }

    /**
     * Coarse file family, used to pick the resource row icon.
     *
     * @return Attribute<string, never>
     */
    protected function fileKind(): Attribute
    {
        return Attribute::get(fn (): string => match (mb_strtolower((string) $this->extension)) {
            'pdf' => 'pdf',
            'doc', 'docx', 'rtf', 'txt' => 'doc',
            'xls', 'xlsx', 'csv' => 'sheet',
            'zip' => 'archive',
            'png', 'jpg', 'jpeg', 'webp', 'gif' => 'image',
            'ppt', 'pptx' => 'slides',
            default => 'file',
        });
    }
}
