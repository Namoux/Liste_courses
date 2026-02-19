<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../db/connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Utilisateur non connecté"]);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM products WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);

echo json_encode(["success" => true]);
