<?php
require_once __DIR__ . '/includes/auth.php';
$currentPath = '/tienda';
$pageTitle = 'Tienda';
$extraScripts = ['assets/js/tienda.js'];
require __DIR__ . '/includes/page-start.php';
require __DIR__ . '/includes/header.php';
?>

<main class="u-flex-1">
  <div class="shop-hero">
    <div>
      <h1>Tienda Veterinaria Online</h1>
      <p>Medicamentos de grado profesional, suplementos y dietas especializadas entregadas directamente a tu hogar.</p>
      <div class="search-bar">
        <span>🔍</span>
        <input type="text" id="search-input" placeholder="Buscar medicamentos, alimento o productos...">
      </div>
    </div>
    <img src="https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=600&q=80" alt="Tienda de mascotas">
  </div>

  <div class="category-bar" id="category-bar"></div>

  <div class="product-grid" id="product-grid"></div>

  <div class="u-fab" id="cart-fab" style="display:none;">
    <a href="carrito.php" class="btn btn-primary" style="box-shadow:var(--sombra-md); display:flex; align-items:center; gap:12px; padding:16px 24px; font-size:1rem;">
      <span>🛒 Ver Carrito (<span id="cart-fab-count">0</span>)</span>
    </a>
  </div>
</main>

<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/page-end.php';
?>
