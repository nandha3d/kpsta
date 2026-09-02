<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('file_type_kind')) {

    /**
     * Reduce a download's path to the badge kind shown on the listing rows.
     * Uploads carry a real filename; link rows have to be read out of the URL
     * path, and plenty of those end in a script or a query rather than an
     * extension, so anything unrecognised falls back to 'link'.
     */
    function file_type_kind($path, $uploadType = 'file') {
        if (empty($path)) {
            return 'link';
        }

        if ($uploadType === 'url') {
            $urlPath = parse_url($path, PHP_URL_PATH);
            $path = ($urlPath === false || $urlPath === NULL) ? '' : $urlPath;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $map = array(
            'pdf' => 'pdf',
            'doc' => 'doc', 'docx' => 'doc', 'rtf' => 'doc', 'odt' => 'doc',
            'xls' => 'xls', 'xlsx' => 'xls', 'csv' => 'xls', 'ods' => 'xls',
            'ppt' => 'ppt', 'pptx' => 'ppt',
            'zip' => 'zip', 'rar' => 'zip', '7z' => 'zip',
            'jpg' => 'img', 'jpeg' => 'img', 'png' => 'img', 'gif' => 'img', 'webp' => 'img',
            'ttf' => 'font', 'otf' => 'font',
        );

        return isset($map[$ext]) ? $map[$ext] : 'link';
    }
}

if (!function_exists('file_type_badge')) {

    /**
     * The red document mark from the reference, coloured per file type.
     */
    function file_type_badge($path, $uploadType = 'file') {
        $kind = file_type_kind($path, $uploadType);
        $labels = array(
            'pdf'  => 'PDF',
            'doc'  => 'DOC',
            'xls'  => 'XLS',
            'ppt'  => 'PPT',
            'zip'  => 'ZIP',
            'img'  => 'IMG',
            'font' => 'TTF',
            'link' => 'WEB',
        );

        return '<span class="pdf-icon-badge pdf-icon-badge--' . $kind . '" aria-hidden="true">'
             . $labels[$kind]
             . '</span>';
    }
}
