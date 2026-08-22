<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');
$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

function consulta_with_vet(PDO $db, array $c): array {
    $vetNombre = 'Dr. Veterinaria VetAnimal';
    if ($c['veterinario_id']) {
        $stmt = $db->prepare('SELECT nombre FROM users WHERE id = ?');
        $stmt->execute([$c['veterinario_id']]);
        $vet = $stmt->fetch();
        if ($vet) $vetNombre = $vet['nombre'];
    }
    $c['vet_nombre'] = $vetNombre;
    return $c;
}

if ($method === 'GET') {
    $petId = (int)($_GET['mascota_id'] ?? 0);
    if (!$petId) {
        echo json_encode([]);
        exit;
    }
    $stmt = $db->prepare('SELECT * FROM consultas WHERE mascota_id = ? ORDER BY fecha DESC');
    $stmt->execute([$petId]);
    echo json_encode(array_map(fn($c) => consulta_with_vet($db, $c), $stmt->fetchAll()));
    exit;
}

if ($method === 'POST') {
    require_vet_api();
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    $mascota_id = (int)($input['mascota_id'] ?? 0);
    $veterinario_id = (int)($input['veterinario_id'] ?? current_user()['id']);
    $fecha = $input['fecha'] ?? date('Y-m-d');
    $tipo = $input['tipo'] ?? 'CONTROL';
    $titulo = trim($input['titulo'] ?? '');
    $descripcion = trim($input['descripcion'] ?? '');

    if (!$mascota_id || !$titulo || !$descripcion) {
        http_response_code(400);
        echo json_encode(['error' => 'Completá el título y la descripción.']);
        exit;
    }

    $stmt = $db->prepare('INSERT INTO consultas (mascota_id, veterinario_id, fecha, tipo, titulo, descripcion) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$mascota_id, $veterinario_id, $fecha, $tipo, $titulo, $descripcion]);
    $newId = (int)$db->lastInsertId();

    $stmt = $db->prepare('SELECT * FROM consultas WHERE id = ?');
    $stmt->execute([$newId]);
    echo json_encode(consulta_with_vet($db, $stmt->fetch()));
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Método no permitido.']);
