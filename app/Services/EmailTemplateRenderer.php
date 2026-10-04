<?php

namespace App\Services;

use App\Models\EmailTemplate;

/**
 * Renders an admin-editable email template with safe, allow-listed variable
 * substitution — plain `str_replace` on `{{ token }}` markers, never Blade
 * compilation or any other form of code execution over admin-authored text.
 */
class EmailTemplateRenderer
{
    /**
     * Fallback content used until an administrator edits a template, keyed
     * the same way as the `email_templates.key` column.
     *
     * @var array<string, array{name: string, subject: string, body: string}>
     */
    public const DEFAULTS = [
        'welcome' => [
            'name' => 'Welcome email',
            'subject' => 'Welcome to {{ academy_name }}',
            'body' => "Hello {{ student_name }},\n\nYour account is ready. You can browse the course catalogue and start learning right away.\n\n{{ academy_name }}",
        ],
        'enrollment_confirmation' => [
            'name' => 'Enrollment confirmation',
            'subject' => "You're enrolled in {{ course_name }}",
            'body' => "Hello {{ student_name }},\n\nYou now have access to {{ course_name }}. Head to your dashboard to start the first lesson.\n\n{{ academy_name }}",
        ],
        'live_class_reminder' => [
            'name' => 'Live class reminder',
            'subject' => 'Reminder: {{ live_class_name }} — {{ live_class_date }}',
            'body' => "Hello {{ student_name }},\n\nThis is a reminder that \"{{ live_class_name }}\" is scheduled for {{ live_class_date }}. See the class page on your dashboard for join details.\n\n{{ academy_name }}",
        ],
        'certificate_issued' => [
            'name' => 'Certificate issued',
            'subject' => 'Your certificate for {{ course_name }} is ready',
            'body' => "Hello {{ student_name }},\n\nCongratulations on completing {{ course_name }}. Your certificate is ready to view, download and share from your dashboard.\n\n{{ academy_name }}",
        ],
    ];

    /**
     * @param  array<string, string>  $variables
     * @return array{subject: string, body: string}
     */
    public function render(string $key, array $variables): array
    {
        $template = EmailTemplate::query()->where('key', $key)->first();
        $default = self::DEFAULTS[$key] ?? ['name' => $key, 'subject' => $key, 'body' => ''];

        $subject = $template->subject ?? $default['subject'];
        $body = $template->body ?? $default['body'];

        return [
            'subject' => $this->substitute($subject, $variables),
            'body' => $this->substitute($body, $variables),
        ];
    }

    /**
     * @param  array<string, string>  $variables
     */
    private function substitute(string $text, array $variables): string
    {
        $search = [];
        $replace = [];

        foreach ($variables as $key => $value) {
            $search[] = '{{ '.$key.' }}';
            $replace[] = (string) $value;
        }

        return str_replace($search, $replace, $text);
    }
}
