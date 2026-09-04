<?php

namespace App\Libraries\Ci3;

use CodeIgniter\HTTP\IncomingRequest;

/**
 * CodeIgniter 3's $this->input, backed by CI4's IncomingRequest.
 *
 * Only the surface the application actually uses is implemented. The CI3
 * return contract is preserved: a missing key yields NULL (not an empty
 * string), and passing no key at all returns the whole array.
 */
class Input
{
    public function __construct(private IncomingRequest $request)
    {
    }

    /**
     * @param string|null $index  Field name, or NULL for everything.
     * @param bool        $xssClean Accepted for signature compatibility; CI4
     *                              escapes on output instead of on input, so
     *                              this is intentionally not applied here.
     */
    public function post($index = null, bool $xssClean = false)
    {
        if ($index === null) {
            return $_POST;
        }

        return $_POST[$index] ?? null;
    }

    public function get($index = null, bool $xssClean = false)
    {
        if ($index === null) {
            return $_GET;
        }

        return $_GET[$index] ?? null;
    }

    /**
     * POST first, then GET -- the order CI3 used.
     */
    public function post_get($index = null, bool $xssClean = false)
    {
        return $_POST[$index] ?? $_GET[$index] ?? null;
    }

    public function get_post($index = null, bool $xssClean = false)
    {
        return $_GET[$index] ?? $_POST[$index] ?? null;
    }

    public function server($index = null, bool $xssClean = false)
    {
        if ($index === null) {
            return $_SERVER;
        }

        return $_SERVER[$index] ?? null;
    }

    public function cookie($index = null, bool $xssClean = false)
    {
        if ($index === null) {
            return $_COOKIE;
        }

        return $_COOKIE[$index] ?? null;
    }

    public function request_headers(bool $xssClean = false): array
    {
        $headers = [];
        foreach ($this->request->headers() as $name => $header) {
            $headers[$name] = $header->getValueLine();
        }

        return $headers;
    }

    public function get_request_header(string $index, bool $xssClean = false): ?string
    {
        $header = $this->request->header($index);

        return $header === null ? null : $header->getValueLine();
    }

    /**
     * CI3's set_cookie(). Accepts either an associative array describing the
     * cookie, or the positional arguments.
     *
     * @param string|array $name
     */
    public function set_cookie(
        $name,
        string $value = '',
        $expire = 0,
        string $domain = '',
        string $path = '/',
        string $prefix = '',
        ?bool $secure = null,
        ?bool $httpOnly = null,
        ?string $sameSite = null
    ): void {
        if (is_array($name)) {
            foreach (['value', 'expire', 'domain', 'path', 'prefix', 'secure', 'httponly', 'samesite'] as $item) {
                if (isset($name[$item])) {
                    ${$item === 'httponly' ? 'httpOnly' : ($item === 'samesite' ? 'sameSite' : $item)} = $name[$item];
                }
            }

            $name = $name['name'] ?? '';
        }

        $expire = (int) $expire;
        // CI3 treated a negative expiry as "delete now".
        $expires = $expire < 0 ? 1 : ($expire === 0 ? 0 : time() + $expire);

        setcookie($prefix . $name, (string) $value, [
            'expires'  => $expires,
            'path'     => $path !== '' ? $path : '/',
            'domain'   => $domain,
            'secure'   => (bool) ($secure ?? false),
            'httponly' => (bool) ($httpOnly ?? false),
            'samesite' => $sameSite ?? 'Lax',
        ]);

        if ($expires === 1) {
            unset($_COOKIE[$prefix . $name]);
        } else {
            $_COOKIE[$prefix . $name] = (string) $value;
        }
    }

    public function delete_cookie(string $name, string $domain = '', string $path = '/', string $prefix = ''): void
    {
        $this->set_cookie($name, '', -3600, $domain, $path, $prefix);
    }

    public function is_ajax_request(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    public function is_cli_request(): bool
    {
        return is_cli();
    }

    public function method(bool $upper = false): string
    {
        $method = $this->request->getMethod();

        return $upper ? strtoupper($method) : strtolower($method);
    }

    public function ip_address(): string
    {
        return $this->request->getIPAddress();
    }

    public function valid_ip(string $ip, string $which = ''): bool
    {
        $flag = match (strtolower($which)) {
            'ipv4'  => FILTER_FLAG_IPV4,
            'ipv6'  => FILTER_FLAG_IPV6,
            default => 0,
        };

        return (bool) filter_var($ip, FILTER_VALIDATE_IP, $flag);
    }

    public function user_agent(bool $xssClean = false): ?string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }
}
