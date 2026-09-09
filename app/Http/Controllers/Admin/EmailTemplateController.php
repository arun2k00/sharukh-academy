<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\EmailTemplateRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Editable copy for the emails the platform sends (§24). Only the four
 * templates the application actually controls are editable here — password
 * reset uses Laravel's own tested notification and is left alone.
 */
class EmailTemplateController extends Controller
{
    /**
     * @var array<string, list<string>>
     */
    public const VARIABLES = [
        'welcome' => ['student_name', 'academy_name'],
        'enrollment_confirmation' => ['student_name', 'course_name', 'academy_name'],
        'live_class_reminder' => ['student_name', 'live_class_name', 'live_class_date', 'academy_name'],
        'certificate_issued' => ['student_name', 'course_name', 'academy_name'],
    ];

    public function index(): View
    {
        return view('admin.email-templates.index', [
            'templates' => $this->definitions(),
        ]);
    }

    public function edit(string $emailTemplate): View
    {
        abort_unless(array_key_exists($emailTemplate, EmailTemplateRenderer::DEFAULTS), 404);

        $record = EmailTemplate::firstWhere('key', $emailTemplate);
        $default = EmailTemplateRenderer::DEFAULTS[$emailTemplate];

        return view('admin.email-templates.edit', [
            'key' => $emailTemplate,
            'name' => $default['name'],
            'subject' => $record->subject ?? $default['subject'],
            'body' => $record->body ?? $default['body'],
            'variables' => self::VARIABLES[$emailTemplate] ?? [],
            'isCustomised' => $record !== null,
        ]);
    }

    public function update(Request $request, string $emailTemplate): RedirectResponse
    {
        abort_unless(array_key_exists($emailTemplate, EmailTemplateRenderer::DEFAULTS), 404);

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:4000'],
        ]);

        EmailTemplate::updateOrCreate(
            ['key' => $emailTemplate],
            [...$validated, 'name' => EmailTemplateRenderer::DEFAULTS[$emailTemplate]['name']],
        );

        return redirect()->route('admin.email-templates.index')->with('status', 'Email template saved.');
    }

    /**
     * @return list<array{key: string, name: string, customised: bool}>
     */
    private function definitions(): array
    {
        $customised = EmailTemplate::pluck('key')->flip();

        return collect(EmailTemplateRenderer::DEFAULTS)
            ->map(fn (array $default, string $key): array => [
                'key' => $key,
                'name' => $default['name'],
                'customised' => $customised->has($key),
            ])
            ->values()
            ->all();
    }
}
