<?php
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido.']);
    exit;
}

$petId = (int)($_GET['mascota_id'] ?? 0);
if (!$petId) {
    echo json_encode([]);
    exit;
}

$stmt = getDB()->prepare('SELECT * FROM vacunas WHERE mascota_id = ? ORDER BY id');
$stmt->execute([$petId]);
echo json_encode($stmt->fetchAll());
