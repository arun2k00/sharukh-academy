<?php

namespace App\Models;

use App\Models\Concerns\HasDownloadableFile;
use Database\Factories\LessonResourceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'lesson_id', 'title', 'file_path', 'original_name', 'mime_type',
    'extension', 'file_size', 'sort_order',
])]
class LessonResource extends Model
{
    /** @use HasFactory<LessonResourceFactory> */
    use HasDownloadableFile, HasFactory;

    /**
     * @return BelongsTo<Lesson, $this>
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
