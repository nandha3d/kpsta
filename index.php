<?php

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CodeIgniter 4 front controller
 *---------------------------------------------------------------
 *
 * This sits at the project root rather than in public/, because the document
 * root on the shared host is the project root and the cPanel deployment copies
 * the tree there wholesale. CodeIgniter's preferred layout points the document
 * root at public/ so that app/, writable/ and vendor/ are not reachable over
 * HTTP; where the host allows that, moving the document root is worth doing.
 * Until then .htaccess denies access to those directories directly.
 */

$minPhpVersion = '8.2';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

// Path to the front controller. Here that is the project root, not public/.
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory.
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 */

require FCPATH . 'app/Config/Paths.php';

$paths = new Paths();

require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'Boot.php';

exit(Boot::bootWeb($paths));
