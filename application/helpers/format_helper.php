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

if (!function_exists('posted_filename')) {

    /**
     * Read a client-supplied file name from POST, safely.
     *
     * Two problems this solves:
     *
     *  1. The callers read $_POST['pdfName'] directly, so a request without
     *     that field raised "Undefined array key" (a notice on PHP 7, a
     *     warning on PHP 8).
     *  2. The value was concatenated straight onto an upload directory and
     *     handed to unlink(), so a name like "../../config/database.php"
     *     escaped the intended directory. basename() strips any path, and
     *     leading dots are removed so the result cannot be a traversal
     *     fragment or a dotfile.
     *
     * @param  string $key POST field to read.
     * @return string      A bare file name, or '' when absent/unusable.
     */
    function posted_filename($key) {
        if (!isset($_POST[$key]) OR !is_string($_POST[$key])) {
            return '';
        }

        $name = basename(trim($_POST[$key]));
        $name = ltrim($name, '.');

        // Reject anything that still carries a separator or a null byte.
        if ($name === '' OR strpbrk($name, "/\\\0") !== FALSE) {
            return '';
        }

        return $name;
    }

}
