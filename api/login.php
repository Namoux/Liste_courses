<?php

// Autoriser toutes les origines (dev/test)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Préflight (OPTIONS) pour fetch()
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    exit;
}

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../db/connect.php';

if (!isset($_POST['username'], $_POST['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

// Récupère l’utilisateur
try {
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        session_write_close();
        echo json_encode(['success' => 'Connexion réussie']);
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(['error' => 'Nom d’utilisateur ou mot de passe incorrect']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur lors de la connexion']);
}
