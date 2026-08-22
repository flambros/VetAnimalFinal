<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');
$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

if ($method === 'GET') {
    $userId = (int)($_GET['userId'] ?? 0);
    if (!$userId) {
        echo json_encode([]);
        exit;
    }
    $stmt = $db->prepare('SELECT * FROM orders WHERE usuario_id = ? ORDER BY id DESC');
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll();

    foreach ($orders as &$order) {
        $stmt2 = $db->prepare('SELECT oi.*, p.nombre AS producto_nombre FROM order_items oi
                                LEFT JOIN products p ON p.id = oi.producto_id
                                WHERE oi.pedido_id = ?');
        $stmt2->execute([$order['id']]);
        $order['items'] = $stmt2->fetchAll();
    }
    unset($order);

    echo json_encode($orders);
    exit;
}

if ($method === 'POST') {
    require_login_api();
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    $usuario_id = (int)($input['usuario_id'] ?? 0);
    $items = $input['items'] ?? [];

    if (!$usuario_id || !is_array($items) || count($items) === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'El carrito está vacío.']);
        exit;
    }

    $db->beginTransaction();
    try {
        $total = 0.0;
        $orderItems = [];

        foreach ($items as $it) {
            $productId = (int)($it['productId'] ?? 0);
            $quantity = (int)($it['quantity'] ?? 1);

            $stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
            $stmt->execute([$productId]);
            $prod = $stmt->fetch();
            $price = $prod ? (float)$prod['precio'] : 0;
            $subtotal = $price * $quantity;
            $total += $subtotal;

            $orderItems[] = [
                'producto_id' => $productId,
                'cantidad' => $quantity,
                'precio_unitario' => $price,
                'producto_nombre' => $prod ? $prod['nombre'] : 'Producto',
            ];
        }

        $stmt = $db->prepare('INSERT INTO orders (usuario_id, total, estado) VALUES (?, ?, "pagado")');
        $stmt->execute([$usuario_id, $total]);
        $orderId = (int)$db->lastInsertId();

        $stmtItem = $db->prepare('INSERT INTO order_items (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)');
        foreach ($orderItems as $oi) {
            $stmtItem->execute([$orderId, $oi['producto_id'], $oi['cantidad'], $oi['precio_unitario']]);
        }

        $db->commit();

        $stmt = $db->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        $order['items'] = $orderItems;

        echo json_encode($order);
    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Error al procesar la compra.']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Método no permitido.']);
