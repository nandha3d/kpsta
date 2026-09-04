<?php

/*
| -------------------------------------------------------------------------
| Outgoing mail configuration
| -------------------------------------------------------------------------
|
| Credentials are read from the environment so they are never committed.
| Set these in the server environment (cPanel "Environment Variables", or an
| Apache SetEnv directive), for example:
|
|   MAIL_DSN=smtp://user%40gmail.com:app-password@smtp.gmail.com:587
|
| Gmail requires an App Password (not the account password) once 2FA is on.
| Any character that is reserved in a URL must be percent-encoded in the DSN;
| '@' becomes %40, ':' becomes %3A.
|
| If MAIL_DSN is not set, the individual MAIL_HOST/MAIL_USERNAME/... values
| below are assembled into a DSN instead.
*/

$config['mail_dsn'] = getenv('MAIL_DSN') ?: '';

$config['mail_host']       = getenv('MAIL_HOST') ?: 'smtp.gmail.com';
$config['mail_port']       = (int) (getenv('MAIL_PORT') ?: 587);
$config['mail_username']   = getenv('MAIL_USERNAME') ?: '';
$config['mail_password']   = getenv('MAIL_PASSWORD') ?: '';

// Envelope sender. Must be an address the SMTP account is allowed to send as,
// otherwise the provider will rewrite or reject the message.
$config['mail_from']       = getenv('MAIL_FROM') ?: 'kpsta.in@gmail.com';
$config['mail_from_name']  = getenv('MAIL_FROM_NAME') ?: 'KPSTA Website';

// Where website enquiries are delivered.
$config['mail_to']         = getenv('MAIL_TO') ?: 'kpsta.in@gmail.com';
