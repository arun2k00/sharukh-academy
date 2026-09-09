<?php

namespace App\Models;

use App\Models\Concerns\HasDownloadableFile;
use Database\Factories\LiveClassResourceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'live_class_id', 'title', 'file_path', 'original_name', 'mime_type',
    'extension', 'file_size', 'sort_order',
])]
class LiveClassResource extends Model
{
    /** @use HasFactory<LiveClassResourceFactory> */
    use HasDownloadableFile, HasFactory;

    /**
     * @return BelongsTo<LiveClass, $this>
     */
    public function liveClass(): BelongsTo
    {
        return $this->belongsTo(LiveClass::class);
    }
}
