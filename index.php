<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

if (PHP_SAPI === 'cli-server') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    // Let the built-in PHP server serve existing public files directly.
    if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
        return false;
    }
}

require_once __DIR__.'/public/index.php';

