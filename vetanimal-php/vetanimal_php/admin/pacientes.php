<?php
require_once __DIR__ . '/../includes/auth.php';
require_vet_page();
$assetBase = '../';
$pageTitle = 'Registro de Pacientes';
$extraScripts = ['assets/js/admin-pacientes.js'];
require __DIR__ . '/../includes/page-start.php';
$activeAdminTab = 'pacientes';
?>

<div class="admin-shell">
  <?php require __DIR__ . '/_sidebar.php'; ?>

  <div class="admin-main">
    <div class="admin-topbar">
      <div>
        <h1 class="u-text-2xl u-font-bold">Registro de Pacientes</h1>
        <p class="u-text-sm" style="color:#4b5563;">Listado completo de animales atendidos y sus dueños.</p>
      </div>
    </div>

    <div class="panel-card">
      <div class="u-mb-6" style="max-width:420px;">
        <div class="search-bar">
          <span>🔍</span>
          <input type="text" id="search-input" placeholder="Buscar por paciente, especie o dueño...">
        </div>
      </div>

      <div style="overflow-x:auto;">
        <table class="data-table">
          <thead>
            <tr><th>Paciente</th><th>Especie / Raza</th><th>Edad &amp; Peso</th><th>Dueño / Contacto</th><th>Estado Salud</th><th>Acciones</th></tr>
          </thead>
          <tbody id="pets-body"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/page-end.php'; ?>
