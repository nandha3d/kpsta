<?php

namespace App\Libraries\Ci3;

/**
 * CodeIgniter 3's $this->uri.
 *
 * This matters more than it looks. CI3 rewrote the matched URI to the route's
 * target string, and any capture the target did not reference with $1 was
 * dropped rather than passed to the method -- so a lot of controllers here
 * read their id straight off the URI with segment(). Those call sites are
 * preserved verbatim, which means segment numbering has to match CI3's:
 * 1-based, counted from the application root, ignoring the script directory.
 */
class Uri
{
    /** @var list<string> */
    private array $segments;

    public function __construct()
    {
        $this->segments = array_values(array_filter(
            explode('/', $this->uri_string()),
            static fn ($s) => $s !== ''
        ));
    }

    /**
     * Current URI path, no leading or trailing slash.
     */
    public function uri_string(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        $scriptDir = rtrim(str_replace(
            basename($_SERVER['SCRIPT_NAME'] ?? ''),
            '',
            $_SERVER['SCRIPT_NAME'] ?? ''
        ), '/');

        if ($scriptDir !== '' && str_starts_with($path, $scriptDir)) {
            $path = substr($path, strlen($scriptDir));
        }

        return trim($path, '/');
    }

    /**
     * @param int   $n       1-based segment index.
     * @param mixed $default Returned when the segment is absent, as CI3 did.
     */
    public function segment(int $n, $default = null)
    {
        return $this->segments[$n - 1] ?? $default;
    }

    /**
     * CI3 distinguished routed segments from raw ones. Routing here never
     * re-maps the visible path, so the two are the same.
     */
    public function rsegment(int $n, $default = null)
    {
        return $this->segment($n, $default);
    }

    /**
     * @return list<string>
     */
    public function segment_array(): array
    {
        return $this->segments;
    }

    /**
     * @return list<string>
     */
    public function rsegment_array(): array
    {
        return $this->segments;
    }

    public function total_segments(): int
    {
        return count($this->segments);
    }

    public function total_rsegments(): int
    {
        return count($this->segments);
    }

    /**
     * CI3's uri_to_assoc(): read the path as alternating key/value pairs.
     *
     * @return array<string, string|null>
     */
    public function uri_to_assoc(int $n = 3, array $default = []): array
    {
        $segs = array_slice($this->segments, $n - 1);
        $out  = [];

        for ($i = 0, $c = count($segs); $i < $c; $i += 2) {
            $out[$segs[$i]] = $segs[$i + 1] ?? null;
        }

        foreach ($default as $key) {
            if (! array_key_exists($key, $out)) {
                $out[$key] = null;
            }
        }

        return $out;
    }

    public function slash_segment(int $n, string $where = 'trailing'): string
    {
        $value = (string) $this->segment($n, '');

        if ($value === '') {
            return '';
        }

        return match ($where) {
            'leading'  => '/' . $value,
            'both'     => '/' . $value . '/',
            default    => $value . '/',
        };
    }
}
