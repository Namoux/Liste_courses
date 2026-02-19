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

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../db/connect.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Utilisateur non connecté']);
    exit;
}

$userId = $_SESSION['user_id'];

// Vérifie que les données existent
if (!isset($_POST['nom'], $_POST['quantite'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Nom ou quantité manquante']);
    exit;
}

$nom = trim($_POST['nom']);
$quantite = trim($_POST['quantite']);

// Vérifie que les champs ne sont pas vides
if ($nom === '' || $quantite === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Nom ou quantité vide']);
    exit;
}

// Insertion sécurisée avec PDO
$stmt = $pdo->prepare("INSERT INTO products (nom, quantite, user_id) VALUES (?, ?, ?)");
$stmt->execute([$nom, $quantite, $userId]);

// Retour JSON
echo json_encode([
    'success' => 'Produit ajouté',
    'product' => [
        'id' => $pdo->lastInsertId(),
        'nom' => $nom,
        'quantite' => $quantite
    ]
]);
