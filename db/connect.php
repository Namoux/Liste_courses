<?php
/**
 * Retourne une instance PDO unique (singleton simple).
 * - Lit les variables de connexion depuis .env
 * - Vérifie les clés obligatoires
 * - Ouvre la connexion MySQL avec options PDO sécurisées
 */
function getPdo(): PDO
{
    // Conserve la connexion en mémoire pour éviter de recréer PDO à chaque appel
    static $pdo = null;

    // Si déjà créée, on la retourne immédiatement
    if ($pdo !== null) {
        return $pdo;
    }

    // Chemin du fichier .env à la racine du projet
    $dotenv = __DIR__ . '/../.env';

    // Stoppe avec exception si le fichier n'existe pas
    if (!file_exists($dotenv)) {
        throw new RuntimeException('.env file not found');
    }

    // Parse le .env (format clé=valeur)
    $env = parse_ini_file($dotenv, false, INI_SCANNER_RAW);

    // Vérifie que le parsing a bien fonctionné
    if (!is_array($env)) {
        throw new RuntimeException('Unable to parse .env');
    }

    // Variables minimales requises pour se connecter à MySQL
    $required = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS'];

    // Vérifie la présence de chaque variable obligatoire
    foreach ($required as $key) {
        if (!array_key_exists($key, $env)) {
            throw new RuntimeException("Missing {$key} in .env");
        }
    }

    // Construction du DSN MySQL
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $env['DB_HOST'],
        $env['DB_PORT'],
        $env['DB_NAME']
    );

    // Création de la connexion PDO
    $pdo = new PDO(
        $dsn,
        $env['DB_USER'],
        $env['DB_PASS'],
        [
            // Lève des exceptions en cas d'erreur SQL
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            // Retourne les résultats en tableaux associatifs
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Utilise les requêtes préparées natives de MySQL (plus sûr)
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    // Retourne l'instance PDO prête à l'emploi
    return $pdo;
}