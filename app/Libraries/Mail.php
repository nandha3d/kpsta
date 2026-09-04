<?php

namespace App\Libraries;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email as MimeEmail;

/**
 * Outgoing mail, on symfony/mailer.
 *
 * Replaces the Swiftmailer 5.4 implementation the CodeIgniter 3 build used.
 * That version does not parse on PHP 8 and the project is end-of-life.
 *
 * Credentials come from the environment (.env or real environment variables);
 * nothing sensitive belongs in this file.
 */
class Mail
{
    private string $dsn;

    /** Last transport error, for the caller to log. */
    public ?string $last_error = null;

    private string $from;
    private string $fromName;
    private string $to;

    public function __construct()
    {
        $this->from     = (string) (env('mail.from') ?: env('MAIL_FROM') ?: 'kpsta.in@gmail.com');
        $this->fromName = (string) (env('mail.fromName') ?: env('MAIL_FROM_NAME') ?: 'KPSTA Website');
        $this->to       = (string) (env('mail.to') ?: env('MAIL_TO') ?: $this->from);

        $this->dsn = $this->buildDsn();
    }

    /**
     * Prefer an explicit DSN. With nothing configured, fall back to the local
     * sendmail binary rather than silently aiming at an SMTP host with no
     * credentials.
     */
    private function buildDsn(): string
    {
        $dsn = (string) (env('mail.dsn') ?: env('MAIL_DSN') ?: '');

        if ($dsn !== '') {
            return $dsn;
        }

        $user = (string) (env('MAIL_USERNAME') ?: '');
        $pass = (string) (env('MAIL_PASSWORD') ?: '');
        $host = (string) (env('MAIL_HOST') ?: 'smtp.gmail.com');
        $port = (int) (env('MAIL_PORT') ?: 587);

        if ($user === '' || $pass === '') {
            return 'sendmail://default';
        }

        return sprintf('smtp://%s:%s@%s:%d', rawurlencode($user), rawurlencode($pass), $host, $port);
    }

    /**
     * Send a website enquiry to the configured recipient.
     *
     * Signature kept from the CI3 library so the Contact controller is
     * unchanged. $replyTo is the visitor's address: it becomes Reply-To, not
     * From, because sending as an arbitrary third party gets the message
     * rewritten by the provider or dropped by SPF/DMARC.
     */
    public function send($replyTo = null, string $subject = 'Your subject', string $body = 'Test'): bool
    {
        return $this->dispatch($this->to, $subject, $body, is_string($replyTo) ? $replyTo : null);
    }

    /**
     * Send to an explicit recipient. Used by the CI3 email shim, which Aauth
     * drives for verification and password-reset messages.
     */
    public function sendTo(string $to, string $subject, string $body): bool
    {
        return $this->dispatch($to !== '' ? $to : $this->to, $subject, $body, null);
    }

    private function dispatch(string $to, string $subject, string $body, ?string $replyTo): bool
    {
        $this->last_error = null;

        try {
            $email = (new MimeEmail())
                ->from(sprintf('%s <%s>', $this->fromName, $this->from))
                ->subject($subject)
                ->html($body);

            foreach (array_filter(array_map('trim', explode(',', $to))) as $recipient) {
                $email->addTo($recipient);
            }

            if ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
                $email->replyTo($replyTo);
            }

            (new Mailer(Transport::fromDsn($this->dsn)))->send($email);

            return true;
        } catch (\Throwable $e) {
            // Never surface transport detail to the visitor.
            $this->last_error = $e->getMessage();
            log_message('error', 'Mail send failed: ' . $e->getMessage());

            return false;
        }
    }
}
