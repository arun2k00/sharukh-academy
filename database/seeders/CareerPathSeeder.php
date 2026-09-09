<?php

namespace Database\Seeders;

use App\Models\CareerPath;
use App\Models\Course;
use Illuminate\Database\Seeder;

/**
 * The flagship career path: a staged roadmap through the data engineering
 * catalogue seeded by CatalogSeeder. Each course stays independently
 * enrollable — this only sequences and groups them for the journey page.
 */
class CareerPathSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            'sql-for-data-engineering' => 'FOUNDATION',
            'python-for-data-engineering' => 'FOUNDATION',
            'azure-data-factory' => 'DATA ENGINEERING',
            'snowflake-masterclass' => 'DATA ENGINEERING',
            'dbt-for-modern-data-engineering' => 'TRANSFORMATION',
            'power-bi-for-data-professionals' => 'ANALYTICS',
        ];

        $courses = Course::query()->whereIn('slug', array_keys($stages))->get()->keyBy('slug');

        if ($courses->count() !== count($stages)) {
            return;
        }

        $careerPath = CareerPath::updateOrCreate(
            ['slug' => 'data-engineering-complete-job-ready-program'],
            [
                'title' => 'Data Engineering — Complete Job-Ready Program',
                'subtitle' => 'SQL and Python, then Azure Data Factory and Snowflake, then dbt and Power BI — in the order we would actually recommend.',
                'description' => "One course teaches you a tool. This path teaches you the job: get comfortable with the languages first, learn to move and store data at scale, then transform it and present it the way a real data team does.\n\nEvery course below is a complete, independently enrollable programme on its own — this page is the recommended order to take them in, so what you learn in one course is already in place by the time you need it in the next.",
                'capstone_title' => 'Build an end-to-end data platform',
                'capstone_description' => 'Combine every tool above into one project: pull data from an API with Python, land and model it in Snowflake, orchestrate the load with Azure Data Factory, transform it with dbt, and present it in a Power BI dashboard — the same shape of project a real data engineering job asks for.',
                'is_published' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ],
        );

        $sortOrder = 1;
        $sync = [];

        foreach ($stages as $slug => $stageLabel) {
            $sync[$courses[$slug]->id] = ['stage_label' => $stageLabel, 'sort_order' => $sortOrder++];
        }

        $careerPath->courses()->sync($sync);
    }
}
