<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

// CORS pour cet endpoint
handleCors(['GET']);

// Réponse immédiate pour la requête preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/bootstrap.php';

// Sécurise la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => 'method_not_allowed'], 405);
}

try {
    // Vérifie la session et récupère l'id utilisateur
    $userId = requireAuthUserId();

    // Délègue la récupération au controller (MVC)
    $products = productController()->getProducts($userId);

    // Retourne les données en JSON
    jsonResponse($products, 200);
} catch (Throwable $e) {
    // Évite d'exposer les détails internes en production
    jsonResponse(['error' => 'server_error'], 500);
}