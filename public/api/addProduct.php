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

    // Délègue la logique au controller/service/repository
    $result = productController()->addProduct($userId, $_POST);

    // Réponse succès
    jsonResponse($result, 201);
} catch (InvalidArgumentException $e) {
    // Erreur de validation métier (nom/quantité)
    jsonResponse(['error' => $e->getMessage()], 422);
} catch (Throwable $e) {
    // Erreur interne
    jsonResponse(['error' => 'server_error'], 500);
}