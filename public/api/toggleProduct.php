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

    // Délègue la mise à jour au controller/service/repository
    $result = productController()->toggleProduct($userId, $_POST);

    // Réponse succès
    jsonResponse($result, 200);
} catch (InvalidArgumentException $e) {
    // Paramètres invalides (id/checked)
    jsonResponse(['error' => $e->getMessage()], 422);
} catch (RuntimeException $e) {
    // Produit introuvable ou non autorisé
    jsonResponse(['error' => $e->getMessage()], 404);
} catch (Throwable $e) {
    // Erreur interne
    jsonResponse(['error' => 'server_error'], 500);
}