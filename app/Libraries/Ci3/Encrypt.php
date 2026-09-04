<?php

namespace App\Libraries\Ci3;

use CodeIgniter\Encryption\Encryption;

/**
 * CodeIgniter 3's encrypt library, over CI4's Encryption service.
 *
 * Aauth uses this only for private-message bodies, a feature this application
 * exposes no route to. It is implemented rather than stubbed so the paths stay
 * honest if that feature is ever turned on.
 *
 * CI3's encode() base64-encoded its output; that is preserved so values written
 * by this class round-trip through the same columns.
 */
class Encrypt
{
    private ?object $encrypter = null;

    private function encrypter(): object
    {
        if ($this->encrypter === null) {
            $this->encrypter = (new Encryption())->initialize();
        }

        return $this->encrypter;
    }

    public function encode(?string $string, ?string $key = null): string
    {
        if ($string === null || $string === '') {
            return '';
        }

        return base64_encode($this->encrypter()->encrypt($string));
    }

    public function decode(?string $string, ?string $key = null): string
    {
        if ($string === null || $string === '') {
            return '';
        }

        $raw = base64_decode($string, true);

        if ($raw === false) {
            return '';
        }

        try {
            return (string) $this->encrypter()->decrypt($raw);
        } catch (\Throwable $e) {
            // A value encrypted under a different key is not recoverable;
            // returning empty matches CI3, which returned FALSE-y here.
            log_message('warning', 'Encrypt::decode failed: ' . $e->getMessage());

            return '';
        }
    }
}
