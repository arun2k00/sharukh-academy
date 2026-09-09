<?php

namespace Database\Seeders;

use App\Services\SettingsRepository;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(SettingsRepository $settings): void
    {
        $settings->setMany([
            'site_tagline' => 'Expert-led online programmes with live classes, practical resources and certification.',
            'hero_heading' => 'Learn one craft properly, from people who practise it.',
            'hero_subheading' => 'A small, deliberate catalogue of programmes. Recorded lessons you can work through at your own pace, live sessions with your instructor, and the material you need to apply it the same week.',
            'contact_email' => 'hello@oaceracademy.com',
            'contact_phone' => '+91 80 4718 2200',
            'contact_address' => '4th Floor, Ashirwad Complex, Residency Road, Bengaluru 560025',
            'footer_note' => 'A focused online academy for practitioners who want depth, not a catalogue of shortcuts.',
            'social_linkedin' => 'https://www.linkedin.com/company/oacer-academy',
            'social_youtube' => 'https://www.youtube.com/@oaceracademy',
            'social_instagram' => 'https://www.instagram.com/oaceracademy',
        ]);
    }
}
