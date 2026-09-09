<?php

namespace App\Models;

use Database\Factories\CertificateTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * A background artwork plus a set of overlay field positions.
 *
 * `fields` holds one entry per overlay: student_name, course_name,
 * completion_date, certificate_id, and the optional instructor_name and
 * academy_name. Each entry carries x/y as percentages of the image (so the
 * layout survives the source image being replaced at a different resolution),
 * plus font, size, weight and color. See CertificateImageService for how this
 * is turned into pixels.
 */
#[Fillable(['name', 'image_path', 'image_width', 'image_height', 'is_active', 'show_qr_code', 'fields', 'signature_path', 'signature_x', 'signature_y', 'signature_width'])]
class CertificateTemplate extends Model
{
    /** @use HasFactory<CertificateTemplateFactory> */
    use HasFactory;

    /**
     * Fields every template ships with, in the order they are edited.
     *
     * @var list<string>
     */
    public const REQUIRED_FIELDS = ['student_name', 'course_name', 'completion_date', 'certificate_id'];

    /**
     * @var list<string>
     */
    public const OPTIONAL_FIELDS = ['instructor_name', 'academy_name'];

    /**
     * @return array<string, string>
     */
    public static function fieldLabels(): array
    {
        return [
            'student_name' => 'Student name',
            'course_name' => 'Course name',
            'completion_date' => 'Completion date',
            'certificate_id' => 'Certificate ID',
            'instructor_name' => 'Instructor name',
            'academy_name' => 'Academy name',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_qr_code' => 'boolean',
            'fields' => 'array',
            'signature_x' => 'decimal:2',
            'signature_y' => 'decimal:2',
            'signature_width' => 'decimal:2',
        ];
    }

    /**
     * @return HasMany<Course, $this>
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : null);
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function signatureUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->signature_path
            ? Storage::disk('public')->url($this->signature_path)
            : null);
    }

    /**
     * The configuration for one overlay field, or sensible defaults for a
     * field that has not been positioned yet.
     *
     * @return array{x: float, y: float, font: string, font_size: int, color: string, align: string}
     */
    public function fieldConfig(string $key): array
    {
        return ($this->fields[$key] ?? null) ?: [
            'x' => 50.0,
            'y' => 50.0,
            'font' => 'serif',
            'font_size' => 36,
            'color' => '#1a1a18',
            'align' => 'center',
        ];
    }

    /**
     * @param  Builder<CertificateTemplate>  $query
     * @return Builder<CertificateTemplate>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
