<?php
// filepath: /home/namoux/Documents/php/Projets/Liste_courses/public/api/register.php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

// CORS pour cet endpoint
handleCors(['POST']);

// Méthode HTTP autorisée
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'method_not_allowed'], 405);
}

// Validation minimale des champs
$username = trim((string)($_POST['username'] ?? ''));
$password = (string)($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    jsonResponse(['error' => 'missing_fields'], 400);
}

try {
    $pdo = getPdo();

    // Vérifie si le username existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);

    if ($stmt->fetch()) {
        jsonResponse(['error' => 'username_taken'], 409);
    }

    // Hash du mot de passe
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Compatibilité schéma: users avec ou sans colonne email
    $emailColumn = $pdo->query("SHOW COLUMNS FROM users LIKE 'email'");
    $hasEmail = (bool)$emailColumn->fetch();

    if ($hasEmail) {
        $email = trim((string)($_POST['email'] ?? ''));
        if ($email === '') {
            $email = $username . '+' . uniqid('', true) . '@local.invalid';
        }

        $stmt = $pdo->prepare(
            "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)"
        );
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hash
        ]);
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO users (username, password) VALUES (:username, :password)"
        );
        $stmt->execute([
            'username' => $username,
            'password' => $hash
        ]);
    }

    jsonResponse(['success' => true], 201);
} catch (Throwable $e) {
    jsonResponse(['error' => 'server_error'], 500);
}