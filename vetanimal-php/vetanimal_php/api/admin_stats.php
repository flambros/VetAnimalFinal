<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');
require_vet_api();

$db = getDB();
$hoy = date('Y-m-d');

$stmt = $db->prepare("SELECT COUNT(*) c FROM turnos WHERE fecha = ? AND estado != 'cancelado'");
$stmt->execute([$hoy]);
$totalHoy = (int)$stmt->fetch()['c'];

$pendientes = (int)$db->query("SELECT COUNT(*) c FROM turnos WHERE estado = 'pendiente'")->fetch()['c'];
$totalPacientes = (int)$db->query('SELECT COUNT(*) c FROM pets')->fetch()['c'];
$totalClientes = (int)$db->query("SELECT COUNT(*) c FROM users WHERE rol = 'cliente'")->fetch()['c'];

echo json_encode([
    'totalHoy' => $totalHoy,
    'pendientes' => $pendientes,
    'totalPacientes' => $totalPacientes,
    'totalClientes' => $totalClientes,
]);
