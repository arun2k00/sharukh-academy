<?php

namespace Database\Factories;

use App\Models\CertificateTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CertificateTemplate>
 */
class CertificateTemplateFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Classic '.fake()->word(),
            'image_path' => 'certificate-templates/demo.png',
            'image_width' => 3508,
            'image_height' => 2481,
            'is_active' => true,
            'show_qr_code' => true,
            'fields' => [
                'student_name' => ['x' => 50.0, 'y' => 46.0, 'font' => 'serif', 'font_size' => 130, 'color' => '#1a1a18', 'align' => 'center'],
                'course_name' => ['x' => 50.0, 'y' => 58.0, 'font' => 'serif-bold', 'font_size' => 70, 'color' => '#1a1a18', 'align' => 'center'],
                'completion_date' => ['x' => 22.0, 'y' => 86.0, 'font' => 'sans', 'font_size' => 38, 'color' => '#55554f', 'align' => 'center'],
                'certificate_id' => ['x' => 78.0, 'y' => 86.0, 'font' => 'sans', 'font_size' => 38, 'color' => '#55554f', 'align' => 'center'],
            ],
        ];
    }
}
