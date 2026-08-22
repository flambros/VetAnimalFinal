<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$db = getDB();

function turno_enriched(PDO $db, array $t): array {
    $stmt = $db->prepare('SELECT nombre, usuario_id FROM pets WHERE id = ?');
    $stmt->execute([$t['mascota_id']]);
    $pet = $stmt->fetch();

    $stmt = $db->prepare('SELECT nombre FROM services WHERE id = ?');
    $stmt->execute([$t['servicio_id']]);
    $service = $stmt->fetch();

    $vetNombre = 'Asignado en clínica';
    if ($t['veterinario_id']) {
        $stmt = $db->prepare('SELECT nombre FROM users WHERE id = ?');
        $stmt->execute([$t['veterinario_id']]);
        $vet = $stmt->fetch();
        if ($vet) $vetNombre = $vet['nombre'];
    }

    $dueno = 'Cliente';
    if ($pet) {
        $stmt = $db->prepare('SELECT nombre FROM users WHERE id = ?');
        $stmt->execute([$pet['usuario_id']]);
        $owner = $stmt->fetch();
        if ($owner) $dueno = $owner['nombre'];
    }

    $t['mascota_nombre'] = $pet ? $pet['nombre'] : 'Mascota';
    $t['servicio_nombre'] = $service ? $service['nombre'] : 'Servicio';
    $t['veterinario_nombre'] = $vetNombre;
    $t['dueno'] = $dueno;
    return $t;
}

// --- GET .../turnos.php?action=booked-dates&mes=2026-08
if ($method === 'GET' && $action === 'booked-dates') {
    $mes = $_GET['mes'] ?? date('Y-m');
    $stmt = $db->prepare("SELECT DISTINCT DAY(fecha) as d FROM turnos WHERE DATE_FORMAT(fecha, '%Y-%m') = ? AND estado != 'cancelado'");
    $stmt->execute([$mes]);
    $days = array_map(fn($r) => (int)$r['d'], $stmt->fetchAll());
    echo json_encode(array_values($days));
    exit;
}

// --- GET .../turnos.php?action=occupied-times&fecha=2026-08-25
if ($method === 'GET' && $action === 'occupied-times') {
    $fecha = $_GET['fecha'] ?? '';
    if (!$fecha) {
        echo json_encode([]);
        exit;
    }
    $stmt = $db->prepare("SELECT hora FROM turnos WHERE fecha = ? AND estado != 'cancelado'");
    $stmt->execute([$fecha]);
    echo json_encode(array_column($stmt->fetchAll(), 'hora'));
    exit;
}

// --- GET .../turnos.php  (listado, con filtros userId / fecha / all)
if ($method === 'GET') {
    $userId = isset($_GET['userId']) ? (int)$_GET['userId'] : null;
    $fecha = $_GET['fecha'] ?? null;
    $all = ($_GET['all'] ?? '') === 'true';

    $sql = 'SELECT * FROM turnos WHERE 1=1';
    $params = [];

    if ($userId && !$all) {
        $sql .= ' AND mascota_id IN (SELECT id FROM pets WHERE usuario_id = ?)';
        $params[] = $userId;
    }
    if ($fecha) {
        $sql .= ' AND fecha = ?';
        $params[] = $fecha;
    }
    $sql .= ' ORDER BY fecha DESC, hora DESC';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    echo json_encode(array_map(fn($t) => turno_enriched($db, $t), $rows));
    exit;
}

// --- POST .../turnos.php  (crear turno)
if ($method === 'POST') {
    require_login_api();
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    $mascota_id = (int)($input['mascota_id'] ?? 0);
    $servicio_id = (int)($input['servicio_id'] ?? 0);
    $fecha = $input['fecha'] ?? '';
    $hora = $input['hora'] ?? '';
    $notas = trim($input['notas'] ?? '');

    if (!$mascota_id || !$servicio_id || !$fecha || !$hora) {
        http_response_code(400);
        echo json_encode(['error' => 'Completá todos los datos para la reserva.']);
        exit;
    }

    $stmt = $db->prepare("SELECT id FROM turnos WHERE fecha = ? AND hora = ? AND estado != 'cancelado'");
    $stmt->execute([$fecha, $hora]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'Ese horario ya fue reservado por otra persona. Elegí otro horario.']);
        exit;
    }

    $vets = $db->query("SELECT id FROM users WHERE rol = 'veterinario'")->fetchAll();
    $randomVet = $vets ? $vets[array_rand($vets)]['id'] : null;

    $stmt = $db->prepare('INSERT INTO turnos (mascota_id, servicio_id, veterinario_id, fecha, hora, estado, notas) VALUES (?, ?, ?, ?, ?, "confirmado", ?)');
    $stmt->execute([$mascota_id, $servicio_id, $randomVet, $fecha, $hora, $notas]);
    $newId = (int)$db->lastInsertId();

    $stmt = $db->prepare('SELECT * FROM turnos WHERE id = ?');
    $stmt->execute([$newId]);
    echo json_encode(turno_enriched($db, $stmt->fetch()));
    exit;
}

// --- PATCH .../turnos.php?id=5&action=estado  (cambiar estado)
if ($method === 'PATCH' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $estado = $input['estado'] ?? '';

    $stmt = $db->prepare('SELECT * FROM turnos WHERE id = ?');
    $stmt->execute([$id]);
    $turno = $stmt->fetch();
    if (!$turno) {
        http_response_code(404);
        echo json_encode(['error' => 'Turno no encontrado']);
        exit;
    }

    if (in_array($estado, ['pendiente', 'confirmado', 'cancelado', 'completado'], true)) {
        $db->prepare('UPDATE turnos SET estado = ? WHERE id = ?')->execute([$estado, $id]);
        $turno['estado'] = $estado;
    }
    echo json_encode($turno);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Método no permitido.']);
