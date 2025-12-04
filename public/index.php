<?php
// Secure router script with whitelist

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Whitelist of allowed HTML files (without extension)
$allowedPages = ['index', 'menu', 'menu1', 'menu2'];

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

// If requesting a .html file directly, redirect to version without extension
if (preg_match('/\.html$/', $requestUri)) {
    $cleanUri = preg_replace('/\.html$/', '', $requestUri);

    // Check if the requested page is in whitelist
    if (in_array($cleanUri, $allowedPages)) {
        header("Location: /$cleanUri", true, 301);
        exit;
    } else {
        echo "EXIT"; exit;
        // Not in whitelist - return 404
        http_response_code(404);
        if (file_exists(__DIR__ . '/404.html')) {
            readfile(__DIR__ . '/404.html');
        } else {
            echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>404 - Page Not Found</h1></body></html>';
        }
        exit;
    }
}

// If requesting a path without extension, try to serve the .html file
if (!pathinfo($requestUri, PATHINFO_EXTENSION) && $requestUri !== '') {
    // Check if the requested page is in whitelist
    if (!in_array($requestUri, $allowedPages)) {
        http_response_code(404);
        if (file_exists(__DIR__ . '/404.html')) {
            readfile(__DIR__ . '/404.html');
        } else {
            echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>404 - Page Not Found</h1></body></html>';
        }
        exit;
    }

    $htmlFile = __DIR__ . '/' . $requestUri . '.html';

    // Security: Verify the file is within the public directory
    $realPath = realpath($htmlFile);
    $publicDir = realpath(__DIR__);

    if ($realPath && strpos($realPath, $publicDir) === 0 && file_exists($realPath)) {
        header('Content-Type: text/html; charset=UTF-8');
        readfile($realPath);
        exit;
    } else {
        http_response_code(404);
        if (file_exists(__DIR__ . '/404.html')) {
            readfile(__DIR__ . '/404.html');
        } else {
            echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>404 - Page Not Found</h1></body></html>';
        }
        exit;
    }
}

// Otherwise, let PHP serve the file normally (for static assets like CSS, JS, images)
return false;
