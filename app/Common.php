<?php

/**
 * Global functions with CodeIgniter 3 semantics.
 *
 * CodeIgniter 4 loads this file before system/Common.php, and every global it
 * defines is wrapped in a function_exists() guard, so anything declared here
 * wins. That is the documented extension point, and it is what lets ~35k lines
 * of application code carry over unchanged.
 *
 * The important divergence is redirect(). CI3's sends headers and exits; CI4's
 * returns a RedirectResponse that the caller must return up the stack. The
 * application calls it the CI3 way in 27 places, none of which return it, so
 * the CI3 behaviour is kept here. CI4's core never calls the global helper
 * (it uses $response->redirect()), so nothing in the framework is affected.
 */

use Config\Services;

if (! function_exists('base_url')) {
    /**
     * Absolute URL for the application root, with $uri appended.
     *
     * Derived from the request rather than a configured constant, matching the
     * CI3 config this replaces: the site is served from a subdirectory on the
     * production host, so the script path has to be taken into account.
     */
    function base_url($uri = '', ?string $protocol = null): string
    {
        static $root = null;

        if ($root === null) {
            $https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
            $scheme = $https ? 'https' : 'http';

            $rawScriptName = $_SERVER['SCRIPT_NAME'] ?? '/';
            if (str_contains($rawScriptName, ':') || str_contains($rawScriptName, 'valet.php')) {
                $scriptPath = '/';
            } else {
                $scriptPath = str_replace(basename($rawScriptName), '', $rawScriptName);
            }

            $root = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $scriptPath;
        }

        $base = $root;

        if ($protocol !== null && $protocol !== '') {
            $base = preg_replace('#^https?://#', $protocol . '://', $base);
        }

        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        $uri = (string) $uri;

        return rtrim($base, '/') . '/' . ltrim($uri, '/');
    }
}

if (! function_exists('site_url')) {
    /**
     * As base_url(). The CI3 config ran with an empty index_page and no URL
     * suffix, so the two were already equivalent in this application.
     */
    function site_url($uri = '', ?string $protocol = null, $altConfig = null): string
    {
        return base_url($uri, $protocol);
    }
}

if (! function_exists('current_url')) {
    function current_url(bool $returnObject = false, ?object $request = null)
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        return base_url(ltrim($path, '/'));
    }
}

if (! function_exists('uri_string')) {
    /**
     * The current URI path with no leading slash, as CI3 returned it.
     */
    function uri_string(): string
    {
        $uri  = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        $scriptDir = rtrim(str_replace(basename($_SERVER['SCRIPT_NAME'] ?? ''), '', $_SERVER['SCRIPT_NAME'] ?? ''), '/');
        if ($scriptDir !== '' && str_starts_with($path, $scriptDir)) {
            $path = substr($path, strlen($scriptDir));
        }

        return trim($path, '/');
    }
}

if (! function_exists('redirect')) {
    /**
     * CI3-style redirect: send the header and stop.
     *
     * Deliberately does NOT return a RedirectResponse. Every call site in this
     * application invokes it as a statement and relies on execution halting --
     * the access checks in the admin and membership base controllers do this
     * from their constructors, so returning instead would let the request
     * continue into a controller the user is not allowed to reach.
     *
     * @param string $uri    Relative path, or an absolute URL.
     * @param string $method 'location' or 'refresh'
     * @param int    $code   HTTP status
     */
    function redirect($uri = '', string $method = 'location', ?int $code = null)
    {
        if (! preg_match('#^(\w+:)?//#i', (string) $uri)) {
            $uri = site_url($uri);
        }

        if ($code === null) {
            $code = 302;
        }

        // Flush any buffered output so headers can still be sent.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        if ($method === 'refresh') {
            header('Refresh:0;url=' . $uri);
        } else {
            header('Location: ' . $uri, true, $code);
        }

        exit;
    }
}

if (! function_exists('show_404')) {
    /**
     * CI3's 404. Throws CI4's page-not-found exception so the framework's own
     * error handling and status code apply.
     */
    function show_404(string $page = '', bool $logError = true)
    {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound($page !== '' ? $page : null);
    }
}

if (! function_exists('get_instance')) {
    /**
     * CI3's "give me the controller" singleton.
     *
     * The application uses it inside libraries and views to reach $this->db,
     * $this->session and the loader. App\Controllers\BaseController registers
     * itself here as it boots.
     */
    function &get_instance()
    {
        $instance = \App\Libraries\Ci3\Registry::controller();

        return $instance;
    }
}

if (! function_exists('form_error')) {
    /**
     * CI3's per-field validation error, wrapped in the delimiters CI3 used by
     * default. CI4 spells this validation_show_error().
     */
    function form_error(string $field = '', string $prefix = '', string $suffix = ''): string
    {
        $errors = \App\Libraries\Ci3\Registry::validationErrors();

        if (! isset($errors[$field])) {
            return '';
        }

        if ($prefix === '') {
            $prefix = '<p class="text-danger">';
        }
        if ($suffix === '') {
            $suffix = '</p>';
        }

        return $prefix . $errors[$field] . $suffix;
    }
}

if (! function_exists('validation_errors')) {
    /**
     * CI3's "all errors as one HTML blob".
     */
    function validation_errors(string $prefix = '', string $suffix = ''): string
    {
        $errors = \App\Libraries\Ci3\Registry::validationErrors();

        if ($errors === []) {
            return '';
        }

        if ($prefix === '') {
            $prefix = '<p class="text-danger">';
        }
        if ($suffix === '') {
            $suffix = '</p>';
        }

        $out = '';
        foreach ($errors as $message) {
            $out .= $prefix . $message . $suffix . "\n";
        }

        return $out;
    }
}

if (! function_exists('set_value')) {
    /**
     * Re-populate a field after a failed submit, falling back to $default.
     */
    function set_value(string $field, $default = '', $escape = true)
    {
        $value = \App\Libraries\Ci3\Registry::oldInput($field);

        if ($value === null) {
            $value = $default;
        }

        if (is_array($value)) {
            return $value;
        }

        return $escape ? esc((string) $value) : $value;
    }
}

if (! function_exists('html_escape')) {
    function html_escape($var, bool $doubleEncode = true)
    {
        if (is_array($var)) {
            return array_map('html_escape', $var);
        }

        if ($var === null) {
            return '';
        }

        if (is_bool($var) || is_object($var)) {
            return $var;
        }

        return htmlspecialchars((string) $var, ENT_QUOTES, 'UTF-8', $doubleEncode);
    }
}

if (! function_exists('random_string')) {
    /**
     * CI3's random_string(). Only the 'alnum' and 'numeric' types are used by
     * this application; both are backed by a CSPRNG here, which CI3's was not
     * for the alnum type.
     */
    function random_string(string $type = 'alnum', int $len = 8): string
    {
        switch ($type) {
            case 'numeric':
                $pool = '0123456789';
                break;

            case 'alpha':
                $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;

            case 'md5':
                return md5(uniqid((string) random_int(0, PHP_INT_MAX), true));

            case 'sha1':
                return sha1(uniqid((string) random_int(0, PHP_INT_MAX), true));

            case 'alnum':
            default:
                $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
        }

        $out = '';
        $max = strlen($pool) - 1;
        for ($i = 0; $i < $len; $i++) {
            $out .= $pool[random_int(0, $max)];
        }

        return $out;
    }
}

if (! function_exists('config_item')) {
    /**
     * CI3's config accessor. Backed by the ported CI3 config arrays.
     */
    function config_item(string $item)
    {
        return \App\Libraries\Ci3\Registry::configItem($item);
    }
}
