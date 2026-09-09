<?php

namespace Database\Seeders;

use App\Models\CertificateTemplate;
use App\Models\Course;
use Database\Seeders\Support\DemoCertificateBackgroundFactory;
use Illuminate\Database\Seeder;

class CertificateTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $path = 'certificate-templates/demo-classic.png';
        DemoCertificateBackgroundFactory::make($path);

        $template = CertificateTemplate::updateOrCreate(
            ['name' => 'Classic parchment'],
            [
                'image_path' => $path,
                'image_width' => DemoCertificateBackgroundFactory::width(),
                'image_height' => DemoCertificateBackgroundFactory::height(),
                'is_active' => true,
                'show_qr_code' => true,
                'fields' => [
                    'student_name' => ['x' => 50.0, 'y' => 48.0, 'font' => 'serif', 'font_size' => 110, 'color' => '#1a1a18', 'align' => 'center'],
                    'course_name' => ['x' => 50.0, 'y' => 60.0, 'font' => 'serif-bold', 'font_size' => 58, 'color' => '#1b4331', 'align' => 'center'],
                    'completion_date' => ['x' => 26.0, 'y' => 84.0, 'font' => 'sans', 'font_size' => 30, 'color' => '#55554f', 'align' => 'center'],
                    'certificate_id' => ['x' => 74.0, 'y' => 84.0, 'font' => 'sans', 'font_size' => 30, 'color' => '#55554f', 'align' => 'center'],
                ],
            ],
        );

        // Assign it to the first published course as a working example.
        Course::query()->published()->orderBy('sort_order')->first()?->update([
            'certificate_template_id' => $template->id,
        ]);
    }
}
