
<?php

// Autoriser toutes les origines (pour dev/test)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Si tu veux accepter les méthodes OPTIONS (préflight) pour fetch()
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    exit;
}

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . "/../db/connect.php"; // connexion PDO

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Utilisateur non connecté"]);
    exit;
}

$userId = $_SESSION['user_id'];

// Récupère les données envoyées
$id = $_POST['id'] ?? null;
$checked = $_POST['checked'] ?? null;

if ($id === null || $checked === null) {
    http_response_code(400);
    echo json_encode(["error" => "Paramètres manquants"]);
    exit;
}

// Force la valeur à 0 ou 1
$checked = filter_var($checked, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
$checked = $checked ? 1 : 0;

// Met à jour le produit en s'assurant que l'utilisateur ne peut modifier que ses propres produits
$stmt = $pdo->prepare("UPDATE products SET checked = ? WHERE id = ? AND user_id = ?");
$stmt->execute([$checked, $id, $userId]);

echo json_encode(["success" => true, "id" => $id, "checked" => $checked]);
