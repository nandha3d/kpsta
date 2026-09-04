<?php

namespace App\Libraries\Ci3;

/**
 * CodeIgniter 3's pagination library.
 *
 * The application drives this entirely through initialize()/create_links()
 * with the Bootstrap tag configuration built by the base controller, so the
 * markup those tags produce is what has to be reproduced exactly.
 *
 * The CI3 build ran with use_page_numbers = TRUE and page_query_string = TRUE,
 * meaning links carry ?page=N (a page number, not a row offset).
 */
class Pagination
{
    /** @var array<string, mixed> */
    private array $config = [];

    private const DEFAULTS = [
        'base_url'             => '',
        'total_rows'           => 0,
        'per_page'             => 10,
        'num_links'            => 2,
        'cur_page'             => 0,
        'use_page_numbers'     => false,
        'page_query_string'    => false,
        'query_string_segment' => 'per_page',
        'uri_segment'          => 3,
        'full_tag_open'        => '',
        'full_tag_close'       => '',
        'first_link'           => 'First',
        'first_tag_open'       => '',
        'first_tag_close'      => '',
        'last_link'            => 'Last',
        'last_tag_open'        => '',
        'last_tag_close'       => '',
        'next_link'            => '&gt;',
        'next_tag_open'        => '',
        'next_tag_close'       => '',
        'prev_link'            => '&lt;',
        'prev_tag_open'        => '',
        'prev_tag_close'       => '',
        'cur_tag_open'         => '<strong>',
        'cur_tag_close'        => '</strong>',
        'num_tag_open'         => '',
        'num_tag_close'        => '',
        'attributes'           => '',
        'reuse_query_string'   => false,
    ];

    public function __construct(array $config = [])
    {
        $this->initialize($config);
    }

    public function initialize(array $config = []): static
    {
        $this->config = array_merge(self::DEFAULTS, $config);

        return $this;
    }

    public function create_links(): string
    {
        $c         = $this->config;
        $totalRows = (int) $c['total_rows'];
        $perPage   = max(1, (int) $c['per_page']);

        $numPages = (int) ceil($totalRows / $perPage);

        // CI3 rendered nothing when everything fits on one page.
        if ($numPages <= 1) {
            return '';
        }

        $current = $this->currentPage($numPages);
        $numLink = max(1, (int) $c['num_links']);

        $start = max(1, $current - $numLink);
        $end   = min($numPages, $current + $numLink);

        $out = (string) $c['full_tag_open'];

        if ($c['first_link'] !== false && $current > ($numLink + 1)) {
            $out .= $c['first_tag_open'] . $this->anchor((string) $c['first_link'], 1) . $c['first_tag_close'];
        }

        if ($c['prev_link'] !== false && $current > 1) {
            $out .= $c['prev_tag_open'] . $this->anchor((string) $c['prev_link'], $current - 1) . $c['prev_tag_close'];
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i === $current) {
                $out .= $c['cur_tag_open'] . $i . $c['cur_tag_close'];
            } else {
                $out .= $c['num_tag_open'] . $this->anchor((string) $i, $i) . $c['num_tag_close'];
            }
        }

        if ($c['next_link'] !== false && $current < $numPages) {
            $out .= $c['next_tag_open'] . $this->anchor((string) $c['next_link'], $current + 1) . $c['next_tag_close'];
        }

        if ($c['last_link'] !== false && ($current + $numLink) < $numPages) {
            $out .= $c['last_tag_open'] . $this->anchor((string) $c['last_link'], $numPages) . $c['last_tag_close'];
        }

        return $out . $c['full_tag_close'];
    }

    private function currentPage(int $numPages): int
    {
        $c = $this->config;

        if ((int) $c['cur_page'] > 0) {
            $page = (int) $c['cur_page'];
        } elseif ($c['page_query_string']) {
            $page = (int) ($_GET[$c['query_string_segment']] ?? 1);
        } else {
            $uri  = new Uri();
            $page = (int) $uri->segment((int) $c['uri_segment'], 1);
        }

        if (! $c['use_page_numbers'] && $page > 0) {
            // Offsets rather than page numbers.
            $page = (int) floor($page / max(1, (int) $c['per_page'])) + 1;
        }

        return min(max(1, $page), $numPages);
    }

    private function anchor(string $label, int $page): string
    {
        $c   = $this->config;
        $url = (string) $c['base_url'];

        if ($c['page_query_string']) {
            $query = [];
            if ($c['reuse_query_string']) {
                $query = $_GET;
            }
            $query[$c['query_string_segment']] = $page;

            $sep = str_contains($url, '?') ? '&' : '?';
            $url = strtok($url, '?') . $sep . http_build_query($query);
        } else {
            $value = $c['use_page_numbers'] ? $page : ($page - 1) * (int) $c['per_page'];
            $url   = rtrim($url, '/') . '/' . $value;
        }

        $attributes = $c['attributes'] !== '' ? ' ' . $c['attributes'] : '';

        return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"' . $attributes . '>' . $label . '</a>';
    }
}
