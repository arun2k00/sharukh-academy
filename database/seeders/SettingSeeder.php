<?php

namespace Database\Seeders;

use App\Services\SettingsRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SettingSeeder extends Seeder
{
    public function run(SettingsRepository $settings): void
    {
        $settings->setMany([
            'academy_name' => 'Edunova',
            'site_tagline' => 'Job-ready data engineering training with live classes, real projects and certification.',
            'hero_heading' => 'Get job-ready in data engineering, taught by people who build it for a living.',
            'hero_subheading' => 'Six focused programmes across the modern data stack — SQL Server, Python, Azure Data Factory, Snowflake, dbt and Power BI — each with live classes, production-grade projects and a certificate that means something.',
            'contact_email' => 'hello@edunova.com',
            'contact_phone' => '+91 80 4718 2200',
            'contact_address' => '4th Floor, Ashirwad Complex, Residency Road, Bengaluru 560025',
            'footer_note' => 'A focused data engineering academy for people who want to be job-ready, not just certificate-ready.',
            'social_linkedin' => 'https://www.linkedin.com/company/edunova',
            'social_youtube' => 'https://www.youtube.com/@edunova',
            'social_instagram' => 'https://www.instagram.com/edunova',
            'color_primary' => '#03753e',
            'color_secondary' => '#1a1a18',
            'logo_path' => $this->copyBrandAsset('enova.png', 'edunova-logo.png'),
            'favicon_path' => $this->copyBrandAsset('icon.png', 'edunova-icon.png'),
        ]);
    }

    /**
     * Seeding a brand asset from `public/` reuses the exact same storage path
     * and settings keys a real admin upload would produce (see
     * SettingController::update()), so no template has to special-case it.
     */
    private function copyBrandAsset(string $sourceFilename, string $storedFilename): ?string
    {
        $sourcePath = public_path($sourceFilename);

        if (! is_file($sourcePath)) {
            return null;
        }

        $storedPath = 'brand/'.$storedFilename;

        Storage::disk('public')->put($storedPath, file_get_contents($sourcePath));

        return $storedPath;
    }
}
