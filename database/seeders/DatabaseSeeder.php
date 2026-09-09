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
            CertificateTemplateSeeder::class,
            DemoAccountSeeder::class,
        ]);

        $this->command?->newLine();
        $this->command?->info('Demonstration accounts (password: password)');
        $this->command?->line('  Administrator  admin@oaceracademy.test');
        $this->command?->line('  Student        meera@example.test   (one course finished, one in progress)');
        $this->command?->line('  Student        rohit@example.test   (part way through)');
        $this->command?->line('  Student        tom@example.test     (enrolled, nothing started)');
    }
}
