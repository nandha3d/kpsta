<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '../vendor/autoload.php';

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

/**
 * Outgoing mail.
 *
 * Replaces the previous Swiftmailer 5.4 implementation. Swiftmailer 5.4 does
 * not parse on PHP 8 (it uses the removed `$string{0}` offset syntax) and the
 * project is end-of-life, so this is built on symfony/mailer instead.
 *
 * Credentials come from application/config/mail.php, which reads them from the
 * environment. Nothing sensitive belongs in this file.
 */
class Mail {

    /** @var string */
    private $dsn;

    /** @var array */
    private $cfg;

    /** @var string|null Last transport error, for logging by the caller. */
    public $last_error = NULL;

    public function __construct() {
        $CI =& get_instance();
        $CI->config->load('mail', TRUE);
        $this->cfg = $CI->config->item('mail');

        $this->dsn = $this->build_dsn();
    }

    /**
     * Assemble the transport DSN, preferring an explicit MAIL_DSN.
     *
     * With no credentials configured we fall back to the local sendmail binary
     * rather than silently pointing at Gmail with an empty password.
     */
    private function build_dsn() {
        if (!empty($this->cfg['mail_dsn'])) {
            return $this->cfg['mail_dsn'];
        }

        if (empty($this->cfg['mail_username']) OR empty($this->cfg['mail_password'])) {
            return 'sendmail://default';
        }

        return sprintf(
            'smtp://%s:%s@%s:%d',
            rawurlencode($this->cfg['mail_username']),
            rawurlencode($this->cfg['mail_password']),
            $this->cfg['mail_host'],
            $this->cfg['mail_port']
        );
    }

    /**
     * Send a website enquiry.
     *
     * Signature is kept from the previous implementation so callers do not
     * change. $replyTo is the address the enquiry came from; it is NOT used as
     * the From header, because sending as an arbitrary third party gets the
     * message rewritten by the provider or dropped by SPF/DMARC.
     *
     * @param  string $replyTo Address that submitted the enquiry.
     * @param  string $subject
     * @param  string $body    HTML body.
     * @return bool
     */
    public function send($replyTo = NULL, $subject = 'Your subject', $body = 'Test') {
        $this->last_error = NULL;

        try {
            $email = (new Email())
                ->from(sprintf('%s <%s>', $this->cfg['mail_from_name'], $this->cfg['mail_from']))
                ->to($this->cfg['mail_to'])
                ->subject($subject)
                ->html($body);

            // Only honour a syntactically valid reply-to; the value is user input.
            if (!empty($replyTo) && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
                $email->replyTo($replyTo);
            }

            $mailer = new Mailer(Transport::fromDsn($this->dsn));
            $mailer->send($email);

            return TRUE;
        } catch (\Throwable $e) {
            // Never surface transport detail to the visitor; log it instead.
            $this->last_error = $e->getMessage();
            log_message('error', 'Mail send failed: ' . $e->getMessage());

            return FALSE;
        }
    }

}
