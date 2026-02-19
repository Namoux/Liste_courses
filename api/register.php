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
require_once __DIR__ . '/../db/connect.php'; // Inclut la connexion PDO

// Vérifie que les données existent
if (!isset($_POST['username'], $_POST['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

// Vérifie que username et password ne sont pas vides
if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Nom d’utilisateur ou mot de passe vide']);
    exit;
}

try {
    // Vérifie si l’utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        http_response_code(409); // Conflict
        echo json_encode(['error' => 'Nom d’utilisateur déjà pris']);
        exit;
    }

    // Hash du mot de passe
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Compatibilité schéma: users avec ou sans colonne email
    $emailColumn = $pdo->query("SHOW COLUMNS FROM users LIKE 'email'");
    $hasEmail = (bool) $emailColumn->fetch();

    if ($hasEmail) {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';

        if ($email === '') {
            $email = $username . '+' . uniqid() . '@local.invalid';
        }

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hash]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
    }

    echo json_encode(['success' => 'Utilisateur créé']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur lors de l’inscription']);
}