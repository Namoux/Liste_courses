<?php

// Autoriser toutes les origines (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Si c'est une requête OPTIONS (prévolée pour CORS), on termine ici
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Inclure la connexion PDO
require_once __DIR__ . '/../db/connect.php';
require_once __DIR__ . '/bootstrap.php';

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Non autorisé"]);
    exit;
}

// Récupère l'id du produit depuis POST
$id = $_POST['id'] ?? null;
if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "ID manquant"]);
    exit;
}

// Supprime uniquement si le produit appartient à l'utilisateur
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);

echo json_encode(["success" => true]);
