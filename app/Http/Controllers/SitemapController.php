<?php

namespace App\Http\Controllers;

use App\Models\CareerPath;
use App\Models\Course;
use Illuminate\Http\Response;

/**
 * Generated rather than a static file so a newly published course is
 * discoverable without anyone remembering to update anything.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('courses.index'), 'priority' => '0.9'],
            ['loc' => route('career-paths.index'), 'priority' => '0.9'],
            ['loc' => route('about'), 'priority' => '0.5'],
            ['loc' => route('faculty'), 'priority' => '0.5'],
            ['loc' => route('contact'), 'priority' => '0.4'],
            ['loc' => route('legal.terms'), 'priority' => '0.2'],
            ['loc' => route('legal.privacy'), 'priority' => '0.2'],
            ['loc' => route('legal.refunds'), 'priority' => '0.2'],
        ]);

        $urls = $urls
            ->merge(Course::query()->published()->get()->map(fn (Course $course) => [
                'loc' => route('courses.show', $course),
                'lastmod' => $course->updated_at?->toAtomString(),
                'priority' => '0.8',
            ]))
            ->merge(CareerPath::query()->published()->get()->map(fn (CareerPath $path) => [
                'loc' => route('career-paths.show', $path),
                'lastmod' => $path->updated_at?->toAtomString(),
                'priority' => '0.8',
            ]));

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Served from here rather than `public/robots.txt` so the Sitemap line can
     * carry an absolute URL: the same code is deployed to more than one
     * domain, and a relative sitemap path is not honoured by crawlers.
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /tutor',
            'Disallow: /dashboard',
            'Disallow: /learn',
            'Disallow: /profile',
            'Disallow: /payments',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n")
            ->header('Content-Type', 'text/plain');
    }
}
