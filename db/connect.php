<?php

//Charger les variables d'environnement
$dotenv = __DIR__ . "/../.env";
if (!file_exists($dotenv)) {
    die(".env file not found");
};

$env = parse_ini_file($dotenv);

// Vérifie que toutes les variables sont présentes
$required = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS'];
foreach ($required as $key) {
    if (!isset($env[$key])) {
        die("Missing $key in .env");
    }
};

//Connexion PDO
try {
    $pdo = new PDO(
        "mysql:host={$env['DB_HOST']};port={$env['DB_PORT']};dbname={$env['DB_NAME']};charset=utf8mb4",
        $env['DB_USER'],
        $env['DB_PASS'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Erreurs en exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Résultats associatifs
            PDO::ATTR_EMULATE_PREPARES => false, // Préparés natifs
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
};