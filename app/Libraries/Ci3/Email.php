<?php

namespace App\Libraries\Ci3;

use App\Libraries\Mail;

/**
 * CodeIgniter 3's email library, over the application's symfony/mailer wrapper.
 *
 * Aauth uses this for account verification and password-reset messages. Those
 * flows have no route in this application today, but the calls are on live code
 * paths inside Aauth, so they need to work rather than fatal.
 */
class Email
{
    private string $to = '';
    private string $from = '';
    private string $fromName = '';
    private string $subject = '';
    private string $message = '';
    private bool $html = false;

    /** @var list<string> */
    private array $errors = [];

    /**
     * CI3 allowed per-send config; nothing here needs to vary, so the values
     * are accepted and ignored rather than silently mis-applied.
     *
     * @param array<string, mixed> $config
     */
    public function initialize(array $config = []): static
    {
        return $this;
    }

    public function from(string $from, string $name = '', string $returnPath = ''): static
    {
        $this->from     = $from;
        $this->fromName = $name;

        return $this;
    }

    public function to($to): static
    {
        $this->to = is_array($to) ? implode(',', $to) : (string) $to;

        return $this;
    }

    public function subject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function message(string $body): static
    {
        $this->message = $body;

        return $this;
    }

    public function set_mailtype(string $type = 'text'): static
    {
        $this->html = $type === 'html';

        return $this;
    }

    public function send(bool $autoClear = true): bool
    {
        $mail = new Mail();

        $body = $this->html
            ? $this->message
            : nl2br(htmlspecialchars($this->message, ENT_QUOTES, 'UTF-8'));

        $sent = $mail->sendTo($this->to, $this->subject, $body);

        if (! $sent) {
            $this->errors[] = (string) $mail->last_error;
        }

        if ($autoClear) {
            $this->clear();
        }

        return $sent;
    }

    public function clear(bool $clearAttachments = false): static
    {
        $this->to      = '';
        $this->subject = '';
        $this->message = '';

        return $this;
    }

    public function print_debugger($include = ['headers', 'subject', 'body']): string
    {
        return implode("\n", $this->errors);
    }
}
