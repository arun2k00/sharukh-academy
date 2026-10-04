<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application with a complete, working demonstration academy.
     *
     * Order matters: the catalogue has to exist before accounts can be enrolled
     * onto it, and settings are read by the public layouts.
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            CatalogSeeder::class,
            CareerPathSeeder::class,
            CertificateTemplateSeeder::class,
            DemoAccountSeeder::class,
            EngagementSeeder::class,
        ]);

        $this->command?->newLine();
        $this->command?->info('Demonstration accounts');
        $this->command?->line('  Administrator  admin@aixone.site  <DEMO_SEED_PASSWORD>');
        $this->command?->line('  Trainer        trainer@aixone.site  <DEMO_SEED_PASSWORD>');
        $this->command?->line('  Student        student@aixone.site  <DEMO_SEED_PASSWORD>  (one course finished, one in progress)');
        $this->command?->line('  Student        rohit@example.test  <DEMO_SEED_PASSWORD>  (part way through)');
        $this->command?->line('  Student        tom@example.test  <DEMO_SEED_PASSWORD>  (enrolled, nothing started)');
    }
}
