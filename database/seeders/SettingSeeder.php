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
            'academy_name' => 'Sharukh Academy',
            'site_tagline' => 'Job-ready data engineering training with live classes, real projects and certification.',
            // No hero_heading: leaving it unset lets the homepage's rotating
            // default headlines show. An administrator setting one here (or
            // from the admin settings form) pins it back to a single line.
            'hero_subheading' => 'Six focused programmes, each with live classes, production-grade projects and a certificate that means something.',
            'contact_email' => 'no-reply@aixone.site',
            'contact_phone' => '+91 80 4718 2200',
            'contact_address' => '4th Floor, Ashirwad Complex, Residency Road, Bengaluru 560025',
            'footer_note' => 'A focused data engineering academy for people who want to be job-ready, not just certificate-ready.',
            'social_linkedin' => 'https://www.linkedin.com/company/sharukhacademy',
            'social_youtube' => 'https://www.youtube.com/@sharukhacademy',
            'social_instagram' => 'https://www.instagram.com/sharukhacademy',
            'color_primary' => '#0d539b',
            'color_secondary' => '#073055',
            'logo_path' => $this->copyBrandAsset('sharukh-academy-logo.png', 'sharukh-academy-logo.png'),
            // White cut of the same mark, for the dark header band in emails.
            'logo_email_path' => $this->copyBrandAsset('sharukh-academy-logo-white.png', 'sharukh-academy-logo-white.png'),
            'favicon_path' => $this->copyBrandAsset('sharukh-icon.png', 'sharukh-icon.png'),
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
