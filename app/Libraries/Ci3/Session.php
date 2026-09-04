<?php

namespace App\Libraries\Ci3;

use CodeIgniter\Session\Session as Ci4Session;

/**
 * CodeIgniter 3's $this->session, backed by CI4's session service.
 *
 * The session keys themselves are unchanged ('id', 'group', 'office',
 * 'loggedin', ...), so a user signed in before the migration keeps their
 * session, and anything reading userdata() keeps working.
 */
class Session
{
    public function __construct(private Ci4Session $session)
    {
    }

    /**
     * @param string|null $key NULL returns the whole session, as CI3 did.
     */
    public function userdata($key = null)
    {
        if ($key === null) {
            return $this->session->get();
        }

        return $this->session->get($key);
    }

    /**
     * @param string|array $data Key, or an associative array of pairs.
     */
    public function set_userdata($data, $value = null): void
    {
        if (is_array($data)) {
            $this->session->set($data);

            return;
        }

        $this->session->set($data, $value);
    }

    /**
     * @param string|array $key
     */
    public function unset_userdata($key): void
    {
        $this->session->remove($key);
    }

    public function has_userdata(string $key): bool
    {
        return $this->session->has($key);
    }

    public function all_userdata(): array
    {
        return $this->session->get();
    }

    public function flashdata($key = null)
    {
        if ($key === null) {
            return $this->session->getFlashdata();
        }

        return $this->session->getFlashdata($key);
    }

    /**
     * @param string|array $data
     */
    public function set_flashdata($data, $value = null): void
    {
        if (is_array($data)) {
            $this->session->setFlashdata($data);

            return;
        }

        $this->session->setFlashdata($data, $value);
    }

    public function keep_flashdata($key): void
    {
        $this->session->keepFlashdata($key);
    }

    public function tempdata($key = null)
    {
        if ($key === null) {
            return $this->session->getTempdata();
        }

        return $this->session->getTempdata($key);
    }

    public function set_tempdata($data, $value = null, int $ttl = 300): void
    {
        if (is_array($data)) {
            $this->session->setTempdata($data, null, $ttl);

            return;
        }

        $this->session->setTempdata($data, $value, $ttl);
    }

    public function unset_tempdata($key): void
    {
        $this->session->removeTempdata($key);
    }

    public function sess_destroy(): void
    {
        $this->session->destroy();
    }

    public function sess_regenerate(bool $destroy = false): void
    {
        $this->session->regenerate($destroy);
    }

    /**
     * The underlying CI4 session, for code that wants the modern API.
     */
    public function ci4(): Ci4Session
    {
        return $this->session;
    }
}
