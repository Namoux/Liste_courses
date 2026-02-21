<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

// CORS pour cet endpoint
handleCors(['POST']);

// Méthode HTTP autorisée
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'method_not_allowed'], 405);
}

try {
    // Vérifie la session et récupère l'id user
    $userId = requireAuthUserId();

    // Délègue la suppression au controller/service/repository
    $result = productController()->deleteAllProducts($userId);

    // Réponse succès
    jsonResponse($result, 200);
} catch (InvalidArgumentException $e) {
    jsonResponse(['error' => $e->getMessage()], 422);
} catch (Throwable $e) {
    jsonResponse(['error' => 'server_error'], 500);
}