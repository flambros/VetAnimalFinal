<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?? [];

function user_public(array $u): array {
    unset($u['password']);
    return $u;
}

if ($method === 'POST' && $action === 'login') {
    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';

    $stmt = getDB()->prepare('SELECT * FROM users WHERE LOWER(email) = LOWER(?)');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Email o contraseña incorrectos.']);
        exit;
    }

    $_SESSION['user'] = user_public($user);
    echo json_encode(['user' => user_public($user)]);
    exit;
}

if ($method === 'POST' && $action === 'register') {
    $nombre = trim($input['nombre'] ?? '');
    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';
    $telefono = trim($input['telefono'] ?? '');

    if (!$nombre || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Completá todos los campos obligatorios.']);
        exit;
    }

    $db = getDB();
    $check = $db->prepare('SELECT id FROM users WHERE LOWER(email) = LOWER(?)');
    $check->execute([$email]);
    if ($check->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'Ya existe una cuenta registrada con ese email.']);
        exit;
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare('INSERT INTO users (nombre, email, password, rol, telefono) VALUES (?, ?, ?, "cliente", ?)');
    $stmt->execute([$nombre, $email, $hash, $telefono]);
    $newId = (int)$db->lastInsertId();

    $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$newId]);
    $user = $stmt->fetch();

    $_SESSION['user'] = user_public($user);
    echo json_encode(['user' => user_public($user)]);
    exit;
}

if ($method === 'POST' && $action === 'forgot-password') {
    // Simulado: en un sistema real acá se enviaría un email con un token.
    echo json_encode(['message' => 'Si el correo existe en nuestro sistema, te enviamos un enlace de recuperación.']);
    exit;
}

if ($method === 'POST' && $action === 'logout') {
    unset($_SESSION['user']);
    echo json_encode(['message' => 'Sesión cerrada.']);
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Acción no encontrada.']);
