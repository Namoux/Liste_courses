<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

// CORS pour cet endpoint
handleCors(['POST']);

// Méthode HTTP autorisée
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'method_not_allowed'], 405);
}

// Validation minimale des champs
if (!isset($_POST['username'], $_POST['password'])) {
    jsonResponse(['error' => 'missing_fields'], 400);
}

$username = trim((string)$_POST['username']);
$password = (string)$_POST['password'];

if ($username === '' || $password === '') {
    jsonResponse(['error' => 'missing_fields'], 400);
}

try {
    $pdo = getPdo();

    $stmt = $pdo->prepare(
        "SELECT id, password
         FROM users
         WHERE username = :username
         LIMIT 1"
    );
    $stmt->execute(['username' => $username]);

    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        jsonResponse(['error' => 'invalid_credentials'], 401);
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    session_write_close();

    jsonResponse(['success' => true], 200);
} catch (Throwable $e) {
    jsonResponse(['error' => 'server_error'], 500);
}