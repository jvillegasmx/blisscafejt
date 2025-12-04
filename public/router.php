<?php
// Router script for PHP built-in server to mimic .htaccess behavior

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If requesting a path without extension, try to serve the .html file
if (!pathinfo($requestUri, PATHINFO_EXTENSION)) {
    $htmlFile = __DIR__ . $requestUri . '.html';
    if (file_exists($htmlFile)) {
        header('Content-Type: text/html; charset=UTF-8');
        readfile($htmlFile);
        exit;
    }
}

// If requesting a .html file directly, redirect to version without extension
if (preg_match('/\.html$/', $requestUri)) {
    $cleanUri = preg_replace('/\.html$/', '', $requestUri);
    header("Location: $cleanUri", true, 301);
    exit;
}

// Otherwise, let PHP serve the file normally
return false;
