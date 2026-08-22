<?php
require_once __DIR__ . '/includes/auth.php';
$currentPath = '/carrito';
$pageTitle = 'Carrito de Compras';
$extraScripts = ['assets/js/carrito.js'];
require __DIR__ . '/includes/page-start.php';
require __DIR__ . '/includes/header.php';
$__user = current_user();
?>
<script>
  window.CURRENT_USER_ID = <?= $__user ? (int)$__user['id'] : 'null' ?>;
  window.IS_LOGGED_IN = <?= $__user ? 'true' : 'false' ?>;
</script>

<main class="u-flex-1">
  <div class="container section u-max-w-xl" style="max-width:800px;">

    <div id="view-cart">
      <a href="tienda.php" class="back-link u-mb-6">← Volver a la Tienda</a>
      <h1 class="u-text-2xl u-font-bold u-mb-6">Carrito de Compras</h1>

      <div id="checkout-error" class="alert alert-error u-mb-4" style="display:none;"></div>

      <div id="empty-cart" class="empty-state" style="display:none;">
        <h2>Tu carrito está vacío</h2>
        <p class="u-mt-2 u-mb-6" style="color:#6b7280;">Explorá nuestra tienda para agregar medicamentos, alimentos o productos de belleza.</p>
        <a href="tienda.php" class="btn btn-primary">Ir a la Tienda</a>
      </div>

      <div id="cart-box" class="u-card u-shadow" style="display:none;">
        <div id="cart-items" class="u-divider"></div>

        <div class="u-flex u-justify-between u-items-center u-mt-6 u-border-t" style="padding-top:24px;">
          <span class="u-text-lg u-font-bold">Total:</span>
          <span class="u-text-2xl u-font-bold" style="color:var(--verde-oscuro);" id="cart-total">$0.00</span>
        </div>

        <div class="u-flex u-justify-end u-gap-3 u-mt-8">
          <button type="button" id="btn-clear-cart" class="btn btn-light">Vaciar Carrito</button>
          <button type="button" id="btn-checkout" class="btn btn-primary">Finalizar Compra ✓</button>
        </div>
      </div>
    </div>

    <div id="view-success" style="display:none; text-align:center; max-width:560px; margin:0 auto;">
      <div style="font-size:3.4rem;">🎉</div>
      <h1 class="u-text-3xl u-font-bold" style="margin:16px 0;">¡Compra realizada con éxito!</h1>
      <p class="u-muted u-mb-8">Tu pedido está siendo procesado y te enviaremos la confirmación por correo electrónico.</p>
      <a href="tienda.php" class="btn btn-primary">Volver a la Tienda</a>
    </div>

  </div>
</main>

<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/page-end.php';
?>
