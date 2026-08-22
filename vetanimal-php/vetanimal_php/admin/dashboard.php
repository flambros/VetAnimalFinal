<?php
require_once __DIR__ . '/../includes/auth.php';
require_vet_page();
$assetBase = '../';
$pageTitle = 'Panel Veterinario';
$extraScripts = ['assets/js/admin-dashboard.js'];
require __DIR__ . '/../includes/page-start.php';
$__user = current_user();
$hoyStr = date('Y-m-d');
$activeAdminTab = 'dashboard';
?>

<div class="admin-shell">
  <?php require __DIR__ . '/_sidebar.php'; ?>

  <div class="admin-main">
    <div class="admin-topbar">
      <div>
        <h1 class="u-text-2xl u-font-bold">Panel Veterinario</h1>
        <p class="u-text-sm" style="color:#4b5563;">Bienvenido/a, Dr/a. <?= htmlspecialchars($__user['nombre']) ?>. Hoy es <?= $hoyStr ?>.</p>
      </div>
      <a href="../booking.php" class="btn btn-primary btn-sm">+ Agendar Turno</a>
    </div>

    <div class="stat-grid">
      <div class="stat-card"><div class="num" id="stat-hoy">0</div><div class="lbl">Turnos para hoy</div></div>
      <div class="stat-card"><div class="num" id="stat-pendientes">0</div><div class="lbl">Pendientes de atención</div></div>
      <div class="stat-card"><div class="num" id="stat-pacientes">0</div><div class="lbl">Pacientes en sistema</div></div>
      <div class="stat-card"><div class="num" id="stat-clientes">0</div><div class="lbl">Clientes registrados</div></div>
    </div>

    <div class="panel-card">
      <h2>Agenda del Día (<span id="today-count">0</span>)</h2>
      <div id="today-empty" class="u-text-center u-py-6" style="color:#6b7280; display:none;">No hay turnos agendados para la fecha de hoy.</div>
      <div id="today-wrap" style="overflow-x:auto; display:none;">
        <table class="data-table">
          <thead><tr><th>Hora</th><th>Mascota</th><th>Dueño</th><th>Servicio</th><th>Estado</th><th>Acciones</th></tr></thead>
          <tbody id="today-body"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/page-end.php'; ?>
