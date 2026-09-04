<?php

namespace App\Libraries\Ci3;

use CodeIgniter\HTTP\ResponseInterface;

/**
 * CodeIgniter 3's $this->output, including the nocache() that MY_Output added.
 *
 * The CI3 build called $this->output->cache() on the home page, but the cache
 * directory it pointed at did not exist, so writes failed silently and nothing
 * was ever served from cache. Rather than carry that over, caching is a no-op
 * here and delete_cache() succeeds trivially; CI4's own response cache can be
 * turned on later without the call sites changing.
 */
class Output
{
    private string $output = '';

    public function __construct(private ResponseInterface $response)
    {
    }

    /**
     * The no-store header set MY_Output added, used by the membership area so
     * a signed-in page is not restored from the browser's back/forward cache.
     */
    public function nocache(): static
    {
        $this->response->setHeader('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
        $this->response->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $this->response->appendHeader('Cache-Control', 'post-check=0, pre-check=0');
        $this->response->setHeader('Pragma', 'no-cache');

        return $this;
    }

    public function set_output(string $output): static
    {
        $this->output = $output;
        $this->response->setBody($output);

        return $this;
    }

    public function get_output(): string
    {
        return $this->output !== '' ? $this->output : (string) $this->response->getBody();
    }

    public function append_output(string $output): static
    {
        $this->output = $this->get_output() . $output;
        $this->response->setBody($this->output);

        return $this;
    }

    /**
     * @param string $header A full "Name: value" line, as CI3 took it.
     */
    public function set_header(string $header, bool $replace = true): static
    {
        if (str_contains($header, ':')) {
            [$name, $value] = explode(':', $header, 2);
            $name  = trim($name);
            $value = trim($value);

            if ($replace) {
                $this->response->setHeader($name, $value);
            } else {
                $this->response->appendHeader($name, $value);
            }
        }

        return $this;
    }

    public function set_content_type(string $mimeType, string $charset = 'UTF-8'): static
    {
        $this->response->setContentType($mimeType, $charset);

        return $this;
    }

    public function set_status_header(int $code = 200, string $text = ''): static
    {
        $this->response->setStatusCode($code, $text);

        return $this;
    }

    /**
     * No-op. See the class comment: CI3's page cache never actually wrote.
     */
    public function cache(int $time): static
    {
        return $this;
    }

    public function delete_cache(?string $uri = null): bool
    {
        return true;
    }

    /**
     * CI3 exposed this to let the compress hook emit early. Nothing registers
     * that hook here, so it only needs to not break callers.
     */
    public function _display(string $output = ''): void
    {
        if ($output !== '') {
            $this->set_output($output);
        }
    }
}
