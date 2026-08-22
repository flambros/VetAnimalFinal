<?php
require_once __DIR__ . '/../includes/auth.php';
require_vet_page();
$assetBase = '../';
$pageTitle = 'Gestión de Turnos';
$extraScripts = ['assets/js/admin-turnos.js'];
require __DIR__ . '/../includes/page-start.php';
$activeAdminTab = 'turnos';
?>

<div class="admin-shell">
  <?php require __DIR__ . '/_sidebar.php'; ?>

  <div class="admin-main">
    <div class="admin-topbar">
      <div>
        <h1 class="u-text-2xl u-font-bold">Gestión Global de Turnos</h1>
        <p class="u-text-sm" style="color:#4b5563;">Visualizá, confirmá o cancelá citas agendadas por clientes.</p>
      </div>
      <a href="../booking.php" class="btn btn-primary btn-sm">+ Agendar Turno</a>
    </div>

    <div class="panel-card">
      <div class="u-flex u-flex-wrap u-gap-4 u-items-center u-mb-6">
        <div class="u-flex u-items-center u-gap-2">
          <label class="u-text-xs u-font-bold" style="text-transform:uppercase; color:#6b7280;">Estado:</label>
          <select id="filter-estado" style="border:1px solid var(--borde); border-radius:8px; padding:8px 12px; background:#fff;">
            <option value="todos">Todos los estados</option>
            <option value="confirmado">Confirmados</option>
            <option value="pendiente">Pendientes</option>
            <option value="completado">Completados</option>
            <option value="cancelado">Cancelados</option>
          </select>
        </div>
        <div class="u-flex u-items-center u-gap-2">
          <label class="u-text-xs u-font-bold" style="text-transform:uppercase; color:#6b7280;">Fecha:</label>
          <input type="date" id="filter-fecha" style="border:1px solid var(--borde); border-radius:8px; padding:8px 12px; background:#fff;">
          <button type="button" id="clear-fecha" class="u-text-xs u-text-red-dark u-font-semibold" style="display:none; background:none; border:none; cursor:pointer;">Limpiar</button>
        </div>
      </div>

      <div style="overflow-x:auto;">
        <table class="data-table">
          <thead><tr><th>ID</th><th>Fecha y Hora</th><th>Mascota</th><th>Dueño</th><th>Servicio</th><th>Estado</th><th>Acciones</th></tr></thead>
          <tbody id="turnos-body"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/page-end.php'; ?>
