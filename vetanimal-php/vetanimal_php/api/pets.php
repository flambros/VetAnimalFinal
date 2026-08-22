<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

function pet_with_owner(PDO $db, array $pet): array {
    $stmt = $db->prepare('SELECT nombre, telefono FROM users WHERE id = ?');
    $stmt->execute([$pet['usuario_id']]);
    $owner = $stmt->fetch();
    $pet['dueno'] = $owner ? $owner['nombre'] : 'Desconocido';
    $pet['telefono'] = $owner ? $owner['telefono'] : '';
    return $pet;
}

// --- GET /api/pets.php?id=5  -> una mascota puntual
if ($method === 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare('SELECT * FROM pets WHERE id = ?');
    $stmt->execute([$id]);
    $pet = $stmt->fetch();
    if (!$pet) {
        http_response_code(404);
        echo json_encode(['error' => 'Mascota no encontrada']);
        exit;
    }
    echo json_encode(pet_with_owner($db, $pet));
    exit;
}

// --- GET /api/pets.php?userId=3   ó   ?all=true
if ($method === 'GET') {
    $userId = isset($_GET['userId']) ? (int)$_GET['userId'] : null;
    $all = ($_GET['all'] ?? '') === 'true';

    if ($all || !$userId) {
        // Solo veterinarios pueden ver todas las mascotas
        if ($all) {
            require_vet_api();
        }
        $rows = $db->query('SELECT * FROM pets ORDER BY id')->fetchAll();
        $result = array_map(fn($p) => pet_with_owner($db, $p), $rows);
        echo json_encode($result);
        exit;
    }

    $stmt = $db->prepare('SELECT * FROM pets WHERE usuario_id = ? ORDER BY id');
    $stmt->execute([$userId]);
    echo json_encode($stmt->fetchAll());
    exit;
}

// --- POST /api/pets.php  (crear mascota)
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    $usuario_id = (int)($input['usuario_id'] ?? 0);
    $nombre = trim($input['nombre'] ?? '');
    $especie = trim($input['especie'] ?? '');
    $raza = trim($input['raza'] ?? '');
    $edad = ($input['edad'] ?? '') !== '' ? (int)$input['edad'] : null;
    $peso = ($input['peso'] ?? '') !== '' ? (float)$input['peso'] : null;
    $foto = trim($input['foto'] ?? '');

    if (!$nombre || !$especie) {
        http_response_code(400);
        echo json_encode(['error' => 'El nombre y la especie son obligatorios.']);
        exit;
    }

    $defaultFoto = 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=500&q=80';
    if ($especie === 'Gato') {
        $defaultFoto = 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=500&q=80';
    } elseif ($especie === 'Ave') {
        $defaultFoto = 'https://images.unsplash.com/photo-1552728089-57bdde30beb3?w=500&q=80';
    } elseif ($especie === 'Exótico') {
        $defaultFoto = 'https://images.unsplash.com/photo-1425082661705-1834bfd09dca?w=500&q=80';
    }

    $stmt = $db->prepare('INSERT INTO pets (usuario_id, nombre, especie, raza, edad, peso, foto, estado_salud, alergias, condiciones_cronicas)
                           VALUES (?, ?, ?, ?, ?, ?, ?, "Estable", "", "Ninguna diagnosticada a la fecha.")');
    $stmt->execute([$usuario_id, $nombre, $especie, $raza, $edad, $peso, $foto ?: $defaultFoto]);
    $newId = (int)$db->lastInsertId();

    $stmt = $db->prepare('SELECT * FROM pets WHERE id = ?');
    $stmt->execute([$newId]);
    echo json_encode($stmt->fetch());
    exit;
}

// --- PUT /api/pets.php?id=5  (editar mascota)
if ($method === 'PUT' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    $stmt = $db->prepare('SELECT * FROM pets WHERE id = ?');
    $stmt->execute([$id]);
    $pet = $stmt->fetch();
    if (!$pet) {
        http_response_code(404);
        echo json_encode(['error' => 'Mascota no encontrada.']);
        exit;
    }

    $fields = [
        'nombre' => $input['nombre'] ?? $pet['nombre'],
        'especie' => $input['especie'] ?? $pet['especie'],
        'raza' => $input['raza'] ?? $pet['raza'],
        'edad' => ($input['edad'] ?? '') !== '' ? (int)$input['edad'] : null,
        'peso' => ($input['peso'] ?? '') !== '' ? (float)$input['peso'] : null,
        'foto' => $input['foto'] ?? $pet['foto'],
        'alergias' => $input['alergias'] ?? $pet['alergias'],
        'condiciones_cronicas' => $input['condiciones_cronicas'] ?? $pet['condiciones_cronicas'],
        'estado_salud' => $input['estado_salud'] ?? $pet['estado_salud'],
    ];

    $stmt = $db->prepare('UPDATE pets SET nombre=?, especie=?, raza=?, edad=?, peso=?, foto=?, alergias=?, condiciones_cronicas=?, estado_salud=? WHERE id=?');
    $stmt->execute([
        $fields['nombre'], $fields['especie'], $fields['raza'], $fields['edad'], $fields['peso'],
        $fields['foto'], $fields['alergias'], $fields['condiciones_cronicas'], $fields['estado_salud'], $id
    ]);

    $stmt = $db->prepare('SELECT * FROM pets WHERE id = ?');
    $stmt->execute([$id]);
    echo json_encode($stmt->fetch());
    exit;
}

// --- DELETE /api/pets.php?id=5
if ($method === 'DELETE' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare('SELECT id FROM pets WHERE id = ?');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'Mascota no encontrada.']);
        exit;
    }
    $db->prepare('DELETE FROM pets WHERE id = ?')->execute([$id]);
    echo json_encode(['message' => 'Mascota eliminada correctamente.']);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Método no permitido.']);
