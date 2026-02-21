<?php

declare(strict_types=1);

$forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
$serverPort = $_SERVER['SERVER_PORT'] ?? '';

$secureCookie = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    $forwardedProto === 'https' ||
    (string)$serverPort === '443'
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
        'samesite' => 'Lax',
    ]);

    session_start();
}

require_once __DIR__ . '/../../db/connect.php';
require_once __DIR__ . '/../../app/Repositories/ProductRepository.php';
require_once __DIR__ . '/../../app/Services/ProductService.php';
require_once __DIR__ . '/../../app/Controllers/ProductController.php';

/**
 * Gère les headers CORS pour un endpoint.
 * Important: si credentials=true côté front, il faut une origin explicite (pas '*').
 */
function handleCors(array $methods): void
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowedOrigin = 'http://localhost:8000'; // adapte à ton front

    if ($origin === $allowedOrigin) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
        header('Vary: Origin');
    }

    header('Access-Control-Allow-Methods: ' . implode(', ', array_unique([...$methods, 'OPTIONS'])));
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/**
 * Réponse JSON standard + fin du script.
 */
function jsonResponse(mixed $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Vérifie la session utilisateur.
 * Retourne user_id si connecté, sinon 401.
 */
function requireAuthUserId(): int
{
    if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_id'] <= 0) {
        jsonResponse(['error' => 'unauthorized'], 401);
    }

    return (int)$_SESSION['user_id'];
}

/**
 * Fabrique le ProductController avec ses dépendances.
 */
function productController(): ProductController
{
    $pdo = getPdo();
    $repo = new ProductRepository($pdo);
    $service = new ProductService($repo);

    return new ProductController($service);
}