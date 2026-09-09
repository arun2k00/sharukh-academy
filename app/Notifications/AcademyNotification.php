<?php

namespace App\Notifications;

use App\Services\EmailTemplateRenderer;
use App\Services\NotificationDispatcher;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * One notification class for every kind of academy event, discriminated by
 * `kind` in its stored data. Deliberately not queued: sent synchronously so
 * the application never depends on a permanent queue worker being present.
 *
 * @see NotificationDispatcher for the events that fire this.
 */
class AcademyNotification extends Notification
{
    /**
     * @param  array<string, string>  $mailVariables
     */
    public function __construct(
        public readonly string $kind,
        public readonly string $title,
        public readonly string $body,
        public readonly ?string $url = null,
        public readonly ?string $mailTemplateKey = null,
        public readonly array $mailVariables = [],
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
