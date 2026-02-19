<?php

$forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
$serverPort = $_SERVER['SERVER_PORT'] ?? '';
$secureCookie = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    $forwardedProto === 'https' ||
    (string) $serverPort === '443'
);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('LISTECOURSESESSID');

    $sessionDir = __DIR__ . '/../tmp/sessions';
    if (!is_dir($sessionDir)) {
        @mkdir($sessionDir, 0700, true);
    }
    if (is_dir($sessionDir) && is_writable($sessionDir)) {
        session_save_path($sessionDir);
    }

    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_secure', $secureCookie ? '1' : '0');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secureCookie,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}
