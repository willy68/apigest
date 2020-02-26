<?php
// Copyright Monwoo 2017, service@monwoo.com
// Enabling CORS in bultin dev to test locally with multiples servers
// used to replace lack of .htaccess support inside php builting webserver.
// call with :
// php -S localhost:8000 -t public public/server.php
$CORS_ORIGIN_ALLOWED = "http://localhost:4200";    

function consoleLog($level, $msg) {
    file_put_contents("php://stdout", "[" . $level . "] " . $msg . "\n");
}

function applyCorsHeaders() {
    global $CORS_ORIGIN_ALLOWED;
    header("Access-Control-Allow-Origin: {$CORS_ORIGIN_ALLOWED}");
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Accept, X-requested-with, Authorization, Client-security-token, User-agent');
}

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/web'.$uri)) {
    consoleLog('info', "Transparent routing for : " . __DIR__.'/web'.$uri);
    return false;
}

applyCorsHeaders();
require_once __DIR__.'/web/frontend.php';

