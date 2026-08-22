<?php
/**
 * Header incluido en todas las páginas públicas (no auth, no admin).
 * Requiere que la página haya definido $currentPath (ej: '/', '/booking').
 */
require_once __DIR__ . '/auth.php';
$user = current_user();

// contador de carrito: lo resuelve JS leyendo localStorage (vet_cart),
// acá solo dejamos un <span> vacío que main.js completa al cargar.
?>
<header class="site-header">
  <a href="index.php" class="logo">Veterinaria VetAnimal</a>
  <nav class="main-nav">
    <a href="index.php" class="<?= $currentPath === '/' ? 'active' : '' ?>">Inicio</a>
    <a href="booking.php" class="<?= $currentPath === '/booking' ? 'active' : '' ?>">Turnos</a>
    <a href="historial.php" class="<?= $currentPath === '/historial' ? 'active' : '' ?>">Diagnósticos</a>
    <a href="tienda.php" class="<?= $currentPath === '/tienda' ? 'active' : '' ?>">Tienda</a>
    <a href="carrito.php" id="cart-nav-link" class="<?= $currentPath === '/carrito' ? 'active' : '' ?>" style="display:none;">
      🛒 Carrito <span id="cart-count-badge" style="background:#2f4b3c;color:#fff;border-radius:999px;font-size:.72rem;padding:2px 8px;margin-left:4px;">0</span>
    </a>
  </nav>
  <div class="header-actions">
    <?php if ($user): ?>
      <?php if ($user['rol'] === 'veterinario'): ?>
        <a href="admin/dashboard.php" class="btn btn-outline btn-sm">Panel Veterinario</a>
      <?php endif; ?>
      <a href="logout.php" class="btn btn-light btn-sm">Salir</a>
      <a href="perfil.php" class="avatar-btn" title="<?= htmlspecialchars($user['nombre']) ?>">
        <?= htmlspecialchars(mb_strtoupper(mb_substr($user['nombre'], 0, 1))) ?>
      </a>
    <?php else: ?>
      <a href="login.php" class="btn btn-light btn-sm">Ingresar</a>
    <?php endif; ?>
    <a href="tel:911" class="btn btn-danger btn-sm">✱ Llamada de Emergencia</a>
  </div>
</header>
