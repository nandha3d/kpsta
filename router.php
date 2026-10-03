<?php

// router.php for PHP built-in web server (local development)
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri !== '/') {
    // 1. Direct file in project root (e.g. /uploads/..., /images/..., /css/..., /js/...)
    $file = __DIR__ . $uri;
    if (file_exists($file) && is_file($file)) {
        return false; // Let PHP built-in server handle the file natively
    }

    // 2. Direct file in public/
    $publicFile = __DIR__ . '/public' . $uri;
    if (file_exists($publicFile) && is_file($publicFile)) {
        return false;
    }

    // 3. If an upload/media file is requested but missing on disk, return 404 immediately
    // so we don't boot the entire CodeIgniter framework just to say not found.
    if (str_starts_with($uri, '/uploads/')) {
        header("HTTP/1.1 404 Not Found");
        header("Content-Type: text/plain");
        echo "404 Not Found";
        exit;
    }
}

// 4. All other dynamic / API routes go through CodeIgniter front controller
require __DIR__ . '/index.php';
