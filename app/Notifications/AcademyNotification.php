<?php

namespace App\Notifications;

use App\Services\EmailTemplateRenderer;
use App\Services\NotificationDispatcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;

/**
 * One notification class for every kind of academy event, discriminated by
 * `kind` in its stored data.
 *
 * Queued so a slow or unreachable SMTP host cannot stall the request that
 * triggered it (signup and password reset most of all). Under the default
 * `QUEUE_CONNECTION=sync` this still runs inline and needs no worker; set the
 * connection to `database` and run the cron in DEPLOYMENT.md to get it off the
 * request path.
 *
 * @see NotificationDispatcher for the events that fire this.
 */
class AcademyNotification extends Notification implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    /**
     * @param  array<string, string>  $mailVariables
     * @param  array<string, mixed>  $extra  Additional fields merged into the
     *                                       stored database payload — used where a notification needs to be
     *                                       matched later, e.g. de-duplicating a scheduled reminder.
     */
    public function __construct(
        public readonly string $kind,
        public readonly string $title,
        public readonly string $body,
        public readonly ?string $url = null,
        public readonly ?string $mailTemplateKey = null,
        public readonly array $mailVariables = [],
        public readonly array $extra = [],
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return $this->mailTemplateKey !== null ? ['database', 'mail'] : ['database'];
    }

    /**
     * @return array<string, string|null>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'kind' => $this->kind,
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
            ...$this->extra,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $rendered = app(EmailTemplateRenderer::class)->render((string) $this->mailTemplateKey, $this->mailVariables);

        return (new MailMessage)
            ->subject($rendered['subject'])
            ->view('emails.templated', ['body' => $rendered['body'], 'url' => $this->url]);
    }
}
