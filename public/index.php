<?php
// Secure router script

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve index.html at root
if ($requestUri === '/' || $requestUri === '') {
    if (file_exists(__DIR__ . '/index.html')) {
        header('Content-Type: text/html; charset=UTF-8');
        readfile(__DIR__ . '/index.html');
        exit;
    }
}

// Security: Remove any directory traversal attempts
$requestUri = str_replace(['../', '..\\', "\0"], '', $requestUri);
$requestUri = ltrim($requestUri, '/');

// If requesting a path without extension, try to serve the .html file
if (!pathinfo($requestUri, PATHINFO_EXTENSION) && $requestUri !== '') {
    $htmlFile = __DIR__ . '/' . $requestUri . '.html';

    // Security: Verify the file is within the public directory
    $realPath = realpath($htmlFile);
    $publicDir = realpath(__DIR__);

    if ($realPath && strpos($realPath, $publicDir) === 0 && file_exists($realPath)) {
        header('Content-Type: text/html; charset=UTF-8');
        readfile($realPath);
        exit;
    }
}

// If requesting a .html file directly, redirect to version without extension
if (preg_match('/\.html$/', $requestUri)) {
    $cleanUri = preg_replace('/\.html$/', '', $requestUri);
    // Security: Only allow relative redirects within the site
    if (strpos($cleanUri, '/') === 0 || strpos($cleanUri, 'http') === false) {
        header("Location: /$cleanUri", true, 301);
        exit;
    }
}

// Otherwise, let PHP serve the file normally
return false;
