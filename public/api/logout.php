<?php
// filepath: /home/namoux/Documents/php/Projets/Liste_courses/public/api/logout.php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

// CORS pour cet endpoint
handleCors(['POST']);

// Méthode HTTP autorisée
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'method_not_allowed'], 405);
}

try {
    // Vide les données de session
    $_SESSION = [];

    // Supprime le cookie de session côté navigateur
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            [
                'expires' => time() - 42000,
                'path' => $params['path'] ?? '/',
                'domain' => $params['domain'] ?? '',
                'secure' => (bool)($params['secure'] ?? false),
                'httponly' => (bool)($params['httponly'] ?? true),
                'samesite' => $params['samesite'] ?? 'Lax',
            ]
        );
    }

    // Détruit la session serveur
    session_destroy();

    jsonResponse(['success' => true], 200);
} catch (Throwable $e) {
    jsonResponse(['error' => 'server_error'], 500);
}