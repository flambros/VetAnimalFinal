<?php
/**
 * Sidebar del panel veterinario. Requiere $activeAdminTab: 'dashboard'|'turnos'|'pacientes'|''
 * y que la página ya haya llamado require_vet_page().
 */
$__user = current_user();
?>
<div class="admin-sidebar">
  <a href="../index.php" class="logo">Veterinaria VetAnimal</a>
  <nav class="admin-nav">
    <a href="dashboard.php" class="<?= $activeAdminTab === 'dashboard' ? 'active' : '' ?>">📊 Panel Principal</a>
    <a href="turnos.php" class="<?= $activeAdminTab === 'turnos' ? 'active' : '' ?>">📅 Gestión de Turnos</a>
    <a href="pacientes.php" class="<?= $activeAdminTab === 'pacientes' ? 'active' : '' ?>">🐾 Pacientes / Clientes</a>
    <a href="../historial.php">📋 Historiales Clínicos</a>
  </nav>
  <div class="u-mt-8 u-border-t" style="padding-top:24px; border-top:1px solid rgba(255,255,255,.2); margin-top:auto;">
    <p class="u-text-xs" style="color:rgba(255,255,255,.7);">Sesión iniciada como:</p>
    <p class="u-font-semibold u-text-sm"><?= htmlspecialchars($__user['nombre']) ?></p>
    <a href="../index.php" class="btn btn-outline btn-sm u-mt-4 u-w-full" style="color:#fff; border-color:#fff; text-align:center;">← Ir a la web pública</a>
  </div>
</div>
