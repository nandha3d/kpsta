<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('display_date')) {

    /**
     * Format a stored date for display, or return $fallback when there isn't a
     * usable one.
     *
     * Rows imported into this database can carry MySQL's zero date
     * ('0000-00-00 00:00:00'). strtotime() maps that to a negative year, so
     * date() rendered it as "Nov 30, -0001" on the public pages. Anything that
     * isn't a real date is treated as absent instead.
     */
    function display_date($value, $format = 'M d, Y', $fallback = '') {
        if (empty($value)) {
            return $fallback;
        }

        // Zero dates, in the usual variants
        if (strpos((string) $value, '0000-00-00') === 0) {
            return $fallback;
        }

        $timestamp = strtotime($value);
        if ($timestamp === FALSE || $timestamp <= 0) {
            return $fallback;
        }

        return date($format, $timestamp);
    }
}
